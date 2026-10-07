<?php
/**
 * Skript pro zpracování fotek po migraci
 * - Zmenší fotky v obsahu článků
 * - Vytvoří thumbnaily pro náhledy článků (velké a malé)
 * - Zmenší profilové fotky uživatelů
 */

$internalRun = defined('MIGRATION_INTERNAL_RUN') && MIGRATION_INTERNAL_RUN;

if (!$internalRun && php_sapi_name() !== 'cli') {
    $credentialsFile = __DIR__ . '/../config/db_credentials.php';
    if (file_exists($credentialsFile)) {
        require_once $credentialsFile;
    }

    $token = (string)($_GET['token'] ?? '');
    if (!defined('MIGRATION_TOKEN') || $token === '' || !hash_equals((string)MIGRATION_TOKEN, $token)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

// Pro webový výstup - vypnout buffering pro průběžný výstup
if (!$internalRun && php_sapi_name() !== 'cli') {
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: text/html; charset=utf-8');
    if (function_exists('apache_setenv')) {
        @apache_setenv('no-gzip', 1);
    }
    @ini_set('zlib.output_compression', 0);
}

// Funkce pro výpis zpráv
function zprava($text) {
    global $internalRun;
    echo $text . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
    if (!$internalRun && php_sapi_name() !== 'cli') {
        flush();
        if (ob_get_level() > 0) {
            ob_flush();
        }
    }
}

// ============================================================================
// KONFIGURACE CEST
// ============================================================================

$base_path = dirname(__DIR__);

// Nové cesty (kde jsou fotky po ručním přesunu)
$paths = [
    'articles' => $base_path . '/web/uploads/articles/',           // Fotky v obsahu článků
    'thumbnails_velke' => $base_path . '/web/uploads/thumbnails/velke/',  // Velké náhledy
    'thumbnails_male' => $base_path . '/web/uploads/thumbnails/male/',    // Malé náhledy
    'users' => $base_path . '/web/uploads/users/thumbnails/'              // Profilové fotky
];

// Parametry pro zpracování
$type = isset($_GET['type']) ? $_GET['type'] : 'all'; // all, articles, thumbnails, users
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50; // Počet fotek na jedno spuštění
$offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

// ============================================================================
// FUNKCE PRO ZPRACOVÁNÍ OBRÁZKŮ
// ============================================================================

/**
 * Zmenší obrázek v obsahu článku (max 1920px šířka, zachovat poměr)
 */
function resizeArticleImage($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    list($originalWidth, $originalHeight, $imageType) = @getimagesize($filePath);
    if (!$originalWidth || !$originalHeight) {
        return false;
    }
    
    $maxWidth = 1920;
    
    // Pokud je obrázek už menší, nic nedělat
    if ($originalWidth <= $maxWidth) {
        return true;
    }
    
    // Načtení obrázku
    $source = null;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $source = @imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $source = @imagecreatefrompng($filePath);
            break;
        case IMAGETYPE_GIF:
            $source = @imagecreatefromgif($filePath);
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Výpočet nových rozměrů
    $ratio = $maxWidth / $originalWidth;
    $newWidth = $maxWidth;
    $newHeight = round($originalHeight * $ratio);
    
    // Vytvoření nového obrázku
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Zachování průhlednosti pro PNG
    if ($imageType === IMAGETYPE_PNG) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Změna velikosti
    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Uložení
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($newImage, $filePath, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($newImage, $filePath, 6);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($newImage, $filePath);
            break;
    }
    
    imagedestroy($newImage);
    imagedestroy($source);
    
    return $result;
}

/**
 * Vytvoří thumbnail pro náhled článku (poměr 3:2)
 */
function createArticleThumbnail($sourcePath, $targetPath, $maxWidth, $maxHeight) {
    if (!file_exists($sourcePath)) {
        return false;
    }
    
    // Načtení EXIF dat pro zjištění orientace
    $exif = @exif_read_data($sourcePath);
    
    // Načtení původního obrázku
    list($originalWidth, $originalHeight, $imageType) = @getimagesize($sourcePath);
    if (!$originalWidth || !$originalHeight) {
        return false;
    }
    
    // Načtení zdrojového obrázku
    $sourceImage = null;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = @imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = @imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = @imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Oprava orientace podle EXIF dat
    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $sourceImage = imagerotate($sourceImage, 180, 0);
                break;
            case 6:
                $sourceImage = imagerotate($sourceImage, -90, 0);
                list($originalWidth, $originalHeight) = array($originalHeight, $originalWidth);
                break;
            case 8:
                $sourceImage = imagerotate($sourceImage, 90, 0);
                list($originalWidth, $originalHeight) = array($originalHeight, $originalWidth);
                break;
        }
    }
    
    // Výpočet cílového poměru stran (3:2)
    $targetRatio = 3 / 2;
    $sourceRatio = $originalWidth / $originalHeight;
    
    // Určení rozměrů pro oříznutí
    if ($sourceRatio < $targetRatio) {
        // Obrázek je vyšší než potřebujeme
        $cropHeight = round($originalWidth / $targetRatio);
        $cropWidth = $originalWidth;
        $cropX = 0;
        $cropY = round(($originalHeight - $cropHeight) / 2);
    } else {
        // Obrázek je širší nebo má správný poměr
        $cropWidth = round($originalHeight * $targetRatio);
        $cropHeight = $originalHeight;
        $cropX = round(($originalWidth - $cropWidth) / 2);
        $cropY = 0;
    }
    
    // Vytvoření dočasného obrázku pro ořez
    $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);
    
    // Zachování průhlednosti pro PNG
    if ($imageType === IMAGETYPE_PNG) {
        imagealphablending($croppedImage, false);
        imagesavealpha($croppedImage, true);
    }
    
    // Provedení ořezu
    imagecopy($croppedImage, $sourceImage, 0, 0, $cropX, $cropY, $cropWidth, $cropHeight);
    
    // Výpočet finálních rozměrů pro změnu velikosti
    if ($cropWidth > $maxWidth || $cropHeight > $maxHeight) {
        $ratio = min($maxWidth / $cropWidth, $maxHeight / $cropHeight);
        $newWidth = round($cropWidth * $ratio);
        $newHeight = round($cropHeight * $ratio);
    } else {
        $newWidth = $cropWidth;
        $newHeight = $cropHeight;
    }
    
    // Vytvoření finálního obrázku
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Zachování průhlednosti pro PNG
    if ($imageType === IMAGETYPE_PNG) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Změna velikosti oříznutého obrázku
    imagecopyresampled($newImage, $croppedImage, 0, 0, 0, 0, $newWidth, $newHeight, $cropWidth, $cropHeight);
    
    // Uložení výsledku
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($newImage, $targetPath, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($newImage, $targetPath, 6);
            break;
    }
    
    // Uvolnění paměti
    imagedestroy($newImage);
    imagedestroy($croppedImage);
    imagedestroy($sourceImage);
    
    return $result;
}

