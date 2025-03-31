<?php
// Nastavení pro vývojové prostředí
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '512M');

// Konfigurace databáze
$db_config = [
    'host' => 'db.mp.spse-net.cz',
    'username' => 'vincenon21',
    'password' => 'larahobujulu',
    'database' => 'vincenon21_1'
];

// Cesty k souborům
$export_dir = 'export_data';
$sql_file = $export_dir . '/migrace.sql';

// Cílové cesty pro soubory
$target_audio_path = 'web/uploads/audio/';
$target_images_path = 'web/uploads/articles/';
$target_profile_photos_path = 'web/uploads/profile/';
$target_thumbnails_path = 'web/uploads/articles/velke/';
$target_thumbnails_small_path = 'web/uploads/articles/male/';

// Cesty k souborům na novém serveru
$new_audio_path = 'web/uploads/audio/';
$new_thumbnails_path = 'web/uploads/thumbnails/velke/';
$new_thumbnails_small_path = 'web/uploads/thumbnails/male/';
$new_profile_photos_path = 'web/uploads/users/thumbnails/';

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
    global $new_audio_path, $new_thumbnails_path, $new_thumbnails_small_path, $new_profile_photos_path;
    
    $directories = [
        $new_audio_path,
        $new_thumbnails_path,
        $new_thumbnails_small_path,
        $new_profile_photos_path
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
            echo "<p style='color: green'>✓ Vytvořena složka: $dir</p>";
        }
    }
}

// Funkce pro přesun složky
function moveDirectory($source, $destination) {
    if (!file_exists($source)) {
        echo "<p style='color: red'>✗ Zdrojová složka neexistuje: $source</p>";
        return;
    }
    
    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }
    
    $files = scandir($source);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $src = $source . '/' . $file;
            $dst = $destination . '/' . $file;
            if (rename($src, $dst)) {
                echo "<p style='color: green'>✓ Soubor přesunut: $dst</p>";
            } else {
                echo "<p style='color: red'>✗ Chyba při přesunu souboru: $src</p>";
            }
        }
    }
}

// Na začátek souboru přidám sledování času
$start_time = microtime(true);

echo "<h1>Spouštím import dat</h1>";
echo "<p>Začátek importu: " . date('d.m.Y H:i:s') . "</p>";

// Kontrola existence export složky
if (!file_exists($export_dir)) {
    die("<p style='color: red'>✗ Složka s exportovanými daty nebyla nalezena!</p>");
}

// Kontrola existence SQL souboru
if (!file_exists($sql_file)) {
    die("<p style='color: red'>✗ SQL soubor nebyl nalezen!</p>");
}

// Vytvoření potřebných složek
createDirectories();

// Připojení k databázi
try {
    $db = connectDB($db_config);
    echo "<p style='color: green'>✓ Připojeno k databázi</p>";
    
    // Vypneme kontrolu cizích klíčů
    $db->query("SET FOREIGN_KEY_CHECKS=0");
} catch (Exception $e) {
    die("<p style='color: red'>✗ Chyba: " . $e->getMessage() . "</p>");
}

// Import SQL dat
echo "<h2>Import SQL dat</h2>";

// Resetování AUTO_INCREMENT hodnot a vyčištění tabulek
$reset_tables = [
    "SET FOREIGN_KEY_CHECKS=0;",
    "TRUNCATE TABLE users;",
    "TRUNCATE TABLE kategorie;",
    "TRUNCATE TABLE clanky;",
    "TRUNCATE TABLE clanky_kategorie;",
    "TRUNCATE TABLE views_clanku;",
    "TRUNCATE TABLE propagace;",
    "TRUNCATE TABLE socials;",
    "TRUNCATE TABLE user_social;",
    "TRUNCATE TABLE admin_access;",
    "ALTER TABLE clanky MODIFY COLUMN obsah LONGTEXT;",
    "ALTER TABLE users AUTO_INCREMENT = 1;",
    "ALTER TABLE kategorie AUTO_INCREMENT = 1;",
    "ALTER TABLE clanky AUTO_INCREMENT = 1;",
    "ALTER TABLE clanky_kategorie AUTO_INCREMENT = 1;",
    "ALTER TABLE views_clanku AUTO_INCREMENT = 1;",
    "ALTER TABLE propagace AUTO_INCREMENT = 1;",
    "ALTER TABLE socials AUTO_INCREMENT = 1;",
    "ALTER TABLE user_social AUTO_INCREMENT = 1;",
    "ALTER TABLE admin_access AUTO_INCREMENT = 1;",
    "SET FOREIGN_KEY_CHECKS=1;",
    "SET FOREIGN_KEY_CHECKS=0;"
];

