<?php
// Nastavení pro vývojové prostředí
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '512M');

// Cesty k souborům na starém serveru
$old_articles_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/html/clanek_';
$old_audio_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/audio/';
$old_images_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/';
$old_profile_photos_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/profil_foto/';
$old_header_photos_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/profil_zahlavi/';
$old_thumbnails_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/clanek_nahled/';
$old_thumbnails_small_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/clanek_nahled/nahled/';

// Cesty k souborům na novém serveru
$new_audio_path = 'web/uploads/audio/';
$new_images_path = 'web/uploads/articles/';
$new_profile_photos_path = 'web/uploads/profile/';
$new_header_photos_path = 'web/uploads/header/';
$new_thumbnails_path = 'web/uploads/articles/velke/';
$new_thumbnails_small_path = 'web/uploads/articles/male/';

// Konfigurace databází
$old_db_config = [
    'host' => 'md396.wedos.net',
    'username' => 'w340619_clanky',
    'password' => 'bqsUuxcr',
    'database' => 'd340619_clanky'
];

$new_db_config = [
    'host' => 'db.mp.spse-net.cz',
    'username' => 'vincenon21',
    'password' => 'larahobujulu',
    'database' => 'vincenon21_1'
];

// Datum pro filtrování článků (poslední měsíc)
$last_month = date('Y-m-d', strtotime('-1 month'));

// Funkce pro připojení k databázi
function connectDB($config) {
    try {
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
        if ($conn->connect_error) {
            throw new Exception("Připojení selhalo: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Chyba při připojování k databázi: " . $e->getMessage());
    }
}

// Funkce pro vytvoření složek
function createDirectories() {
    global $new_audio_path, $new_images_path, $new_profile_photos_path, $new_header_photos_path, 
           $new_thumbnails_path, $new_thumbnails_small_path;
    
    $directories = [
        $new_audio_path, 
        $new_images_path, 
        $new_profile_photos_path, 
        $new_header_photos_path,
        $new_thumbnails_path,
        $new_thumbnails_small_path
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
            echo "<p style='color: green'>✓ Vytvořena složka: $dir</p>";
        }
    }
}

// Funkce pro kopírování souboru
function copyFile($source, $destination) {
    if (file_exists($source)) {
        if (copy($source, $destination)) {
            echo "<p style='color: green'>✓ Soubor zkopírován: $destination</p>";
            return true;
        } else {
            echo "<p style='color: red'>✗ Chyba při kopírování souboru: $source</p>";
            return false;
        }
    }
    return false;
}

// Funkce pro zpracování obsahu článku a kopírování obrázků
function processArticleContent($content, $old_images_path, $new_images_path) {
    // Hledání všech obrázků v obsahu
    $pattern = '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i';
    preg_match_all($pattern, $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $img_src) {
            // Získání názvu souboru z cesty
            $filename = basename($img_src);
            
            // Kopírování obrázku do nové složky
            $old_path = $old_images_path . $filename;
            $new_path = $new_images_path . $filename;
            
            if (copyFile($old_path, $new_path)) {
                // Aktualizace cesty v obsahu
                $content = str_replace($img_src, '/web/uploads/articles/' . $filename, $content);
            }
        }
    }
    
    return $content;
}

echo "<h1>Spouštím migraci dat</h1>";

// Vytvoření potřebných složek
createDirectories();

// Připojení k databázím
try {
    $old_db = connectDB($old_db_config);
    echo "<p style='color: green'>✓ Připojeno ke staré databázi</p>";
    
    $new_db = connectDB($new_db_config);
    echo "<p style='color: green'>✓ Připojeno k nové databázi</p>";
} catch (Exception $e) {
    die("<p style='color: red'>✗ Chyba: " . $e->getMessage() . "</p>");
}