/**
 * Zmenší profilovou fotku (400x400 čtverec)
 */
function resizeUserPhoto($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    list($originalWidth, $originalHeight, $imageType) = @getimagesize($filePath);
    if (!$originalWidth || !$originalHeight) {
        return false;
    }
    
    $maxSize = 400;
    
    // Načtení obrázku
    $source = null;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $source = @imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $source = @imagecreatefrompng($filePath);
            break;
        case IMAGETYPE_GIF:
            $source = @imagecreatefromgif($filePath);
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Ořez na čtverec podle menší strany
    $size = min($originalWidth, $originalHeight);
    $srcX = ($originalWidth - $size) / 2;
    $srcY = ($originalHeight - $size) / 2;
    
    $croppedImage = imagecreatetruecolor($size, $size);
    
    // Zachování průhlednosti pro PNG
    if ($imageType === IMAGETYPE_PNG) {
        imagealphablending($croppedImage, false);
        imagesavealpha($croppedImage, true);
    }
    
    imagecopyresampled($croppedImage, $source, 0, 0, $srcX, $srcY, $size, $size, $size, $size);
    
    // Změna velikosti na 400x400
    $resizedImage = imagecreatetruecolor($maxSize, $maxSize);
    
    if ($imageType === IMAGETYPE_PNG) {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
    }
    
    imagecopyresampled($resizedImage, $croppedImage, 0, 0, 0, 0, $maxSize, $maxSize, $size, $size);
    
    // Uložení
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($resizedImage, $filePath, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($resizedImage, $filePath, 1);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($resizedImage, $filePath);
            break;
    }
    
    imagedestroy($resizedImage);
    imagedestroy($croppedImage);
    imagedestroy($source);
    
    return $result;
}

