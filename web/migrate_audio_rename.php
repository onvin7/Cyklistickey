<?php
/**
 * Skript pro přejmenování audio souborů podle ID článku
 * Předpokládá, že audio soubory jsou už ručně přesunuty do /web/uploads/audio/
 * Skript je najde a přejmenuje na {id_clanku}.mp3
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

// Pro webový výstup - vypnout buffering pro průběžný výstup
if (php_sapi_name() !== 'cli') {
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
    echo $text . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
    if (php_sapi_name() !== 'cli') {
        flush();
        if (ob_get_level() > 0) {
            ob_flush();
        }
    }
}

// ============================================================================
// KONFIGURACE
// ============================================================================

// Připojení k databázi (použít stejnou konfiguraci jako migrate_db.php)
$new_db_config = [
    'host' => 'md413.wedos.net',
    'username' => 'w340619_blog',
    'password' => 'kaYak714?',
    'database' => 'd340619_blog'
];

try {
    $pdo = new PDO(
        "mysql:host={$new_db_config['host']};dbname={$new_db_config['database']};charset=utf8mb4",
        $new_db_config['username'],
        $new_db_config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    zprava("✓ Připojeno k databázi");
} catch (PDOException $e) {
    zprava("❌ Chyba připojení k databázi: " . $e->getMessage());
    exit;
}

// Cesta k audio souborům
$audio_path = $_SERVER['DOCUMENT_ROOT'] . '/web/uploads/audio/';

// Parametry
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0; // 0 = všechny

zprava("=== 🎵 Přejmenování audio souborů podle ID článku ===\n");

// Zkontrolovat, zda složka existuje
if (!is_dir($audio_path)) {
    zprava("❌ Složka neexistuje: $audio_path");
    zprava("💡 Vytvoř složku a přesuň tam audio soubory ručně.");
    exit;
}

zprava("📁 Cílová složka: $audio_path");
zprava("");

// Načíst všechny audio soubory ze složky
$audio_files = glob($audio_path . '*.{mp3,MP3,wav,WAV,m4a,M4A}', GLOB_BRACE);
$total_files = count($audio_files);

zprava("Našlo se $total_files audio souborů ve složce.");

if ($total_files == 0) {
    zprava("⚠️ Žádné audio soubory k přejmenování.");
    zprava("💡 Přesuň audio soubory do: $audio_path");
    exit;
}

// Zobrazit prvních 10 souborů pro kontrolu
zprava("");
zprava("📋 Prvních 10 souborů ve složce:");
foreach (array_slice($audio_files, 0, 10) as $file) {
    $size = filesize($file);
    zprava("   - " . basename($file) . " ($size bajtů)");
}
zprava("");

// Načíst mapování z tabulky audio (stará DB) - pokud existuje
zprava("");
zprava("🔍 Zjišťuji mapování souborů na ID článků...");

try {
    // Zkusit připojit ke staré DB pro mapování
    $old_db_config = [
        'host' => 'md396.wedos.net',
        'username' => 'w340619_clanky',
        'password' => 'bqsUuxcr',
        'database' => 'd340619_clanky'
    ];
    
    $pdo_old = new PDO(
        "mysql:host={$old_db_config['host']};dbname={$old_db_config['database']};charset=utf8mb4",
        $old_db_config['username'],
        $old_db_config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Načíst mapování: nazev_souboru -> id_clanku
    $stmt_map = $pdo_old->query("SELECT nazev_souboru, id_clanku FROM audio WHERE id_clanku IS NOT NULL AND id_clanku > 0");
    $file_map = [];
    while ($row = $stmt_map->fetch()) {
        $file_map[basename($row['nazev_souboru'])] = $row['id_clanku'];
    }
    
    zprava("✓ Načteno " . count($file_map) . " mapování ze staré DB.");
    
} catch (Exception $e) {
    zprava("⚠️ Nepodařilo se připojit ke staré DB: " . $e->getMessage());
    zprava("   Budu zkoušet extrahovat ID z názvu souboru...");
    $file_map = [];
}

zprava("");
zprava("🚀 Začínám přejmenovávání...");
zprava("");

$renamed = 0;
$skipped = 0;
$errors = 0;
$already_correct = 0;

$processed = 0;
foreach ($audio_files as $index => $file) {
    if ($limit > 0 && $processed >= $limit) {
        break;
    }
    
    // Oddělovač
    if ($index > 0) {
        zprava("─────────────────────────────────────────────────────────");
    }
    
    $filename = basename($file);
    $filepath = dirname($file);
    
    zprava("📄 Zpracovávám soubor: $filename");
    
    // Zkusit najít ID článku
    $id_clanku = null;
    
    // 1. Zkusit z mapování (stará DB)
    if (isset($file_map[$filename])) {
        $id_clanku = $file_map[$filename];
        zprava("   ✓ ID článku z mapování: $id_clanku");
    } else {
        // 2. Zkusit extrahovat z názvu souboru (pokud je tam číslo)
        if (preg_match('/(\d+)\.(mp3|MP3|wav|WAV|m4a|M4A)$/', $filename, $matches)) {
            $possible_id = (int)$matches[1];
            // Zkontrolovat, zda článek s tímto ID existuje
            $stmt_check = $pdo->prepare("SELECT id FROM clanky WHERE id = :id");
            $stmt_check->execute([':id' => $possible_id]);
            if ($stmt_check->fetch()) {
                $id_clanku = $possible_id;
                zprava("   ✓ ID článku z názvu souboru: $id_clanku");
            }
        }
        
        // 3. Pokud stále nevíme, zkusit najít podle názvu v tabulce audio
        if (!$id_clanku && !empty($file_map)) {
            // Hledat podobný název (bez přípony, case-insensitive)
            $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
            foreach ($file_map as $map_filename => $map_id) {
                $map_filename_no_ext = pathinfo($map_filename, PATHINFO_FILENAME);
                if (strcasecmp($filename_no_ext, $map_filename_no_ext) == 0) {
                    $id_clanku = $map_id;
                    zprava("   ✓ ID článku nalezeno podle podobného názvu: $id_clanku");
                    break;
                }
            }
        }
    }
    
    if (!$id_clanku) {
        $skipped++;
        zprava("   ❌ Nepodařilo se zjistit ID článku pro: $filename");
        zprava("   💡 Zkus:");
        zprava("      - Přejmenovat soubor tak, aby obsahoval ID článku (např. 123.mp3 nebo audio_123.mp3)");
        zprava("      - Nebo zkontrolovat, zda soubor existuje v tabulce audio ve staré DB");
        zprava("   ⏭️  Přeskočeno");
        continue;
    }
    
    // Zkontrolovat, zda článek existuje v nové DB
    $stmt_check = $pdo->prepare("SELECT id FROM clanky WHERE id = :id");
    $stmt_check->execute([':id' => $id_clanku]);
    if (!$stmt_check->fetch()) {
        $skipped++;
        zprava("   ❌ Článek ID $id_clanku neexistuje v nové DB");
        zprava("   ⏭️  Přeskočeno");
        continue;
    }
    
    // Nový název: {id_clanku}.mp3
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $new_filename = $id_clanku . '.mp3';
    $new_filepath = $filepath . '/' . $new_filename;
    
    // Pokud už má správný název, přeskočit
    if ($filename === $new_filename) {
        $already_correct++;
        zprava("   ✓ Soubor už má správný název: $new_filename");
        zprava("   ✅ Done (už správně pojmenován)");
        $processed++;
        continue;
    }
    
    // Přejmenovat soubor
    zprava("   📁 Mám soubor: $filename");
    zprava("   🔄 Přejmenovávám na: $new_filename");
    
    // Pokud už existuje soubor s cílovým názvem, přeskočit nebo přepsat?
    if (file_exists($new_filepath)) {
        if (filesize($file) == filesize($new_filepath)) {
            // Stejná velikost - pravděpodobně stejný soubor
            zprava("   ⚠️ Soubor s názvem $new_filename už existuje (stejná velikost)");
            zprava("   🗑️  Mažu starý soubor: $filename");
            if (@unlink($file)) {
                $already_correct++;
                zprava("   ✅ Done (starý soubor smazán, cílový už existuje)");
            } else {
                $errors++;
                zprava("   ❌ Chyba při mazání starého souboru");
            }
            $processed++;
            continue;
        } else {
            // Různá velikost - přepsat?
            zprava("   ⚠️ Soubor s názvem $new_filename už existuje (jiná velikost)");
            zprava("   💾 Přepisuji existující soubor...");
            @unlink($new_filepath);
        }
    }
    
    // Zkontrolovat oprávnění
    if (!is_writable($filepath)) {
        $errors++;
        zprava("   ❌ Složka není zapisovatelná: $filepath");
        zprava("   💡 Zkontroluj oprávnění složky (mělo by být 755 nebo 777)");
        continue;
    }
    
    if (!is_readable($file)) {
        $errors++;
        zprava("   ❌ Soubor není čitelný: $file");
        continue;
    }
    
    // Zkusit přejmenovat
    $rename_result = @rename($file, $new_filepath);
    
    if ($rename_result) {
        // Ověřit, že soubor skutečně existuje na nové cestě
        if (file_exists($new_filepath)) {
            zprava("   💾 Přejmenováno na: $new_filepath");
            
            // Aktualizovat DB
            $db_updated = false;
            try {
                $stmt_update = $pdo->prepare("UPDATE clanky SET audio_file = :audio_file WHERE id = :id");
                $stmt_update->execute([
                    ':id' => $id_clanku,
                    ':audio_file' => $new_filename
                ]);
                $db_updated = true;
                zprava("   💾 DB aktualizována (audio_file = $new_filename)");
            } catch (PDOException $e) {
                try {
                    $stmt_update = $pdo->prepare("UPDATE clanky SET audio = :audio WHERE id = :id");
                    $stmt_update->execute([
                        ':id' => $id_clanku,
                        ':audio' => $new_filename
                    ]);
                    $db_updated = true;
                    zprava("   💾 DB aktualizována (audio = $new_filename)");
                } catch (PDOException $e2) {
                    zprava("   ⚠️ Pole audio/audio_file neexistuje v DB (soubor přejmenován, DB bez aktualizace)");
                }
            }
            
            $renamed++;
            zprava("   ✅ Done");
        } else {
            $errors++;
            zprava("   ❌ Soubor se nepodařilo přejmenovat (soubor neexistuje na nové cestě)");
        }
    } else {
        $errors++;
        $last_error = error_get_last();
        $error_msg = $last_error ? $last_error['message'] : 'Neznámá chyba';
        zprava("   ❌ Chyba při přejmenování: $error_msg");
        zprava("   💡 Zkontroluj oprávnění souboru a složky");
    }
    
    $processed++;
    
    // Progress každých 10 souborů
    if ($processed % 10 == 0) {
        zprava("");
        zprava("   📊 Zpracováno $processed souborů...");
        zprava("");
    }
}

zprava("");
zprava("─────────────────────────────────────────────────────────");
zprava("=== ✅ Dokončeno ===");
zprava("Celkem souborů: $total_files");
zprava("Přejmenováno: $renamed souborů");
zprava("Už správně pojmenováno: $already_correct souborů");
zprava("Přeskočeno: $skipped souborů (nelze zjistit ID článku nebo článek neexistuje)");
zprava("Chyb: $errors");

if ($renamed == 0 && $already_correct == 0 && $skipped > 0) {
    zprava("");
    zprava("⚠️ POZOR: Žádný soubor nebyl přejmenován!");
    zprava("   Možné příčiny:");
    zprava("   1. Soubory neobsahují ID článku v názvu");
    zprava("   2. Soubory nejsou v mapování ze staré DB");
    zprava("   3. Články s danými ID neexistují v nové DB");
    zprava("");
    zprava("💡 Tip: Přejmenuj soubory tak, aby obsahovaly ID článku:");
    zprava("   - audio_123.mp3 → 123.mp3");
    zprava("   - nebo 123.mp3 (už správně)");
}

// Zobrazit aktuální stav složky
zprava("");
zprava("📁 Aktuální soubory ve složce po zpracování:");
$final_files = glob($audio_path . '*.{mp3,MP3,wav,WAV,m4a,M4A}', GLOB_BRACE);
if (count($final_files) > 0) {
    foreach (array_slice($final_files, 0, 10) as $final_file) {
        zprava("   - " . basename($final_file));
    }
    if (count($final_files) > 10) {
        zprava("   ... a dalších " . (count($final_files) - 10) . " souborů");
    }
} else {
    zprava("   (žádné soubory)");
}

if ($limit > 0 && $total_files > $limit) {
    $remaining = $total_files - $processed;
    zprava("");
    zprava("💡 Zbývá ještě $remaining souborů.");
    zprava("   Pro pokračování použij: ?limit=" . ($limit + $processed));
}