// 1. Migrace uživatelů
echo "<h2>Migrace uživatelů</h2>";
$users = $old_db->query("SELECT * FROM users");
if ($users) {
    while ($user = $users->fetch_assoc()) {
        // Kopírování profilové fotky
        if (!empty($user['profil_foto'])) {
            $old_photo = $old_profile_photos_path . $user['profil_foto'];
            $new_photo = $new_profile_photos_path . $user['profil_foto'];
            copyFile($old_photo, $new_photo);
        }
        
        // Kopírování fotky záhlaví
        if (!empty($user['zahlavi_foto'])) {
            $old_header = $old_header_photos_path . $user['zahlavi_foto'];
            $new_header = $new_header_photos_path . $user['zahlavi_foto'];
            copyFile($old_header, $new_header);
        }
        
        // Vložení uživatele do nové databáze
        $stmt = $new_db->prepare("INSERT INTO users (email, heslo, role, name, surname, profil_foto, zahlavi_foto, popis, datum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissssss", 
            $user['email'],
            $user['heslo'],
            $user['role'],
            $user['name'],
            $user['surname'],
            $user['profil_foto'],
            $user['zahlavi_foto'],
            $user['popis'],
            $user['datum']
        );
        
        if ($stmt->execute()) {
            echo "<p style='color: green'>✓ Uživatel {$user['name']} {$user['surname']} importován</p>";
        } else {
            echo "<p style='color: red'>✗ Chyba při importu uživatele: " . $stmt->error . "</p>";
        }
    }
}

// 2. Migrace kategorií
echo "<h2>Migrace kategorií</h2>";
$categories = $old_db->query("SELECT * FROM kategorie");
if ($categories) {
    while ($category = $categories->fetch_assoc()) {
        $stmt = $new_db->prepare("INSERT INTO kategorie (nazev_kategorie, url) VALUES (?, ?)");
        $stmt->bind_param("ss", $category['nazev_kategorie'], $category['url']);
        
        if ($stmt->execute()) {
            echo "<p style='color: green'>✓ Kategorie {$category['nazev_kategorie']} importována</p>";
        } else {
            echo "<p style='color: red'>✗ Chyba při importu kategorie: " . $stmt->error . "</p>";
        }
    }
}