foreach ($reset_tables as $query) {
    if (!$db->query($query)) {
        echo "<p style='color: red'>✗ Chyba při přípravě tabulek: " . $db->error . "</p>";
    }
}

// Import SQL dat
$sql_content = file_get_contents($sql_file);
$queries = explode(";\n", $sql_content);
$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        // Odstraníme přebytečné mezery a zalomení řádků, ale zachováme formát pro čitelnost
        $query = trim($query);
        
        try {
            if ($db->query($query)) {
                $success_count++;
            } else {
                $error_count++;
                echo "<p style='color: red'>✗ Chyba při vykonávání SQL příkazu: " . $db->error . "</p>";
                echo "<p style='color: red'>SQL příkaz: " . htmlspecialchars(substr($query, 0, 500)) . (strlen($query) > 500 ? '...' : '') . "</p>";
            }
        } catch (mysqli_sql_exception $e) {
            $error_count++;
            echo "<p style='color: red'>✗ SQL chyba: " . $e->getMessage() . "</p>";
            echo "<p style='color: red'>SQL příkaz: " . htmlspecialchars(substr($query, 0, 500)) . (strlen($query) > 500 ? '...' : '') . "</p>";
        }
    }
}

// Zapneme kontrolu cizích klíčů zpět
$db->query("SET FOREIGN_KEY_CHECKS=1");

echo "<p>Úspěšně vykonáno: $success_count SQL příkazů</p>";
if ($error_count > 0) {
    echo "<p style='color: red'>Chyby při vykonávání: $error_count SQL příkazů</p>";
}

// Kopírování souborů
echo "<h2>Kopírování souborů</h2>";

// Kopírování audio souborů
$audio_files = glob($export_dir . '/audio/*.mp3');
foreach ($audio_files as $file) {
    $filename = basename($file);
    $new_path = $new_audio_path . $filename;
    if (copy($file, $new_path)) {
        echo "<p style='color: green'>✓ Audio soubor zkopírován: $filename</p>";
    } else {
        echo "<p style='color: red'>✗ Chyba při kopírování audio souboru: $filename</p>";
    }
}

// Kopírování náhledových obrázků
$thumbnail_files = glob($export_dir . '/thumbnails/velke/*');
foreach ($thumbnail_files as $file) {
    $filename = basename($file);
    $new_path = $new_thumbnails_path . $filename;
    if (copy($file, $new_path)) {
        echo "<p style='color: green'>✓ Velký náhled zkopírován: $filename</p>";
    } else {
        echo "<p style='color: red'>✗ Chyba při kopírování velkého náhledu: $filename</p>";
    }
}

$thumbnail_small_files = glob($export_dir . '/thumbnails/male/*');
foreach ($thumbnail_small_files as $file) {
    $filename = basename($file);
    $new_path = $new_thumbnails_small_path . $filename;
    if (copy($file, $new_path)) {
        echo "<p style='color: green'>✓ Malý náhled zkopírován: $filename</p>";
    } else {
        echo "<p style='color: red'>✗ Chyba při kopírování malého náhledu: $filename</p>";
    }
}

// Kopírování profilových fotek
$profile_files = glob($export_dir . '/users/thumbnails/*');
foreach ($profile_files as $file) {
    $filename = basename($file);
    $new_path = $new_profile_photos_path . $filename;
    if (copy($file, $new_path)) {
        echo "<p style='color: green'>✓ Profilová fotka zkopírována: $filename</p>";
    } else {
        echo "<p style='color: red'>✗ Chyba při kopírování profilové fotky: $filename</p>";
    }
}

// Zavření připojení
$db->close();

// Na konec souboru přidám výpis času a doby běhu
$end_time = microtime(true);
$execution_time = ($end_time - $start_time);

echo "<h2>Import dokončen</h2>";
echo "<p style='color: green'>✓ Data byla úspěšně importována</p>";
echo "<p>Konec importu: " . date('d.m.Y H:i:s') . "</p>";
echo "<p>Celková doba běhu: " . round($execution_time, 2) . " sekund</p>";
echo "<p>Zkontrolujte prosím, zda:</p>";
echo "<ul>";
echo "<li>Všechny SQL příkazy byly úspěšně vykonány</li>";
echo "<li>Všechny soubory byly správně přesunuty</li>";
echo "<li>Obrázky v článcích jsou správně zobrazeny</li>";
echo "<li>Audio soubory jsou dostupné</li>";
echo "<li>Profilové fotky uživatelů jsou viditelné</li>";
echo "</ul>";
?> 