// ============================================================================
// HLAVNÍ ZPRACOVÁNÍ
// ============================================================================

zprava("=== 🖼️ Zpracování fotek po migraci ===\n");

$processed = 0;
$errors = 0;

// 1. Zpracování fotek v obsahu článků
if ($type === 'all' || $type === 'articles') {
    zprava("\n📸 Zpracování fotek v obsahu článků...");
    
    if (!is_dir($paths['articles'])) {
        zprava("⚠️ Složka neexistuje: " . $paths['articles']);
    } else {
        $files = glob($paths['articles'] . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        sort($files);
        $total = count($files);
        zprava("Našlo se $total souborů.");
        
        $batchFiles = $limit > 0 ? array_slice($files, $offset, $limit) : array_slice($files, $offset);
        $count = 0;
        $seen = 0;
        foreach ($batchFiles as $file) {
            $seen++;
            
            $filename = basename($file);
            if (resizeArticleImage($file)) {
                $processed++;
                $count++;
                if ($count % 10 == 0) {
                    zprava("  Zpracováno $count fotek...");
                }
            } else {
                $errors++;
                zprava("  ⚠️ Chyba u: $filename");
            }
        }
        
        zprava("✓ Zpracováno $count fotek v obsahu článků.");
        $nextOffset = $offset + $seen;
        $done = ($seen === 0) || ($nextOffset >= $total);
        zprava("MIGRATE_IMAGES_TYPE=articles");
        zprava("MIGRATE_IMAGES_TOTAL=$total");
        zprava("MIGRATE_IMAGES_OFFSET=$offset");
        zprava("MIGRATE_IMAGES_SEEN=$seen");
        zprava("MIGRATE_IMAGES_NEXT_OFFSET=$nextOffset");
        zprava("MIGRATE_IMAGES_DONE=" . ($done ? '1' : '0'));
        zprava("PROGRESS_CURRENT=$nextOffset");
        zprava("PROGRESS_TOTAL=$total");
    }
}

// 2. Zpracování náhledů článků (velké a malé)
if ($type === 'all' || $type === 'thumbnails') {
    zprava("\n🖼️ Zpracování náhledů článků...");
    
    // Zkontrolovat, jestli existuje složka s velkými náhledy
    if (!is_dir($paths['thumbnails_velke'])) {
        zprava("⚠️ Složka neexistuje: " . $paths['thumbnails_velke']);
        zprava("💡 Vytvoř složku a přesuň tam velké náhledy z: /www/subdom/magazin/assets/img/upload/clanek_nahled/");
    } else {
        // Zajistit, že existuje složka pro malé náhledy
        if (!is_dir($paths['thumbnails_male'])) {
            mkdir($paths['thumbnails_male'], 0777, true);
        }
        
        $files = glob($paths['thumbnails_velke'] . '*.{jpg,jpeg,png}', GLOB_BRACE);
        sort($files);
        $total = count($files);
        zprava("Našlo se $total souborů pro zpracování.");
        
        $batchFiles = $limit > 0 ? array_slice($files, $offset, $limit) : array_slice($files, $offset);
        $count = 0;
        $seen = 0;
        foreach ($batchFiles as $file) {
            $seen++;
            
            $filename = basename($file);
            
            // Vytvořit velký thumbnail (1200x800)
            if (createArticleThumbnail($file, $file, 1200, 800)) {
                // Vytvořit malý thumbnail (600x400)
                $smallPath = $paths['thumbnails_male'] . $filename;
                if (createArticleThumbnail($file, $smallPath, 600, 400)) {
                    $processed++;
                    $count++;
                    if ($count % 10 == 0) {
                        zprava("  Zpracováno $count náhledů...");
                    }
                } else {
                    $errors++;
                    zprava("  ⚠️ Chyba při vytváření malého náhledu: $filename");
                }
            } else {
                $errors++;
                zprava("  ⚠️ Chyba při zpracování: $filename");
            }
        }
        
        zprava("✓ Zpracováno $count náhledů článků.");
        $nextOffset = $offset + $seen;
        $done = ($seen === 0) || ($nextOffset >= $total);
        zprava("MIGRATE_IMAGES_TYPE=thumbnails");
        zprava("MIGRATE_IMAGES_TOTAL=$total");
        zprava("MIGRATE_IMAGES_OFFSET=$offset");
        zprava("MIGRATE_IMAGES_SEEN=$seen");
        zprava("MIGRATE_IMAGES_NEXT_OFFSET=$nextOffset");
        zprava("MIGRATE_IMAGES_DONE=" . ($done ? '1' : '0'));
        zprava("PROGRESS_CURRENT=$nextOffset");
        zprava("PROGRESS_TOTAL=$total");
    }
}

// 3. Zpracování profilových fotek
if ($type === 'all' || $type === 'users') {
    zprava("\n👤 Zpracování profilových fotek...");
    
    if (!is_dir($paths['users'])) {
        zprava("⚠️ Složka neexistuje: " . $paths['users']);
    } else {
        $files = glob($paths['users'] . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        sort($files);
        $total = count($files);
        zprava("Našlo se $total souborů.");
        
        $batchFiles = $limit > 0 ? array_slice($files, $offset, $limit) : array_slice($files, $offset);
        $count = 0;
        $seen = 0;
        foreach ($batchFiles as $file) {
            $seen++;
            
            $filename = basename($file);
            if (resizeUserPhoto($file)) {
                $processed++;
                $count++;
                if ($count % 10 == 0) {
                    zprava("  Zpracováno $count fotek...");
                }
            } else {
                $errors++;
                zprava("  ⚠️ Chyba u: $filename");
            }
        }
        
        zprava("✓ Zpracováno $count profilových fotek.");
        $nextOffset = $offset + $seen;
        $done = ($seen === 0) || ($nextOffset >= $total);
        zprava("MIGRATE_IMAGES_TYPE=users");
        zprava("MIGRATE_IMAGES_TOTAL=$total");
        zprava("MIGRATE_IMAGES_OFFSET=$offset");
        zprava("MIGRATE_IMAGES_SEEN=$seen");
        zprava("MIGRATE_IMAGES_NEXT_OFFSET=$nextOffset");
        zprava("MIGRATE_IMAGES_DONE=" . ($done ? '1' : '0'));
        zprava("PROGRESS_CURRENT=$nextOffset");
        zprava("PROGRESS_TOTAL=$total");
    }
}

// ============================================================================
// VÝSLEDKY
// ============================================================================

zprava("\n=== ✅ Zpracování dokončeno ===");
zprava("Zpracováno: $processed fotek");
if ($errors > 0) {
    zprava("Chyb: $errors");
}

zprava("\n💡 Pro pokračování použij:");
zprava("   ?type=articles&limit=$limit&offset=" . ($offset + $limit));
zprava("   ?type=thumbnails&limit=$limit&offset=" . ($offset + $limit));
zprava("   ?type=users&limit=$limit&offset=" . ($offset + $limit));
zprava("   ?type=all&limit=$limit&offset=" . ($offset + $limit));