// 3. Migrace článků z posledního měsíce
echo "<h2>Migrace článků</h2>";
$articles = $old_db->query("SELECT * FROM clanky WHERE datum >= '$last_month'");
if ($articles) {
    while ($article = $articles->fetch_assoc()) {
        // Kopírování náhledových obrázků (velké)
        if (!empty($article['nahled_foto'])) {
            $old_thumb = $old_thumbnails_path . $article['nahled_foto'];
            $new_thumb = $new_thumbnails_path . $article['nahled_foto'];
            copyFile($old_thumb, $new_thumb);
            
            // Kopírování malých náhledů
            $old_thumb_small = $old_thumbnails_small_path . $article['nahled_foto'];
            $new_thumb_small = $new_thumbnails_small_path . $article['nahled_foto'];
            copyFile($old_thumb_small, $new_thumb_small);
            
            // Aktualizace cesty v databázi
            $article['nahled_foto'] = 'velke/' . $article['nahled_foto'];
        }
        
        // Načtení obsahu článku
        $content_file = $old_articles_path . $article['id'] . '.html';
        if (file_exists($content_file)) {
            $content = file_get_contents($content_file);
            // Zpracování obsahu a kopírování obrázků
            $content = processArticleContent($content, $old_images_path, $new_images_path);
        } else {
            $content = "<!-- Obsah článku nebyl nalezen -->";
        }
        
        // Kopírování audio souborů
        $audio_files = glob($old_audio_path . '*');
        foreach ($audio_files as $audio_file) {
            if (strpos($audio_file, $article['id']) !== false) {
                $extension = pathinfo($audio_file, PATHINFO_EXTENSION);
                $new_audio_file = $new_audio_path . $article['id'] . '.' . $extension;
                copyFile($audio_file, $new_audio_file);
            }
        }
        
        // Vložení článku do nové databáze
        $stmt = $new_db->prepare("INSERT INTO clanky (nazev, datum, viditelnost, nahled_foto, obsah, user_id, url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissis", 
            $article['nazev'],
            $article['datum'],
            $article['viditelnost'],
            $article['nahled_foto'],
            $content,
            $article['user_id'],
            $article['url']
        );
        
        if ($stmt->execute()) {
            $new_article_id = $new_db->insert_id;
            echo "<p style='color: green'>✓ Článek {$article['nazev']} importován</p>";
            
            // Migrace kategorií článku
            $article_categories = $old_db->query("SELECT * FROM kategorie_clanku WHERE id_clanku = {$article['id']}");
            if ($article_categories) {
                while ($cat = $article_categories->fetch_assoc()) {
                    $new_db->query("INSERT INTO clanky_kategorie (id_clanku, id_kategorie) VALUES ($new_article_id, {$cat['id_kategorie']})");
                }
            }
            
            // Migrace statistik zobrazení
            $views = $old_db->query("SELECT * FROM views_clanku WHERE id_clanku = {$article['id']} AND datum >= '$last_month'");
            if ($views) {
                while ($view = $views->fetch_assoc()) {
                    $new_db->query("INSERT INTO views_clanku (id_clanku, pocet, datum) VALUES ($new_article_id, {$view['pocet']}, '{$view['datum']}')");
                }
            }
            
            // Migrace propagací
            $promotions = $old_db->query("SELECT * FROM propagace WHERE id_clanku = {$article['id']} AND zacatek >= '$last_month'");
            if ($promotions) {
                while ($promo = $promotions->fetch_assoc()) {
                    $new_db->query("INSERT INTO propagace (id_clanku, user_id, zacatek, konec) VALUES ($new_article_id, {$promo['user_id']}, '{$promo['zacatek']}', '{$promo['konec']}')");
                }
            }
        } else {
            echo "<p style='color: red'>✗ Chyba při importu článku: " . $stmt->error . "</p>";
        }
    }
}

// 4. Migrace sociálních sítí
echo "<h2>Migrace sociálních sítí</h2>";
$socials = $old_db->query("SELECT * FROM socials");
if ($socials) {
    while ($social = $socials->fetch_assoc()) {
        $stmt = $new_db->prepare("INSERT INTO socials (fa_class, nazev) VALUES (?, ?)");
        $stmt->bind_param("ss", $social['fa_class'], $social['nazev']);
        
        if ($stmt->execute()) {
            echo "<p style='color: green'>✓ Sociální síť {$social['nazev']} importována</p>";
        } else {
            echo "<p style='color: red'>✗ Chyba při importu sociální sítě: " . $stmt->error . "</p>";
        }
    }
}

// 5. Migrace propojení uživatelů a sociálních sítí
echo "<h2>Migrace propojení uživatelů a sociálních sítí</h2>";
$user_socials = $old_db->query("SELECT * FROM user_social");
if ($user_socials) {
    while ($us = $user_socials->fetch_assoc()) {
        $stmt = $new_db->prepare("INSERT INTO user_social (user_id, social_id, link) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $us['user_id'], $us['social_id'], $us['link']);
        
        if ($stmt->execute()) {
            echo "<p style='color: green'>✓ Propojení uživatele a sociální sítě importováno</p>";
        } else {
            echo "<p style='color: red'>✗ Chyba při importu propojení: " . $stmt->error . "</p>";
        }
    }
}

// Zavření připojení
$old_db->close();
$new_db->close();

echo "<h2>Migrace dokončena</h2>";
echo "<p style='color: green'>✓ Data byla úspěšně přenesena</p>";
echo "<p>Zkontrolujte prosím, zda:</p>";
echo "<ul>";
echo "<li>Všechny soubory byly správně zkopírovány</li>";
echo "<li>Obrázky v článcích jsou správně zobrazeny</li>";
echo "<li>Audio soubory jsou dostupné</li>";
echo "<li>Profilové fotky uživatelů jsou viditelné</li>";
echo "</ul>";
?>
