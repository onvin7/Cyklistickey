<?php
/**
 * Skript pro přejmenování audio souborů podle ID článku ze staré DB
 * - Načte všechny články ze staré DB
 * - Pro každý článek najde název audio souboru v tabulce audio
 * - Najde soubor na /uploads/audio/{nazev_souboru}
 * - Přejmenuje ho na {id_clanku}.mp3
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

// Cesta k log souboru
$log_file = dirname(__DIR__) . '/logs/migrate_audio_from_db.log';
@mkdir(dirname($log_file), 0755, true);

// Funkce pro logování
function log_zprava($text) {
    global $log_file;
    if (!$log_file) {
        return;
    }
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $text\n";
    @file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Funkce pro výpis zpráv
function zprava($text, $log_file = null) {
    global $log_file;
    if ($log_file === null) {
        $log_file = $GLOBALS['log_file'] ?? null;
    }
    
    // Výpis na obrazovku
    echo $text . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
    if (php_sapi_name() !== 'cli') {
        flush();
        if (ob_get_level() > 0) {
            ob_flush();
        }
    }
    
    // Logování do souboru
    log_zprava(strip_tags($text));
}

// ============================================================================
// KONFIGURACE
// ============================================================================

// Konfigurace STARÉ databáze (zdroj dat)
$old_db_config = [
    'host' => 'md396.wedos.net',
    'username' => 'w340619_clanky',
    'password' => 'bqsUuxcr',
    'database' => 'd340619_clanky'
];

// Funkce pro připojení k databázi
function connectDB($config, $label) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
        zprava("Připojování k databázi $label...");
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        $pdo->exec("SET NAMES 'utf8mb4'");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET SESSION collation_connection = 'utf8mb4_general_ci'");
        
        zprava("✓ Připojení k databázi $label úspěšné.");
        return $pdo;
    } catch (PDOException $e) {
        zprava("❌ Chyba připojení k databázi $label:");
        zprava("  " . $e->getMessage());
        die();
    }
}

// Cesta k audio souborům
$base_path = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__ . '/..';
$audio_path = $base_path . '/web/uploads/audio/';

// Parametry
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0; // 0 = všechny
$start_id = isset($_GET['start_id']) ? (int)$_GET['start_id'] : 0; // Od kterého ID začít

// Začátek logování
zprava("=== 🎵 Přejmenování audio souborů podle ID článku ze staré DB ===\n");
zprava("Parametry: limit=$limit, start_id=$start_id");

// Připojení ke staré databázi
$pdo_old = connectDB($old_db_config, 'STARÁ DB');

// Zkontrolovat, zda složka existuje
if (!is_dir($audio_path)) {
    zprava("❌ Složka neexistuje: $audio_path");
    zprava("💡 Vytvoř složku a přesuň tam audio soubory ručně.");
    exit;
}

zprava("📁 Cílová složka: $audio_path");
zprava("");

// ============================================================================
// NAČTENÍ ČLÁNKŮ S AUDIO ZE STARÉ DB
// ============================================================================

zprava("🔍 Načítám články s audio soubory ze staré DB...");

try {
    // Načíst všechny články, které mají audio soubor
    $sql = "
        SELECT DISTINCT c.id AS id_clanku, a.nazev_souboru
        FROM clanky c
        INNER JOIN audio a ON c.id = a.id_clanku
        WHERE a.id_clanku IS NOT NULL 
        AND a.id_clanku > 0
        AND a.nazev_souboru IS NOT NULL
        AND a.nazev_souboru != ''
    ";
    
    if ($start_id > 0) {
        $sql .= " AND c.id >= :start_id";
    }
    
    $sql .= " ORDER BY c.id ASC";
    
    $stmt = $pdo_old->prepare($sql);
    if ($start_id > 0) {
        $stmt->execute([':start_id' => $start_id]);
    } else {
        $stmt->execute();
    }
    
    $clanky_audio = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = count($clanky_audio);
    
    zprava("✓ Načteno $total článků s audio soubory.");
    log_zprava("Načteno $total článků s audio soubory ze staré DB");
    
    if ($total == 0) {
        zprava("⚠️ Žádné články s audio soubory k zpracování.");
        exit;
    }
    
    // Omezit počet, pokud je zadán limit (před zobrazením tabulky)
    $clanky_audio_to_process = $clanky_audio;
    if ($limit > 0 && $total > $limit) {
        $clanky_audio_to_process = array_slice($clanky_audio, 0, $limit);
        zprava("⚠️ Zpracováno bude jen prvních " . $limit . " článků (kvůli limitu).");
    }
    
    // Zobrazit kompletní tabulku se všemi záznamy
    zprava("");
    zprava("📋 Kompletní přehled všech záznamů k zpracování:");
    zprava("");
    
    // HTML tabulka
    if (php_sapi_name() !== 'cli') {
        echo '<style>
            table.migrate-table {
                border-collapse: collapse;
                width: 100%;
                max-width: 1200px;
                margin: 20px 0;
                font-family: Arial, sans-serif;
                font-size: 14px;
            }
            table.migrate-table th {
                background-color: #4CAF50;
                color: white;
                padding: 12px;
                text-align: left;
                border: 1px solid #ddd;
            }
            table.migrate-table td {
                padding: 10px;
                border: 1px solid #ddd;
            }
            table.migrate-table tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            table.migrate-table tr:hover {
                background-color: #e8f5e9;
            }
            .table-container {
                max-height: 600px;
                overflow-y: auto;
                border: 1px solid #ddd;
                margin: 20px 0;
            }
        </style>';
        echo '<div class="table-container">';
        echo '<table class="migrate-table">';
        echo '<thead><tr><th>ID článku</th><th>Název souboru</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($clanky_audio_to_process as $item) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['id_clanku']) . '</td>';
            echo '<td>' . htmlspecialchars($item['nazev_souboru']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        // CLI výstup - jednoduchá tabulka
        zprava("ID článku | Název souboru");
        zprava(str_repeat("-", 80));
        foreach ($clanky_audio_to_process as $item) {
            zprava(sprintf("%-10s | %s", $item['id_clanku'], $item['nazev_souboru']));
        }
    }
    
    zprava("");
    zprava("Celkem záznamů v tabulce: " . count($clanky_audio_to_process));
    zprava("");
    
    // Uložit omezený seznam pro zpracování
    $clanky_audio = $clanky_audio_to_process;
    
} catch (Exception $e) {
    zprava("❌ Chyba při načítání článků: " . $e->getMessage());
    exit;
}

// ============================================================================
// ZPRACOVÁNÍ SOUBORŮ
// ============================================================================

zprava("🚀 Začínám přejmenovávání...");
zprava("");
log_zprava("Začínám přejmenovávání souborů...");

$renamed = 0;
$skipped = 0;
$skipped_no_file = 0;
$errors = 0;
$already_correct = 0;

$processed = 0;
foreach ($clanky_audio as $index => $item) {
    if ($limit > 0 && $processed >= $limit) {
        break;
    }
    
    // Oddělovač
    if ($index > 0) {
        zprava("─────────────────────────────────────────────────────────");
    }
    
    $id_clanku = $item['id_clanku'];
    $nazev_souboru = $item['nazev_souboru'];
    
    zprava("📄 Zpracovávám článek ID: $id_clanku");
    zprava("   Název souboru v DB: $nazev_souboru");
    log_zprava("Zpracovávám článek ID: $id_clanku, soubor v DB: $nazev_souboru");
    
    // Vyčistit název souboru (odstranit cesty, pokud jsou tam)
    $nazev_souboru_clean = basename($nazev_souboru);
    
    // Zkusit najít soubor - zkusit různé varianty názvu
    $possible_names = [
        $nazev_souboru_clean,  // Přesný název z DB
        $nazev_souboru_clean . '.mp3',  // S příponou .mp3
        preg_replace('/\.(mp3|MP3|wav|WAV|m4a|M4A)$/i', '', $nazev_souboru_clean) . '.mp3',  // Bez přípony + .mp3
    ];
    
    $old_file = null;
    $old_file_found = null;
    
    foreach ($possible_names as $possible_name) {
        $test_path = $audio_path . $possible_name;
        if (file_exists($test_path)) {
            $old_file = $test_path;
            $old_file_found = $possible_name;
            break;
        }
    }
    
    if (!$old_file) {
        $skipped_no_file++;
        $skipped++;
        zprava("   ❌ Soubor nenalezen: $nazev_souboru_clean");
        zprava("   💡 Zkontroluj, zda soubor existuje ve složce: $audio_path");
        zprava("   ⏭️  Přeskočeno");
        log_zprava("CHYBA: Soubor nenalezen pro článek ID $id_clanku - hledaný název: $nazev_souboru_clean");
        continue;
    }
    
    zprava("   ✓ Soubor nalezen: $old_file_found");
    log_zprava("Soubor nalezen: $old_file_found pro článek ID $id_clanku");
    
    // Nový název: {id_clanku}.mp3
    $new_filename = $id_clanku . '.mp3';
    $new_filepath = $audio_path . $new_filename;
    
    // Pokud už má správný název, přeskočit
    if (basename($old_file) === $new_filename) {
        $already_correct++;
        zprava("   ✓ Soubor už má správný název: $new_filename");
        zprava("   ✅ Done (už správně pojmenován)");
        $processed++;
        continue;
    }
    
    // Přejmenovat soubor
    zprava("   🔄 Přejmenovávám na: $new_filename");
    
    // Pokud už existuje soubor s cílovým názvem, zkontrolovat
    if (file_exists($new_filepath)) {
        if (filesize($old_file) == filesize($new_filepath)) {
            // Stejná velikost - pravděpodobně stejný soubor
            zprava("   ⚠️ Soubor s názvem $new_filename už existuje (stejná velikost)");
            zprava("   🗑️  Mažu starý soubor: $old_file_found");
            if (@unlink($old_file)) {
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
    if (!is_writable($audio_path)) {
        $errors++;
        zprava("   ❌ Složka není zapisovatelná: $audio_path");
        zprava("   💡 Zkontroluj oprávnění složky (mělo by být 755 nebo 777)");
        continue;
    }
    
    if (!is_readable($old_file)) {
        $errors++;
        zprava("   ❌ Soubor není čitelný: $old_file");
        continue;
    }
    
    // Zkusit přejmenovat
    $rename_result = @rename($old_file, $new_filepath);
    
    if ($rename_result) {
        // Ověřit, že soubor skutečně existuje na nové cestě
        if (file_exists($new_filepath)) {
            zprava("   💾 Přejmenováno na: $new_filename");
            $renamed++;
            zprava("   ✅ Done");
            log_zprava("ÚSPĚCH: Přejmenováno z '$old_file_found' na '$new_filename' (článek ID: $id_clanku)");
        } else {
            $errors++;
            zprava("   ❌ Soubor se nepodařilo přejmenovat (soubor neexistuje na nové cestě)");
            log_zprava("CHYBA: Soubor se nepodařilo přejmenovat - neexistuje na nové cestě (článek ID: $id_clanku)");
        }
    } else {
        $errors++;
        $last_error = error_get_last();
        $error_msg = $last_error ? $last_error['message'] : 'Neznámá chyba';
        zprava("   ❌ Chyba při přejmenování: $error_msg");
        zprava("   💡 Zkontroluj oprávnění souboru a složky");
        log_zprava("CHYBA: Přejmenování selhalo pro článek ID $id_clanku - $error_msg");
    }
    
    $processed++;
    
    // Progress každých 10 souborů
    if ($processed % 10 == 0) {
        zprava("");
        zprava("   📊 Zpracováno $processed souborů...");
        zprava("");
    }
}

// ============================================================================
// VÝSLEDKY
// ============================================================================

zprava("");
zprava("─────────────────────────────────────────────────────────");
zprava("=== ✅ Dokončeno ===");
zprava("Celkem článků s audio: $total");
zprava("Přejmenováno: $renamed souborů");
zprava("Už správně pojmenováno: $already_correct souborů");
zprava("Přeskočeno: $skipped souborů (soubor nenalezen)");
zprava("Chyb: $errors");

// Logování výsledků
log_zprava("=== DOKONČENO ===");
log_zprava("Celkem článků s audio: $total");
log_zprava("Přejmenováno: $renamed souborů");
log_zprava("Už správně pojmenováno: $already_correct souborů");
log_zprava("Přeskočeno: $skipped souborů");
log_zprava("Chyb: $errors");
log_zprava(str_repeat("=", 80));

if ($renamed == 0 && $already_correct == 0 && $skipped > 0) {
    zprava("");
    zprava("⚠️ POZOR: Žádný soubor nebyl přejmenován!");
    zprava("   Možné příčiny:");
    zprava("   1. Soubory neexistují ve složce: $audio_path");
    zprava("   2. Názvy souborů v DB neodpovídají skutečným názvům souborů");
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

// Zobrazit informaci o pokračování
if (count($clanky_audio) > 0) {
    $last_id = end($clanky_audio)['id_clanku'];
    $first_id = reset($clanky_audio)['id_clanku'];
    
    if ($limit > 0 && $total > $limit) {
        $next_start_id = $last_id + 1;
        zprava("");
        zprava("📌 Zpracovány články ID: $first_id - $last_id (z celkem $total)");
        zprava("📌 Pro pokračování v migraci použij:");
        zprava("   ?start_id=$next_start_id&limit=$limit");
    } else {
        zprava("");
        zprava("📌 Zpracovány články ID: $first_id - $last_id");
        if ($total > 0 && $total == count($clanky_audio)) {
            zprava("✅ Všechny články v rozsahu byly zpracovány!");
            if ($start_id > 0) {
                $next_start_id = $last_id + 1;
                zprava("💡 Pro pokračování od ID $next_start_id použij:");
                zprava("   ?start_id=$next_start_id&limit=$limit");
            }
        }
    }
}

?>

