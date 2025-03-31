<?php
// Nastavení pro vývojové prostředí
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '512M');

// Vytvoření složky pro export
$export_dir = 'export_data';
if (!file_exists($export_dir)) {
    mkdir($export_dir, 0777, true);
}

// Cesty k souborům na starém serveru
$old_articles_path = 'https://www.magazin.cyklistickey.cz/assets/html/clanek_';
$old_audio_path = 'https://www.magazin.cyklistickey.cz/assets/audio/';
$old_images_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/';
$old_profile_photos_path = 'https://www.magazin.cyklistickey.cz/assets/profil_foto/';
$old_header_photos_path = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/profil_zahlavi/';
$old_thumbnails_path = 'https://www.magazin.cyklistickey.cz/assets/clanky_nahled/';
$old_thumbnails_small_path = 'https://www.magazin.cyklistickey.cz/assets/clanky_nahled/nahled/';

// Cesty k souborům v export složce
$export_audio_path = $export_dir . '/audio/';
$export_images_path = $export_dir . '/articles/';
$export_profile_photos_path = $export_dir . '/users/thumbnails/';
$export_header_photos_path = $export_dir . '/header/';
$export_thumbnails_path = $export_dir . '/thumbnails/velke/';
$export_thumbnails_small_path = $export_dir . '/thumbnails/male/';

// Konfigurace databáze
$db_config = [
    'host' => 'md396.wedos.net',
    'username' => 'w340619_clanky',
    'password' => 'bqsUuxcr',
    'database' => 'd340619_clanky'
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
    global $export_audio_path, $export_images_path, $export_profile_photos_path, 
           $export_header_photos_path, $export_thumbnails_path, $export_thumbnails_small_path;
    
    $directories = [
        $export_audio_path, 
        $export_images_path, 
        $export_profile_photos_path, 
        $export_header_photos_path,
        $export_thumbnails_path,
        $export_thumbnails_small_path
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

// Funkce pro stáhnutí souboru z URL
function downloadFile($url, $destination) {
    $ch = curl_init($url);
    $fp = fopen($destination, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $success = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    
    if ($success) {
        echo "<p style='color: green'>✓ Soubor stažen: $destination</p>";
        return true;
    } else {
        echo "<p style='color: red'>✗ Chyba při stahování souboru: $url</p>";
        return false;
    }
}

// Funkce pro zpracování obsahu článku a kopírování obrázků
function processArticleContent($content, $old_images_path, $export_images_path) {
    // Hledání všech obrázků v obsahu
    $pattern = '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i';
    preg_match_all($pattern, $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $img_src) {
            // Získání názvu souboru z cesty
            $filename = basename($img_src);
            
            // Kopírování obrázku do export složky
            $old_path = $old_images_path . $filename;
            $new_path = $export_images_path . $filename;
            
            if (copyFile($old_path, $new_path)) {
                // Aktualizace cesty v obsahu
                $content = str_replace($img_src, '/web/uploads/articles/' . $filename, $content);
            }
        }
    }
    
    return $content;
}

// Na začátek souboru přidám sledování času
$start_time = microtime(true);

echo "<h1>Spouštím export dat</h1>";
echo "<p>Začátek exportu: " . date('d.m.Y H:i:s') . "</p>";

// Vytvoření potřebných složek
createDirectories();

// Otevření souboru pro SQL export
$sql_file = fopen($export_dir . '/migrace.sql', 'w');

// Přidání příkazů pro vypnutí cizích klíčů na začátek SQL souboru
fwrite($sql_file, "SET FOREIGN_KEY_CHECKS=0;\n\n");

// Připojení k databázi
try {
    $db = connectDB($db_config);
    echo "<p style='color: green'>✓ Připojeno k databázi</p>";
} catch (Exception $e) {
    die("<p style='color: red'>✗ Chyba: " . $e->getMessage() . "</p>");
}

// 1. Export uživatelů
echo "<h2>Export uživatelů</h2>";
$users = $db->query("SELECT * FROM users");
if ($users) {
    while ($user = $users->fetch_assoc()) {
        // Kopírování profilové fotky
        if (!empty($user['profil_foto'])) {
            $old_photo = $old_profile_photos_path . $user['profil_foto'];
            $new_photo = $export_profile_photos_path . $user['profil_foto'];
            copyFile($old_photo, $new_photo);
        }
        
        // Generování SQL příkazu
        $sql = "INSERT INTO users (email, heslo, role, name, surname, profil_foto, popis, datum) VALUES (";
        $sql .= "'" . $db->real_escape_string($user['email']) . "', ";
        $sql .= "'" . $db->real_escape_string($user['heslo']) . "', ";
        $sql .= $user['admin'] . ", ";
        $sql .= "'" . $db->real_escape_string($user['name']) . "', ";
        $sql .= "'" . $db->real_escape_string($user['surname']) . "', ";
        $sql .= "'" . $db->real_escape_string($user['profil_foto']) . "', ";
        $sql .= "'" . $db->real_escape_string($user['popis']) . "', ";
        $sql .= ($user['datum'] ? "'" . $user['datum'] . "'" : "NULL");
        $sql .= ");\n";
        fwrite($sql_file, $sql);
        
        echo "<p style='color: green'>✓ Uživatel {$user['name']} {$user['surname']} exportován</p>";
    }
}

// 2. Export kategorií
echo "<h2>Export kategorií</h2>";
$categories = $db->query("SELECT * FROM kategorie");
if ($categories) {
    while ($category = $categories->fetch_assoc()) {
        $sql = "INSERT INTO kategorie (nazev_kategorie, url) VALUES (";
        $sql .= "'" . $db->real_escape_string($category['nazev_kategorie']) . "', ";
        $sql .= "'" . $db->real_escape_string($category['url']) . "'";
        $sql .= ");\n";
        fwrite($sql_file, $sql);
        
        echo "<p style='color: green'>✓ Kategorie {$category['nazev_kategorie']} exportována</p>";
    }
}

// 3. Export článků z posledního měsíce
echo "<h2>Export článků</h2>";
$articles = $db->query("SELECT * FROM clanky WHERE datum >= '$last_month'");
if ($articles) {
    while ($article = $articles->fetch_assoc()) {
        // Kopírování náhledových obrázků
        if (!empty($article['nahled_foto'])) {
            $old_thumb = $old_thumbnails_path . $article['nahled_foto'];
            $new_thumb = $export_thumbnails_path . $article['nahled_foto'];
            downloadFile($old_thumb, $new_thumb);
            
            $old_thumb_small = $old_thumbnails_small_path . $article['nahled_foto'];
            $new_thumb_small = $export_thumbnails_small_path . $article['nahled_foto'];
            downloadFile($old_thumb_small, $new_thumb_small);
        }
        
        // Načtení obsahu článku
        $content_file = $old_articles_path . $article['id'] . '.php';
        $content = file_get_contents($content_file);
        if ($content === false) {
            $content = "<!-- Obsah článku nebyl nalezen -->";
        }
        
        // Kopírování audio souborů
        $audio_query = $db->query("SELECT * FROM audio WHERE id_clanku = {$article['id']}");
        if ($audio_query) {
            while ($audio = $audio_query->fetch_assoc()) {
                $old_audio = $old_audio_path . $audio['nazev_souboru'];
                $new_audio = $export_audio_path . $article['id'] . '.mp3';
                downloadFile($old_audio, $new_audio);
            }
        }
        
        // Generování SQL příkazu pro článek - zachování původního ID
        $sql = "INSERT INTO clanky (id, nazev, datum, viditelnost, nahled_foto, obsah, user_id, url) VALUES (";
        $sql .= $article['id'] . ", ";
        $sql .= "'" . $db->real_escape_string($article['nazev']) . "', ";
        $sql .= "'" . $article['datum'] . "', ";
        $sql .= $article['viditelnost'] . ", ";
        $sql .= "'" . $db->real_escape_string($article['nahled_foto']) . "', ";
        $sql .= "'" . $db->real_escape_string($content) . "', ";
        $sql .= $article['user_id'] . ", ";
        $sql .= "'" . $db->real_escape_string($article['url']) . "'";
        $sql .= ");\n";
        fwrite($sql_file, $sql);
        
        // Export kategorií článku - použití původního ID článku
        $article_categories = $db->query("SELECT * FROM kategorie_clanku WHERE id_clanku = {$article['id']}");
        if ($article_categories) {
            while ($cat = $article_categories->fetch_assoc()) {
                $sql = "INSERT INTO clanky_kategorie (id_clanku, id_kategorie) VALUES ({$article['id']}, {$cat['id_kategorie']});\n";
                fwrite($sql_file, $sql);
            }
        }
        
        // Export statistik zobrazení - použití původního ID článku
        $views = $db->query("SELECT * FROM views_clanku WHERE id_clanku = {$article['id']} AND datum >= '$last_month'");
        if ($views) {
            while ($view = $views->fetch_assoc()) {
                $sql = "INSERT INTO views_clanku (id_clanku, pocet, datum) VALUES ({$article['id']}, {$view['pocet']}, '{$view['datum']}');\n";
                fwrite($sql_file, $sql);
            }
        }
        
        // Export propagací - použití původního ID článku
        $promotions = $db->query("SELECT * FROM propagace WHERE id_clanku = {$article['id']} AND datum >= '$last_month'");
        if ($promotions) {
            while ($promo = $promotions->fetch_assoc()) {
                // Vypočítáme konec propagace (7 dní od začátku)
                $start_date = date('Y-m-d H:i:s', strtotime($promo['datum']));
                $end_date = date('Y-m-d H:i:s', strtotime($start_date . ' +7 days'));
                
                $user_id = intval($promo['user_id']);
                $clanek_id = intval($article['id']);
                
                $sql = "INSERT INTO propagace (id_clanku, user_id, zacatek, konec) VALUES (";
                $sql .= $clanek_id . ", ";
                $sql .= $user_id . ", ";
                $sql .= "'" . $start_date . "', ";
                $sql .= "'" . $end_date . "'";
                $sql .= ");\n";
                fwrite($sql_file, $sql);
            }
        }
        
        echo "<p style='color: green'>✓ Článek {$article['nazev']} exportován</p>";
    }
}

// 4. Export sociálních sítí a jejich propojení s uživateli
echo "<h2>Export sociálních sítí</h2>";

// Nejprve vytvoříme záznamy pro všechny typy sociálních sítí
$social_networks = [
    ['fa-instagram', 'instagram'],
    ['fa-twitter', 'twitter'],
    ['fa-strava', 'strava']
];

foreach ($social_networks as $index => $network) {
    $fa_class = $network[0];
    $nazev = $network[1];
    $sql = "INSERT INTO socials (id, fa_class, nazev) VALUES (";
    $sql .= ($index + 1) . ", ";
    $sql .= "'" . $fa_class . "', ";
    $sql .= "'" . $nazev . "'";
    $sql .= ");\n";
    fwrite($sql_file, $sql);
    echo "<p style='color: green'>✓ Sociální síť {$nazev} exportována</p>";
}

// Pak exportujeme propojení uživatelů se sociálními sítěmi
echo "<h2>Export propojení uživatelů a sociálních sítí</h2>";
$users = $db->query("SELECT id, ig, twitter, strava FROM users WHERE ig IS NOT NULL OR twitter IS NOT NULL OR strava IS NOT NULL");
if ($users) {
    while ($user = $users->fetch_assoc()) {
        if (!empty($user['ig'])) {
            $sql = "INSERT INTO user_social (user_id, social_id, link) VALUES (";
            $sql .= $user['id'] . ", 1, '" . $db->real_escape_string($user['ig']) . "');\n";
            fwrite($sql_file, $sql);
            echo "<p style='color: green'>✓ Instagram link exportován pro uživatele ID {$user['id']}</p>";
        }
        
        if (!empty($user['twitter'])) {
            $sql = "INSERT INTO user_social (user_id, social_id, link) VALUES (";
            $sql .= $user['id'] . ", 2, '" . $db->real_escape_string($user['twitter']) . "');\n";
            fwrite($sql_file, $sql);
            echo "<p style='color: green'>✓ Twitter link exportován pro uživatele ID {$user['id']}</p>";
        }
        
        if (!empty($user['strava'])) {
            $sql = "INSERT INTO user_social (user_id, social_id, link) VALUES (";
            $sql .= $user['id'] . ", 3, '" . $db->real_escape_string($user['strava']) . "');\n";
            fwrite($sql_file, $sql);
            echo "<p style='color: green'>✓ Strava link exportován pro uživatele ID {$user['id']}</p>";
        }
    }
}

// Přidáme výchozí záznamy pro admin_access
echo "<h2>Export admin přístupů</h2>";
$admin_pages = [
    ['clanky', true, false],
    ['uzivatele', true, true],
    ['kategorie', true, false],
    ['statistiky', true, false]
];

foreach ($admin_pages as $page) {
    $sql = "INSERT INTO admin_access (page, role_1, role_2) VALUES (";
    $sql .= "'" . $page[0] . "', ";
    $sql .= ($page[1] ? "1" : "0") . ", ";
    $sql .= ($page[2] ? "1" : "0");
    $sql .= ");\n";
    fwrite($sql_file, $sql);
    echo "<p style='color: green'>✓ Admin přístup pro stránku {$page[0]} exportován</p>";
}

// Přidání příkazů pro zapnutí cizích klíčů na konec SQL souboru
fwrite($sql_file, "\nSET FOREIGN_KEY_CHECKS=1;\n");

// Zavření souborů a připojení
fclose($sql_file);
$db->close();

// Na konec souboru přidám výpis času a doby běhu
$end_time = microtime(true);
$execution_time = ($end_time - $start_time);

echo "<h2>Export dokončen</h2>";
echo "<p style='color: green'>✓ Data byla úspěšně exportována</p>";
echo "<p>Konec exportu: " . date('d.m.Y H:i:s') . "</p>";
echo "<p>Celková doba běhu: " . round($execution_time, 2) . " sekund</p>";
echo "<p>V adresáři 'export_data' najdete:</p>";
echo "<ul>";
echo "<li>migrace.sql - SQL příkazy pro import dat</li>";
echo "<li>audio/ - zvukové soubory</li>";
echo "<li>articles/ - obrázky článků</li>";
echo "<li>articles/velke/ - velké náhledy</li>";
echo "<li>articles/male/ - malé náhledy</li>";
echo "<li>profile/ - profilové fotky</li>";
echo "<li>header/ - záhlaví profilů</li>";
echo "</ul>";
echo "<p>Pro import dat:</p>";
echo "<ol>";
echo "<li>Nahrajte celou složku 'export_data' na nový server</li>";
echo "<li>Spusťte skript import.php</li>";
echo "</ol>";
?> 