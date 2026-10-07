<?php
/**
 * Migrační skript pro kopírování dat ze staré DB do nové DB
 * Stará DB zůstane nezměněná, data se pouze zkopírují
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
    // Vypnout kompresi, aby se výstup zobrazoval průběžně
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
// KONFIGURACE DATABÁZÍ
// ============================================================================

// Načtení přihlašovacích údajů
$credentialsFile = __DIR__ . '/../config/db_credentials.php';
if (file_exists($credentialsFile)) {
    require_once $credentialsFile;
}

if (php_sapi_name() === 'cli' && !$internalRun && isset($argv) && is_array($argv)) {
    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            zprava("Použití: php migrate_db.php --step=1..10|all [--min_id=] [--max_id=] [--start_id=] [--limit=]");
            exit(0);
        }
        if (strpos($arg, '--') === 0) {
            $pair = explode('=', substr($arg, 2), 2);
            $k = (string)($pair[0] ?? '');
            $v = (string)($pair[1] ?? '1');
            if ($k !== '') {
                $_GET[$k] = $v;
            }
        }
    }
}

$uploadsBase = __DIR__ . '/uploads';

// Konfigurace STARÉ databáze (zdroj dat)
$old_db_config = [
    'host' => defined('OLD_DB_HOST') ? OLD_DB_HOST : '',
    'username' => defined('OLD_DB_USER') ? OLD_DB_USER : '',
    'password' => defined('OLD_DB_PASS') ? OLD_DB_PASS : '',
    'database' => defined('OLD_DB_NAME') ? OLD_DB_NAME : ''
];

// Konfigurace NOVÉ databáze (cíl migrace)
$new_db_config = [
    'host' => defined('DB_HOST') ? DB_HOST : '',
    'username' => defined('DB_USER') ? DB_USER : '',
    'password' => defined('DB_PASS') ? DB_PASS : '',
    'database' => defined('DB_NAME') ? DB_NAME : ''
];

// Cesty k HTML souborům s obsahem článků (zkusí více možností, v tomto pořadí).
// Starý web má soubory typu: .../subdom/magazin/assets/html/clanek_{ID}.php
//
// Volitelně v config/db_credentials.php nastav:
//   define('OLD_HTML_BASE_PATH', 'C:\\cesta\\ke\\Cyklistickey-final\\subdom\\magazin\\assets\\html');
// (bez koncového lomítka; skript přidává prefix "clanek_" + ID + přípona)
// Na produkci Wedos často funguje jen absolutní cesta níže + HTTP z magazinu.
$old_html_paths = [];

if (defined('OLD_HTML_BASE_PATH') && is_string(OLD_HTML_BASE_PATH) && OLD_HTML_BASE_PATH !== '') {
    $base = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, OLD_HTML_BASE_PATH), DIRECTORY_SEPARATOR);
    $old_html_paths[] = $base . DIRECTORY_SEPARATOR . 'clanek_';
}

// Produkční absolutní cesta (stejná struktura jako lokální repo Cyklistickey-final/subdom/magazin/...)
$old_html_paths[] = '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/html/clanek_';

$oldMagazinBase = defined('OLD_MAGAZIN_PUBLIC_URL') && is_string(OLD_MAGAZIN_PUBLIC_URL) && OLD_MAGAZIN_PUBLIC_URL !== ''
    ? rtrim(OLD_MAGAZIN_PUBLIC_URL, '/')
    : 'https://www.magazin.cyklistickey.cz';
$old_html_paths[] = $oldMagazinBase . '/assets/html/clanek_';

$old_html_paths = array_values(array_unique($old_html_paths));

// Který krok se má spustit (1-10, nebo 'all' pro všechny)
$step = isset($_GET['step']) ? $_GET['step'] : 'all';

// Filtrování článků podle ID (pro pokračování od určitého ID)
$min_id = isset($_GET['min_id']) ? (int)$_GET['min_id'] : 0;
$max_id = isset($_GET['max_id']) ? (int)$_GET['max_id'] : 0;

// Start ID - od kterého ID začít zpracovávat (použije se místo min_id, pokud je zadán)
$start_id = isset($_GET['start_id']) ? (int)$_GET['start_id'] : 0;
if ($start_id > 0) {
    $min_id = $start_id; // Přepsat min_id, pokud je zadán start_id
}

// Směr migrace pro články: asc (default) / desc (od nejnovějších)
$direction = isset($_GET['direction']) ? (string)$_GET['direction'] : 'asc';
$direction = strtolower(trim($direction));
if ($direction !== 'desc') {
    $direction = 'asc';
}

// Cursor pro sestupnou migraci (id <= cursor). Když není, vezme se max(id) ve staré DB.
$cursor_id = isset($_GET['cursor_id']) ? (int)$_GET['cursor_id'] : 0;

// Reset cílových tabulek (jen pro články a navázaná data).
// Použij jen při "wipe + import" scénáři: ?step=3&reset=1&direction=desc
$reset = isset($_GET['reset']) ? (int)$_GET['reset'] : 0;

// Limit počtu článků na jedno spuštění (pro vyhnutí se timeoutu)
$batch_limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;

// Funkce pro připojení k databázi
function connectDB($config, $label) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
        zprava("Připojování k databázi $label...");
        zprava("  Host: {$config['host']}");
        zprava("  Database: {$config['database']}");
        zprava("  Username: {$config['username']}");
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        $pdo->exec("SET NAMES 'utf8mb4'");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET SESSION collation_connection = 'utf8mb4_general_ci'");
        $pdo->exec("SET SESSION wait_timeout = 28800");
        $pdo->exec("SET SESSION interactive_timeout = 28800");
        // max_allowed_packet nelze nastavit na úrovni SESSION (je read-only)
        zprava("✓ Připojení k databázi $label úspěšné.");
        return $pdo;
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
        zprava("❌ Chyba připojení k databázi $label:");
        zprava("  " . $errorMsg);
        zprava("");
        zprava("Zkontrolujte:");
        zprava("  1. Správnost uživatelského jména a hesla");
        zprava("  2. Zda má uživatel oprávnění přistupovat z této IP adresy");
        zprava("  3. Zda je databáze dostupná");
        die();
    }
}

// Připojení k databázím
$pdo_old = connectDB($old_db_config, 'STARÁ DB');
$pdo_new = connectDB($new_db_config, 'NOVÁ DB');

// Vypnutí kontroly cizích klíčů pro rychlejší vkládání
$pdo_new->exec("SET FOREIGN_KEY_CHECKS=0");

// Mapování uživatelů (staré ID -> nové ID) pro kontrolu existencí
$user_id_map = [];

// ============================================================================
// KROK 1: KATEGORIE
// ============================================================================
if ($step == 'all' || $step == '1') {
    zprava("\n=== KROK 1: Migrace kategorií ===");
    
    try {
        // Načtení kategorií ze staré DB
        $stmt_old = $pdo_old->query("SELECT id, nazev_kategorie, url FROM kategorie ORDER BY id");
        $kategorie = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        zprava("Načteno " . count($kategorie) . " kategorií ze staré DB.");
        
        $stmt_new = $pdo_new->prepare("
            INSERT INTO kategorie (id, nazev_kategorie, url) 
            VALUES (:id, :nazev_kategorie, :url)
            ON DUPLICATE KEY UPDATE 
                nazev_kategorie = VALUES(nazev_kategorie),
                url = VALUES(url)
        ");
        
        $inserted = 0;
        $updated = 0;
        
        foreach ($kategorie as $kat) {
            try {
                $stmt_new->execute([
                    ':id' => $kat['id'],
                    ':nazev_kategorie' => $kat['nazev_kategorie'],
                    ':url' => $kat['url']
                ]);
                
                if ($stmt_new->rowCount() > 0) {
                    if ($stmt_new->rowCount() == 1) {
                        $inserted++;
                    } else {
                        $updated++;
                    }
                }
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u kategorie ID {$kat['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Kategorie: $inserted nových, $updated aktualizovaných.");
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci kategorií: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 2: UŽIVATELÉ
// ============================================================================
if ($step == 'all' || $step == '2') {
    zprava("\n=== KROK 2: Migrace uživatelů ===");
    
    try {
        // Načtení uživatelů ze staré DB
        $stmt_old = $pdo_old->query("
            SELECT id, email, heslo, admin, name, surname, profil_foto, popis, datum 
            FROM users 
            ORDER BY id
        ");
        $users = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        zprava("Načteno " . count($users) . " uživatelů ze staré DB.");
        
        $stmt_new = $pdo_new->prepare("
            INSERT INTO users (id, email, heslo, role, name, surname, profil_foto, popis, datum) 
            VALUES (:id, :email, :heslo, :role, :name, :surname, :profil_foto, :popis, :datum)
            ON DUPLICATE KEY UPDATE 
                email = VALUES(email),
                heslo = VALUES(heslo),
                role = VALUES(role),
                name = VALUES(name),
                surname = VALUES(surname),
                profil_foto = VALUES(profil_foto),
                popis = VALUES(popis),
                datum = VALUES(datum)
        ");
        
        $inserted = 0;
        $updated = 0;
        
        foreach ($users as $user) {
            try {
                // Profil_foto se zpracuje v kroku 9, tady nechat prázdné
                $stmt_new->execute([
                    ':id' => $user['id'],
                    ':email' => $user['email'],
                    ':heslo' => $user['heslo'],
                    ':role' => $user['admin'], // admin -> role
                    ':name' => $user['name'],
                    ':surname' => $user['surname'],
                    ':profil_foto' => '', // Zpracuje se v kroku 9 (sloupec je NOT NULL)
                    ':popis' => $user['popis'],
                    ':datum' => $user['datum']
                ]);
                
                // Uložení do mapy pro pozdější kontrolu
                $user_id_map[$user['id']] = $user['id'];
                
                if ($stmt_new->rowCount() > 0) {
                    if ($stmt_new->rowCount() == 1) {
                        $inserted++;
                    } else {
                        $updated++;
                    }
                }
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u uživatele ID {$user['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Uživatelé: $inserted nových, $updated aktualizovaných.");
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci uživatelů: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 3: ČLÁNKY
// ============================================================================
if ($step == 'all' || $step == '3') {
    zprava("\n=== KROK 3: Migrace článků ===");
    
    try {
        // Stará DB často nemá sloupec obsah (byl jen v HTML souborech) — jinak celý SELECT spadne.
        $hasObsahColumn = false;
        try {
            $colStmt = $pdo_old->query("SHOW COLUMNS FROM clanky LIKE 'obsah'");
            $hasObsahColumn = $colStmt && $colStmt->rowCount() > 0;
        } catch (Exception $e) {
            $hasObsahColumn = false;
        }
        if (!$hasObsahColumn) {
            zprava("ℹ️ Tabulka clanky ve staré DB nemá sloupec obsah — obsah se bere z HTML / nahradí se po načtení.");
        }

        $obsahSelectPart = $hasObsahColumn ? ', obsah AS obsah_db' : ', CAST(NULL AS CHAR) AS obsah_db';

        // Volitelný reset cílových tabulek (wipe + import).
        // Reset proběhne jen na začátku sestupné migrace (cursor_id=0), aby se neopakoval při pokračování.
        if ($reset === 1 && $direction === 'desc' && $cursor_id <= 0) {
            zprava("🧹 RESET: mažu články a navázaná data v nové DB...");
            try {
                $pdo_new->exec("SET FOREIGN_KEY_CHECKS=0");
                // Pořadí: nejdřív závislé tabulky, pak clanky
                foreach (['link_clicks', 'views_clanku', 'propagace', 'clanky_kategorie', 'clanky'] as $t) {
                    try {
                        $pdo_new->exec("TRUNCATE TABLE `$t`");
                        zprava("  ✓ TRUNCATE $t");
                    } catch (Exception $e) {
                        // Tabulka nemusí existovat v každém schématu
                        zprava("  ⚠️ TRUNCATE $t přeskočeno: " . $e->getMessage());
                    }
                }
                $pdo_new->exec("SET FOREIGN_KEY_CHECKS=1");
                zprava("✅ RESET hotový.");
            } catch (Exception $e) {
                zprava("❌ RESET selhal: " . $e->getMessage());
            }
        }

        // Načtení batch článků ze staré DB.
        // - asc: od nejmenších k největším (původní režim, s min_id/start_id)
        // - desc: od největších k nejmenším (nový režim s cursor_id)
        $params = [];
        $conditions = [];
        if ($min_id > 0) {
            $conditions[] = "id >= :min_id";
            $params[':min_id'] = $min_id;
        }
        if ($max_id > 0) {
            $conditions[] = "id <= :max_id";
            $params[':max_id'] = $max_id;
        }

        if ($direction === 'desc') {
            if ($cursor_id <= 0) {
                // start from the newest article in old DB (within optional filters)
                $sqlMax = "SELECT MAX(id) FROM clanky";
                if (!empty($conditions)) {
                    $sqlMax .= " WHERE " . implode(" AND ", $conditions);
                }
                $stmtMax = $pdo_old->prepare($sqlMax);
                $stmtMax->execute($params);
                $cursor_id = (int)$stmtMax->fetchColumn();
            }
            if ($cursor_id > 0) {
                $conditions[] = "id <= :cursor_id";
                $params[':cursor_id'] = $cursor_id;
            }
        }

        $whereSql = !empty($conditions) ? (" WHERE " . implode(" AND ", $conditions)) : "";
        $orderSql = ($direction === 'desc') ? " ORDER BY id DESC" : " ORDER BY id ASC";
        $limitSql = ($batch_limit > 0) ? (" LIMIT " . (int)$batch_limit) : "";

        $sql = "
            SELECT id, nazev, datum, viditelnost, nahled_foto, user_id, url
            $obsahSelectPart
            FROM clanky
            $whereSql
            $orderSql
            $limitSql
        ";

        $stmt_old = $pdo_old->prepare($sql);
        $stmt_old->execute($params);
        $clanky = $stmt_old->fetchAll(PDO::FETCH_ASSOC);

        $total_clanky = count($clanky);
        zprava("Načteno " . $total_clanky . " článků ze staré DB (direction=$direction, cursor_id=$cursor_id, limit=$batch_limit).");
        
        $stmt_check_user = $pdo_new->prepare("SELECT id FROM users WHERE id = :user_id");
        $stmt_check_existing = $pdo_new->prepare("SELECT id FROM clanky WHERE id = :id");
        $stmt_insert = $pdo_new->prepare("
            INSERT INTO clanky (id, nazev, datum, viditelnost, nahled_foto, obsah, user_id, url) 
            VALUES (:id, :nazev, :datum, :viditelnost, :nahled_foto, :obsah, :user_id, :url)
        ");
        $stmt_update = $pdo_new->prepare("
            UPDATE clanky SET 
                nazev = :nazev,
                datum = :datum,
                viditelnost = :viditelnost,
                nahled_foto = :nahled_foto,
                obsah = :obsah,
                user_id = :user_id,
                url = :url
            WHERE id = :id
        ");
        
        $inserted = 0;
        $updated = 0;
        $unchanged = 0;
        $missing_html = 0;
        $invalid_user = 0;
        
        foreach ($clanky as $clanek) {
            try {
                // Kontrola existence user_id v nové DB
                $user_id = $clanek['user_id'];
                if ($user_id > 0) {
                    $stmt_check_user->execute([':user_id' => $user_id]);
                    if (!$stmt_check_user->fetch()) {
                        $user_id = 0; // Uživatel neexistuje, použít 0
                        $invalid_user++;
                    }
                }
                
                // Načtení HTML obsahu
                $obsahFromDb = (string)($clanek['obsah_db'] ?? '');
                $obsah = $obsahFromDb;
                $found = false;
                $tried_paths = [];
                
                // Zkusit všechny možné cesty a přípony
                $extensions = ['.php', '.html'];
                
                foreach ($old_html_paths as $base_path) {
                    foreach ($extensions as $ext) {
                        $html_file = $base_path . $clanek['id'] . $ext;
                        $tried_paths[] = $html_file;
                        
                        if (strpos($html_file, 'http') === 0) {
                            // URL - stáhnout přes HTTP
                            $context = stream_context_create([
                                'http' => [
                                    'timeout' => 5,
                                    'user_agent' => 'Mozilla/5.0',
                                    'ignore_errors' => true
                                ]
                            ]);
                            $obsah = @file_get_contents($html_file, false, $context);
                            if ($obsah !== false && strlen($obsah) > 0) {
                                $found = true;
                                if (($inserted + $updated + $unchanged) < 3) {
                                    zprava("  ✓ Načteno z: $html_file");
                                }
                                break 2; // Break z obou smyček
                            }
                        } else {
                            // Lokální soubor - zkusit file_exists jen pokud je v povolené cestě
                            try {
                                if (@file_exists($html_file)) {
                                    $obsah = @file_get_contents($html_file);
                                    if ($obsah !== false && strlen($obsah) > 0) {
                                        $found = true;
                                        if (($inserted + $updated + $unchanged) < 3) {
                                            zprava("  ✓ Načteno z: $html_file");
                                        }
                                        break 2; // Break z obou smyček
                                    }
                                }
                            } catch (Exception $e) {
                                // Ignorovat chyby open_basedir, zkusit další cestu
                                continue;
                            }
                        }
                    }
                }
                
                if (!$found) {
                    if (trim($obsahFromDb) === '') {
                        $missing_html++;
                        if ($missing_html <= 5) {
                            zprava("  ⚠️ HTML soubor pro článek ID {$clanek['id']} nenalezen");
                        }
                    } else {
                        if (($inserted + $updated + $unchanged) < 3) {
                            zprava("  ℹ️ HTML soubor pro článek ID {$clanek['id']} nenalezen, používám obsah ze staré DB.");
                        }
                    }
                }

                // Normalize old article-image paths to new uploads path.
                $obsah = str_replace(
                    [
                        'https://www.magazin.cyklistickey.cz/assets/img/upload/clanek_obsah/',
                        'http://www.magazin.cyklistickey.cz/assets/img/upload/clanek_obsah/',
                        '/assets/img/upload/clanek_obsah/',
                        'assets/img/upload/clanek_obsah/',
                    ],
                    '/uploads/articles/',
                    (string)$obsah
                );
                $obsah = str_replace('/web/uploads/articles/', '/uploads/articles/', $obsah);
                
                // Zkontrolovat, zda článek už existuje a načíst ho pro porovnání
                $stmt_check_existing = $pdo_new->prepare("SELECT id, nazev, datum, viditelnost, nahled_foto, obsah, user_id, url FROM clanky WHERE id = :id");
                $stmt_check_existing->execute([':id' => $clanek['id']]);
                $existing_article = $stmt_check_existing->fetch(PDO::FETCH_ASSOC);
                
                // Připravit data ze staré DB
                $nahled_foto_clean = !empty($clanek['nahled_foto']) ? basename($clanek['nahled_foto']) : null;
                
                $data = [
                    ':id' => $clanek['id'],
                    ':nazev' => $clanek['nazev'],
                    ':datum' => $clanek['datum'],
                    ':viditelnost' => $clanek['viditelnost'],
                    ':nahled_foto' => $nahled_foto_clean,
                    ':obsah' => $obsah,
                    ':user_id' => $user_id,
                    ':url' => $clanek['url']
                ];

                if ($existing_article) {
                    // Porovnání - zda se článek v novém webu liší od starého
                    $changed = false;
                    if ($existing_article['nazev'] !== $clanek['nazev']) $changed = true;
                    if ($existing_article['datum'] !== $clanek['datum']) $changed = true;
                    if ($existing_article['viditelnost'] != $clanek['viditelnost']) $changed = true;
                    if ($existing_article['nahled_foto'] !== $nahled_foto_clean) $changed = true;
                    // Pro obsah použijeme trim pro odstranění whitespace na koncích
                    if (trim($existing_article['obsah']) !== trim($obsah)) $changed = true;
                    if ($existing_article['user_id'] != $user_id) $changed = true;
                    if ($existing_article['url'] !== $clanek['url']) $changed = true;

                    if ($changed) {
                        $stmt_update->execute($data);
                        $updated++;
                        if ($updated < 5) {
                            zprava("  🔄 Článek ID {$clanek['id']} byl změněn - přepisuji");
                        }
                    } else {
                        $unchanged++;
                    }
                } else {
                    $stmt_insert->execute($data);
                    $inserted++;
                }
                
                // Progress každých 50 článků
                if (($inserted + $updated + $unchanged) % 50 == 0) {
                    zprava("  Zpracováno " . ($inserted + $updated + $unchanged) . " článků...");
                }
                
             } catch (PDOException $e) {
                 // Pokud se MySQL server odpojil, zkusit znovupřipojit
                 if (strpos($e->getMessage(), 'MySQL server has gone away') !== false || 
                     strpos($e->getMessage(), '2006') !== false) {
                    zprava("  ⚠️ MySQL server se odpojil u článku ID {$clanek['id']}, pokus o znovupřipojení...");
                    
                    // Zkusit znovupřipojit a zpracovat (max 3 pokusy)
                    $retry_success = false;
                    for ($retry = 0; $retry < 3; $retry++) {
                        try {
                            sleep(2); // Počkat 2 sekundy před znovupřipojením
                            $pdo_new = connectDB($new_db_config, 'NOVÁ DB');
                            $pdo_new->exec("SET FOREIGN_KEY_CHECKS=0");
                            
                            // Znovu připravit statementy
                            $stmt_check_existing = $pdo_new->prepare("SELECT id FROM clanky WHERE id = :id");
                            $stmt_insert = $pdo_new->prepare("
                                INSERT INTO clanky (id, nazev, datum, viditelnost, nahled_foto, obsah, user_id, url) 
                                VALUES (:id, :nazev, :datum, :viditelnost, :nahled_foto, :obsah, :user_id, :url)
                            ");
                            $stmt_update = $pdo_new->prepare("
                                UPDATE clanky SET 
                                    nazev = :nazev,
                                    datum = :datum,
                                    viditelnost = :viditelnost,
                                    nahled_foto = :nahled_foto,
                                    obsah = :obsah,
                                    user_id = :user_id,
                                    url = :url
                                WHERE id = :id
                            ");
                            
                            // Zkusit znovu zpracovat tento článek
                            $stmt_check_existing->execute([':id' => $clanek['id']]);
                            $exists = $stmt_check_existing->fetch();
                            
                            if ($exists) {
                                $stmt_update->execute($data);
                            } else {
                                $stmt_insert->execute($data);
                            }
                            
                            if ($exists) {
                                $updated++;
                            } else {
                                $inserted++;
                            }
                            
                            zprava("  ✓ Znovupřipojení úspěšné, článek ID {$clanek['id']} zpracován");
                            $retry_success = true;
                            break;
                        } catch (Exception $retry_e) {
                            if ($retry < 2) {
                                zprava("  ⚠️ Pokus " . ($retry + 1) . " selhal, zkouším znovu za 2 sekundy...");
                                continue;
                            } else {
                                zprava("  ❌ Znovupřipojení selhalo po 3 pokusech: " . $retry_e->getMessage());
                                
                                // Pokud je obsah příliš velký (> 60KB), zkusit uložit bez obsahu nebo zkrátit
                                if (strlen($obsah) > 60000) {
                                    zprava("  ⚠️ Obsah článku ID {$clanek['id']} je příliš velký (" . strlen($obsah) . " znaků), zkracuji na 60KB...");
                                    $obsah_short = substr($obsah, 0, 60000) . "\n\n[Obsah byl zkrácen kvůli limitu databáze]";
                                    $data[':obsah'] = $obsah_short;
                                    
                                    try {
                                        sleep(1);
                                        $pdo_new = connectDB($new_db_config, 'NOVÁ DB');
                                        $pdo_new->exec("SET FOREIGN_KEY_CHECKS=0");
                                        
                                        $stmt_check_existing = $pdo_new->prepare("SELECT id FROM clanky WHERE id = :id");
                                        $stmt_insert = $pdo_new->prepare("
                                            INSERT INTO clanky (id, nazev, datum, viditelnost, nahled_foto, obsah, user_id, url) 
                                            VALUES (:id, :nazev, :datum, :viditelnost, :nahled_foto, :obsah, :user_id, :url)
                                        ");
                                        $stmt_update = $pdo_new->prepare("
                                            UPDATE clanky SET 
                                                nazev = :nazev,
                                                datum = :datum,
                                                viditelnost = :viditelnost,
                                                nahled_foto = :nahled_foto,
                                                obsah = :obsah,
                                                user_id = :user_id,
                                                url = :url
                                            WHERE id = :id
                                        ");
                                        
                                        $stmt_check_existing->execute([':id' => $clanek['id']]);
                                        $exists = $stmt_check_existing->fetch();
                                        
                                        if ($exists) {
                                            $stmt_update->execute($data);
                                        } else {
                                            $stmt_insert->execute($data);
                                        }
                                        
                                        if ($exists) {
                                            $updated++;
                                        } else {
                                            $inserted++;
                                        }
                                        
                                        zprava("  ✓ Článek ID {$clanek['id']} uložen se zkráceným obsahem (" . strlen($obsah_short) . " znaků)");
                                        $retry_success = true;
                                    } catch (Exception $final_e) {
                                        zprava("  ❌ Ani zkrácený obsah se nepodařilo uložit: " . $final_e->getMessage());
                                    }
                                }
                                
                                if (!$retry_success) {
                                    zprava("  ⚠️ Přeskočuji článek ID {$clanek['id']}, pokračuji s dalším...");
                                }
                            }
                        }
                    }
                 } else {
                     zprava("⚠️ Chyba u článku ID {$clanek['id']}: " . $e->getMessage());
                 }
             }
        }
        
        zprava("✓ Články: $inserted nových, $updated aktualizovaných, $unchanged beze změny.");
        
        // Zobrazit informaci o pokračování, pokud běží batch režim
        if (count($clanky) > 0) {
            $last_id = end($clanky)['id'];
            $first_id = reset($clanky)['id'];
            
            if ($batch_limit > 0 && count($clanky) === $batch_limit) {
                zprava("");
                zprava("📌 Zpracovány články ID: $first_id - $last_id");
                zprava("📌 Pro pokračování v migraci použij:");
                if ($direction === 'desc') {
                    $next_cursor = (int)$last_id - 1;
                    if ($next_cursor < 0) $next_cursor = 0;
                    $q = "?step=3&direction=desc&cursor_id=$next_cursor&limit=$batch_limit";
                    if ($min_id > 0) $q .= "&min_id=$min_id";
                    if ($max_id > 0) $q .= "&max_id=$max_id";
                    if ($reset === 1) $q .= "&reset=0";
                    zprava("   $q");
                    zprava("MIGRATE_DB_STEP=3");
                    zprava("MIGRATE_DB_NEXT_CURSOR_ID=$next_cursor");
                    zprava("MIGRATE_DB_DONE=0");
                } else {
                    // ASC: pokračování přes start_id (původní)
                    $next_start_id = (int)$last_id + 1;
                    $q = "?step=3&direction=asc&start_id=$next_start_id&limit=$batch_limit";
                    if ($max_id > 0) $q .= "&max_id=$max_id";
                    zprava("   $q");
                    zprava("MIGRATE_DB_STEP=3");
                    zprava("MIGRATE_DB_NEXT_START_ID=$next_start_id");
                    zprava("MIGRATE_DB_DONE=0");
                }
            } else {
                zprava("");
                zprava("📌 Zpracovány články ID: $first_id - $last_id");
                if ($total_clanky > 0 && $total_clanky == count($clanky)) {
                    zprava("✅ Všechny články v rozsahu byly zpracovány!");
                }
                zprava("MIGRATE_DB_STEP=3");
                zprava("MIGRATE_DB_DONE=1");
            }
        }
        if ($missing_html > 0) {
            zprava("⚠️ $missing_html článků bez HTML obsahu.");
        }
        if ($invalid_user > 0) {
            zprava("⚠️ $invalid_user článků s neexistujícím user_id (nastaveno na 0).");
        }

        // Safety pass: ensure newest articles are present in target DB.
        // This protects against edge cases where batching/interruptions skip the tail.
        try {
            $tailLimit = 10;
            $tailObsahPart = $hasObsahColumn ? ', obsah AS obsah_db' : ', CAST(NULL AS CHAR) AS obsah_db';
            $tailSql = "
                SELECT id, nazev, datum, viditelnost, nahled_foto, user_id, url
                $tailObsahPart
                FROM clanky
            ";
            $tailParams = [];
            $tailConditions = [];
            if ($min_id > 0) {
                $tailConditions[] = "id >= :tail_min_id";
                $tailParams[':tail_min_id'] = $min_id;
            }
            if ($max_id > 0) {
                $tailConditions[] = "id <= :tail_max_id";
                $tailParams[':tail_max_id'] = $max_id;
            }
            if (!empty($tailConditions)) {
                $tailSql .= " WHERE " . implode(" AND ", $tailConditions);
            }
            $tailSql .= " ORDER BY id DESC LIMIT " . (int)$tailLimit;

            $stmt_tail = $pdo_old->prepare($tailSql);
            $stmt_tail->execute($tailParams);
            $tailRows = $stmt_tail->fetchAll(PDO::FETCH_ASSOC);

            $rescuedTail = 0;
            foreach ($tailRows as $tailArticle) {
                $stmt_check_existing->execute([':id' => $tailArticle['id']]);
                if ($stmt_check_existing->fetch()) {
                    continue;
                }

                $tailUserId = (int)$tailArticle['user_id'];
                if ($tailUserId > 0) {
                    $stmt_check_user->execute([':user_id' => $tailUserId]);
                    if (!$stmt_check_user->fetch()) {
                        $tailUserId = 0;
                    }
                }

                $tailObsahFromDb = (string)($tailArticle['obsah_db'] ?? '');
                $tailObsah = $tailObsahFromDb;
                $tailFound = false;
                $extensions = ['.php', '.html'];
                foreach ($old_html_paths as $base_path) {
                    foreach ($extensions as $ext) {
                        $html_file = $base_path . $tailArticle['id'] . $ext;
                        if (strpos($html_file, 'http') === 0) {
                            $context = stream_context_create([
                                'http' => [
                                    'timeout' => 5,
                                    'user_agent' => 'Mozilla/5.0',
                                    'ignore_errors' => true
                                ]
                            ]);
                            $tailObsah = @file_get_contents($html_file, false, $context);
                            if ($tailObsah !== false && strlen($tailObsah) > 0) {
                                $tailFound = true;
                                break 2;
                            }
                        } else {
                            try {
                                if (@file_exists($html_file)) {
                                    $tailObsah = @file_get_contents($html_file);
                                    if ($tailObsah !== false && strlen($tailObsah) > 0) {
                                        $tailFound = true;
                                        break 2;
                                    }
                                }
                            } catch (Exception $e) {
                                continue;
                            }
                        }
                    }
                }

                $tailNahledFotoClean = !empty($tailArticle['nahled_foto']) ? basename($tailArticle['nahled_foto']) : null;
                $tailObsah = str_replace(
                    [
                        'https://www.magazin.cyklistickey.cz/assets/img/upload/clanek_obsah/',
                        'http://www.magazin.cyklistickey.cz/assets/img/upload/clanek_obsah/',
                        '/assets/img/upload/clanek_obsah/',
                        'assets/img/upload/clanek_obsah/',
                    ],
                    '/uploads/articles/',
                    (string)$tailObsah
                );
                $tailObsah = str_replace('/web/uploads/articles/', '/uploads/articles/', $tailObsah);
                $stmt_insert->execute([
                    ':id' => $tailArticle['id'],
                    ':nazev' => $tailArticle['nazev'],
                    ':datum' => $tailArticle['datum'],
                    ':viditelnost' => $tailArticle['viditelnost'],
                    ':nahled_foto' => $tailNahledFotoClean,
                    ':obsah' => $tailObsah,
                    ':user_id' => $tailUserId,
                    ':url' => $tailArticle['url']
                ]);

                $inserted++;
                $rescuedTail++;
                if (!$tailFound && trim($tailObsahFromDb) === '') {
                    $missing_html++;
                }
            }

            if ($rescuedTail > 0) {
                zprava("🛟 Tail safety sync doplnil $rescuedTail nejnovějších článků, které chyběly v nové DB.");
            }
        } catch (Exception $e) {
            zprava("⚠️ Tail safety sync selhal: " . $e->getMessage());
        }
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci článků: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 4: VAZBY KATEGORIÍ A ČLÁNKŮ
// ============================================================================
if ($step == 'all' || $step == '4') {
    zprava("\n=== KROK 4: Migrace vazeb kategorií a článků ===");
    
    try {
        // Načtení vazeb ze staré DB (jen kategorie_clanku, ne podkategorie)
        $stmt_old = $pdo_old->query("
            SELECT id_clanku, id_kategorie 
            FROM kategorie_clanku 
            ORDER BY id
        ");
        $vazby = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        zprava("Načteno " . count($vazby) . " vazeb kategorií ze staré DB.");
        
        // Kontrola existence kategorií a článků
        $stmt_check_kategorie = $pdo_new->prepare("SELECT id FROM kategorie WHERE id = :id");
        $stmt_check_clanek = $pdo_new->prepare("SELECT id FROM clanky WHERE id = :id");
        
        // Kontrola existence vazby před vložením (prevence duplicit)
        $stmt_check_vazba = $pdo_new->prepare("
            SELECT id FROM clanky_kategorie 
            WHERE id_clanku = :id_clanku AND id_kategorie = :id_kategorie
        ");
        
        $stmt_new = $pdo_new->prepare("
            INSERT INTO clanky_kategorie (id_clanku, id_kategorie) 
            VALUES (:id_clanku, :id_kategorie)
        ");
        
        $inserted = 0;
        $skipped = 0;
        $duplicates = 0;
        
        foreach ($vazby as $vazba) {
            try {
                // Kontrola existence kategorie
                $stmt_check_kategorie->execute([':id' => $vazba['id_kategorie']]);
                if (!$stmt_check_kategorie->fetch()) {
                    $skipped++;
                    continue; // Kategorie neexistuje, přeskočit
                }
                
                // Kontrola existence článku
                $stmt_check_clanek->execute([':id' => $vazba['id_clanku']]);
                if (!$stmt_check_clanek->fetch()) {
                    $skipped++;
                    continue; // Článek neexistuje, přeskočit
                }
                
                // Kontrola, zda vazba už neexistuje (prevence duplicit)
                $stmt_check_vazba->execute([
                    ':id_clanku' => $vazba['id_clanku'],
                    ':id_kategorie' => $vazba['id_kategorie']
                ]);
                if ($stmt_check_vazba->fetch()) {
                    $duplicates++;
                    continue; // Vazba už existuje, přeskočit
                }
                
                $stmt_new->execute([
                    ':id_clanku' => $vazba['id_clanku'],
                    ':id_kategorie' => $vazba['id_kategorie']
                ]);
                
                $inserted++;
                
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u vazby článek {$vazba['id_clanku']} - kategorie {$vazba['id_kategorie']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Vazby: $inserted vloženo, $skipped přeskočeno (neexistující kategorie/články), $duplicates duplicit přeskočeno.");
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci vazeb: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 5: PROPAGACE
// ============================================================================
if ($step == 'all' || $step == '5') {
    zprava("\n=== KROK 5: Migrace propagace ===");
    
    try {
        // Načtení propagace ze staré DB
        $stmt_old = $pdo_old->query("
            SELECT id, id_clanku, datum 
            FROM propagace 
            ORDER BY id
        ");
        $propagace = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        zprava("Načteno " . count($propagace) . " propagací ze staré DB.");
        
        $stmt_new = $pdo_new->prepare("
            INSERT INTO propagace (id, id_clanku, user_id, zacatek, konec) 
            VALUES (:id, :id_clanku, :user_id, :zacatek, :konec)
            ON DUPLICATE KEY UPDATE 
                id_clanku = VALUES(id_clanku),
                user_id = VALUES(user_id),
                zacatek = VALUES(zacatek),
                konec = VALUES(konec)
        ");
        
        $inserted = 0;
        $updated = 0;
        
        foreach ($propagace as $prop) {
            try {
                $datum = new DateTime($prop['datum']);
                $konec = $datum->format('Y-m-d H:i:s');
                $zacatek = $datum->modify('-7 days')->format('Y-m-d H:i:s');
                
                $stmt_new->execute([
                    ':id' => $prop['id'],
                    ':id_clanku' => $prop['id_clanku'],
                    ':user_id' => 0, // Vždy 0
                    ':zacatek' => $zacatek,
                    ':konec' => $konec
                ]);
                
                if ($stmt_new->rowCount() > 0) {
                    if ($stmt_new->rowCount() == 1) {
                        $inserted++;
                    } else {
                        $updated++;
                    }
                }
                
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u propagace ID {$prop['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Propagace: $inserted nových, $updated aktualizovaných.");
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci propagace: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 6: ZOBRAZENÍ ČLÁNKŮ (views_clanku)
// ============================================================================
if ($step == 'all' || $step == '6') {
    zprava("\n=== KROK 6: Migrace zobrazení článků ===");
    
    try {
        // Načtení zobrazení ze staré DB
        $stmt_old = $pdo_old->query("
            SELECT id, id_clanku, pocet, datum 
            FROM views_clanku 
            ORDER BY id
        ");
        $views = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        zprava("Načteno " . count($views) . " záznamů zobrazení ze staré DB.");
        
        $stmt_new = $pdo_new->prepare("
            INSERT INTO views_clanku (id, id_clanku, pocet, datum) 
            VALUES (:id, :id_clanku, :pocet, :datum)
            ON DUPLICATE KEY UPDATE 
                id_clanku = VALUES(id_clanku),
                pocet = VALUES(pocet),
                datum = VALUES(datum)
        ");
        
        $inserted = 0;
        $updated = 0;
        $batch = 0;
        
        foreach ($views as $view) {
            try {
                $stmt_new->execute([
                    ':id' => $view['id'],
                    ':id_clanku' => $view['id_clanku'],
                    ':pocet' => $view['pocet'],
                    ':datum' => $view['datum']
                ]);
                
                if ($stmt_new->rowCount() > 0) {
                    if ($stmt_new->rowCount() == 1) {
                        $inserted++;
                    } else {
                        $updated++;
                    }
                }
                
                // Progress každých 1000 záznamů
                $batch++;
                if ($batch % 1000 == 0) {
                    zprava("  Zpracováno $batch záznamů...");
                }
                
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u zobrazení ID {$view['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Zobrazení: $inserted nových, $updated aktualizovaných.");
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci zobrazení: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 7: RESET HESEL (password_resets)
// ============================================================================
if ($step == 'all' || $step == '7') {
    zprava("\n=== KROK 7: Migrace resetů hesel ===");
    
    try {
        // Načtení resetů ze staré DB (jen nevypršelé)
        $stmt_old = $pdo_old->query("
            SELECT id, user_id, email, token, expires_at 
            FROM password_resets 
            WHERE expires_at >= NOW()
            ORDER BY id
        ");
        $resets = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        zprava("Načteno " . count($resets) . " nevypršelých resetů ze staré DB.");
        
        $stmt_check_user = $pdo_new->prepare("SELECT id FROM users WHERE id = :user_id");
        $stmt_new = $pdo_new->prepare("
            INSERT INTO password_resets (id, user_id, email, token, expires_at) 
            VALUES (:id, :user_id, :email, :token, :expires_at)
            ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                email = VALUES(email),
                token = VALUES(token),
                expires_at = VALUES(expires_at)
        ");
        
        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($resets as $reset) {
            try {
                // Kontrola existence user_id
                $stmt_check_user->execute([':user_id' => $reset['user_id']]);
                if (!$stmt_check_user->fetch()) {
                    $skipped++;
                    continue; // Uživatel neexistuje, přeskočit
                }
                
                $stmt_new->execute([
                    ':id' => $reset['id'],
                    ':user_id' => $reset['user_id'],
                    ':email' => $reset['email'],
                    ':token' => $reset['token'],
                    ':expires_at' => $reset['expires_at']
                ]);
                
                if ($stmt_new->rowCount() > 0) {
                    if ($stmt_new->rowCount() == 1) {
                        $inserted++;
                    } else {
                        $updated++;
                    }
                }
                
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u resetu ID {$reset['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Resety hesel: $inserted nových, $updated aktualizovaných, $skipped přeskočeno (neexistující uživatelé).");
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci resetů hesel: " . $e->getMessage());
    }
}

// Zapnutí kontroly cizích klíčů zpět (s kontrolou připojení)
try {
    $pdo_new->exec("SET FOREIGN_KEY_CHECKS=1");
} catch (PDOException $e) {
    // Pokud se server odpojil, znovupřipojit
    if (strpos($e->getMessage(), 'MySQL server has gone away') !== false || 
        strpos($e->getMessage(), '2006') !== false) {
        $pdo_new = connectDB($new_db_config, 'NOVÁ DB');
        $pdo_new->exec("SET FOREIGN_KEY_CHECKS=1");
    } else {
        throw $e;
    }
}

// ============================================================================
// KROK 8: OBRÁZKY ČLÁNKŮ (nahled_foto)
// ============================================================================
if ($step == 'all' || $step == '8') {
    zprava("\n=== KROK 8: Migrace obrázků článků (nahled_foto) ===");
    
    try {
        // Načtení článků se starou DB s nahled_foto
        $sql = "
            SELECT id, nahled_foto 
            FROM clanky 
            WHERE nahled_foto IS NOT NULL AND nahled_foto != ''
        ";
        
        // Přidat filtrování podle ID, pokud je zadáno
        $params = [];
        if ($min_id > 0 || $max_id > 0) {
            $conditions = [];
            if ($min_id > 0) {
                $conditions[] = "id >= :min_id";
                $params[':min_id'] = $min_id;
            }
            if ($max_id > 0) {
                $conditions[] = "id <= :max_id";
                $params[':max_id'] = $max_id;
            }
            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }
        }
        
        // Pokud je zadán start_id, použít ho místo min_id
        if ($start_id > 0) {
            $sql .= " AND id >= :start_id";
            $params[':start_id'] = $start_id;
        }
        
        $sql .= " ORDER BY id ASC";
        
        $stmt_old = $pdo_old->prepare($sql);
        $stmt_old->execute($params);
        $clanky = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        $total_clanky = count($clanky);
        zprava("Načteno " . $total_clanky . " článků s obrázky ze staré DB.");
        
        // Omezit počet článků na batch_limit, pokud je zadán
        if ($batch_limit > 0 && $total_clanky > $batch_limit) {
            $clanky = array_slice($clanky, 0, $batch_limit);
            zprava("⚠️ Zpracováno bude jen prvních " . $batch_limit . " článků (kvůli limitu).");
        }
        
        $stmt_new = $pdo_new->prepare("
            UPDATE clanky 
            SET nahled_foto = :nahled_foto 
            WHERE id = :id
        ");
        
        $updated = 0;
        $skipped = 0;
        
        foreach ($clanky as $clanek) {
            try {
                // Extrahovat jen název souboru z nahled_foto (pokud je tam celá cesta)
                $nahled_foto = $clanek['nahled_foto'];
                if (!empty($nahled_foto)) {
                    // Pokud obsahuje lomítko, extrahovat jen název souboru
                    if (strpos($nahled_foto, '/') !== false || strpos($nahled_foto, '\\') !== false) {
                        $nahled_foto = basename($nahled_foto);
                    }
                    
                    // Zkontrolovat, zda článek existuje v nové DB
                    $stmt_check = $pdo_new->prepare("SELECT id FROM clanky WHERE id = :id");
                    $stmt_check->execute([':id' => $clanek['id']]);
                    if ($stmt_check->fetch()) {
                        $stmt_new->execute([
                            ':id' => $clanek['id'],
                            ':nahled_foto' => $nahled_foto
                        ]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
                
                // Progress každých 50 záznamů
                if (($updated + $skipped) % 50 == 0) {
                    zprava("  Zpracováno " . ($updated + $skipped) . " obrázků...");
                }
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u článku ID {$clanek['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Obrázky článků: $updated aktualizovaných, $skipped přeskočeno (článek neexistuje).");
        
        // Zobrazit informaci o pokračování
        if (count($clanky) > 0) {
            $last_id = end($clanky)['id'];
            $first_id = reset($clanky)['id'];
            
            if ($batch_limit > 0 && $total_clanky > $batch_limit) {
                $next_start_id = $last_id + 1;
                zprava("");
                zprava("📌 Zpracovány obrázky článků ID: $first_id - $last_id (z celkem $total_clanky)");
                zprava("📌 Pro pokračování v migraci použij:");
                if ($max_id > 0) {
                    zprava("   ?step=8&start_id=$next_start_id&max_id=$max_id&limit=$batch_limit");
                } else {
                    zprava("   ?step=8&start_id=$next_start_id&limit=$batch_limit");
                }
                zprava("MIGRATE_DB_STEP=8");
                zprava("MIGRATE_DB_NEXT_START_ID=$next_start_id");
                zprava("MIGRATE_DB_DONE=0");
            } else {
                zprava("");
                zprava("📌 Zpracovány obrázky článků ID: $first_id - $last_id");
                if ($total_clanky > 0 && $total_clanky == count($clanky)) {
                    zprava("✅ Všechny obrázky článků v rozsahu byly zpracovány!");
                    if ($start_id > 0) {
                        $next_start_id = $last_id + 1;
                        zprava("💡 Pro pokračování od ID $next_start_id použij:");
                        zprava("   ?step=8&start_id=$next_start_id&limit=$batch_limit");
                    }
                }
                zprava("MIGRATE_DB_STEP=8");
                zprava("MIGRATE_DB_DONE=1");
            }
        }
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci obrázků článků: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 9: OBRÁZKY UŽIVATELŮ (profil_foto)
// ============================================================================
if ($step == 'all' || $step == '9') {
    zprava("\n=== KROK 9: Migrace obrázků uživatelů (profil_foto) ===");
    
    try {
        // Načtení uživatelů ze staré DB s profil_foto
        $sql = "
            SELECT id, profil_foto 
            FROM users 
            WHERE profil_foto IS NOT NULL AND profil_foto != ''
        ";
        
        // Přidat filtrování podle ID, pokud je zadáno
        $params = [];
        if ($min_id > 0 || $max_id > 0) {
            $conditions = [];
            if ($min_id > 0) {
                $conditions[] = "id >= :min_id";
                $params[':min_id'] = $min_id;
            }
            if ($max_id > 0) {
                $conditions[] = "id <= :max_id";
                $params[':max_id'] = $max_id;
            }
            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }
        }
        
        // Pokud je zadán start_id, použít ho místo min_id
        if ($start_id > 0) {
            $sql .= " AND id >= :start_id";
            $params[':start_id'] = $start_id;
        }
        
        $sql .= " ORDER BY id ASC";
        
        $stmt_old = $pdo_old->prepare($sql);
        $stmt_old->execute($params);
        $users = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        $total_users = count($users);
        zprava("Načteno " . $total_users . " uživatelů s obrázky ze staré DB.");
        
        // Omezit počet uživatelů na batch_limit, pokud je zadán
        if ($batch_limit > 0 && $total_users > $batch_limit) {
            $users = array_slice($users, 0, $batch_limit);
            zprava("⚠️ Zpracováno bude jen prvních " . $batch_limit . " uživatelů (kvůli limitu).");
        }

        // Cesty k profilovým fotkám - zkusit více možností
        $old_photo_paths = [
            '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/img/upload/profil_foto/', // Absolutní cesta
            'https://www.magazin.cyklistickey.cz/assets/img/upload/profil_foto/' // HTTP URL
        ];
        $new_photo_path = $uploadsBase . '/users/thumbnails/';
        
        zprava("📁 Nová cesta: $new_photo_path");
        
        // Zajistit, že nová složka existuje
        if (!is_dir($new_photo_path)) {
            if (mkdir($new_photo_path, 0777, true)) {
                zprava("✓ Vytvořena nová složka: $new_photo_path");
            } else {
                zprava("❌ Nepodařilo se vytvořit složku: $new_photo_path");
            }
        } else {
            zprava("✓ Nová složka existuje: $new_photo_path");
        }
        
        $stmt_new = $pdo_new->prepare("
            UPDATE users 
            SET profil_foto = :profil_foto 
            WHERE id = :id
        ");
        
        $updated = 0;
        $skipped = 0;
        $downloaded = 0;
        $missing_files = 0;
        
        foreach ($users as $user) {
            try {
                // Extrahovat jen název souboru z profil_foto (pokud je tam celá cesta)
                $profil_foto = $user['profil_foto'];
                if (!empty($profil_foto)) {
                    // Pokud obsahuje lomítko, extrahovat jen název souboru
                    if (strpos($profil_foto, '/') !== false || strpos($profil_foto, '\\') !== false) {
                        $profil_foto = basename($profil_foto);
                    }
                    
                    // Zkontrolovat, zda uživatel existuje v nové DB
                    $stmt_check = $pdo_new->prepare("SELECT id FROM users WHERE id = :id");
                    $stmt_check->execute([':id' => $user['id']]);
                    if ($stmt_check->fetch()) {
                        
                        // Stáhnout/zkopírovat soubor
                        $new_file = $new_photo_path . $profil_foto;
                        $file_copied = false;
                        
                        // Pokud soubor už existuje, nepřepisovat (nebo volitelně přepsat?)
                        if (file_exists($new_file) && filesize($new_file) > 0) {
                            $file_copied = true;
                            // zprava("  ✓ Soubor už existuje: $profil_foto");
                        } else {
                            foreach ($old_photo_paths as $old_path) {
                                $old_file = $old_path . $profil_foto;
                                
                                if (strpos($old_file, 'http') === 0) {
                                    // HTTP URL - stáhnout přes HTTP
                                    $context = stream_context_create([
                                        'http' => [
                                            'timeout' => 10,
                                            'user_agent' => 'Mozilla/5.0',
                                            'ignore_errors' => true
                                        ]
                                    ]);
                                    
                                    $file_content = @file_get_contents($old_file, false, $context);
                                    if ($file_content !== false && strlen($file_content) > 0) {
                                        if (@file_put_contents($new_file, $file_content)) {
                                            $file_copied = true;
                                            $downloaded++;
                                            break;
                                        }
                                    }
                                } else {
                                    // Lokální soubor
                                    try {
                                        if (@file_exists($old_file)) {
                                            if (@copy($old_file, $new_file)) {
                                                $file_copied = true;
                                                $downloaded++;
                                                break;
                                            }
                                        }
                                    } catch (Exception $e) {
                                        continue;
                                    }
                                }
                            }
                        }
                        
                        if ($file_copied) {
                            $stmt_new->execute([
                                ':id' => $user['id'],
                                ':profil_foto' => $profil_foto
                            ]);
                            $updated++;
                        } else {
                            $missing_files++;
                            zprava("  ⚠️ Soubor nenalezen: $profil_foto (User ID: {$user['id']})");
                        }
                    } else {
                        $skipped++;
                    }
                }
                
                // Progress každých 50 záznamů
                if (($updated + $skipped + $missing_files) % 50 == 0) {
                    zprava("  Zpracováno " . ($updated + $skipped + $missing_files) . " záznamů...");
                }
            } catch (PDOException $e) {
                zprava("⚠️ Chyba u uživatele ID {$user['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Obrázky uživatelů: $updated aktualizovaných (z toho $downloaded stažených), $missing_files nenalezeno, $skipped přeskočeno (uživatel neexistuje).");
        
        // Zobrazit informaci o pokračování
        if (count($users) > 0) {
            $last_id = end($users)['id'];
            $first_id = reset($users)['id'];
            
            if ($batch_limit > 0 && $total_users > $batch_limit) {
                $next_start_id = $last_id + 1;
                zprava("");
                zprava("📌 Zpracovány obrázky uživatelů ID: $first_id - $last_id (z celkem $total_users)");
                zprava("📌 Pro pokračování v migraci použij:");
                if ($max_id > 0) {
                    zprava("   ?step=9&start_id=$next_start_id&max_id=$max_id&limit=$batch_limit");
                } else {
                    zprava("   ?step=9&start_id=$next_start_id&limit=$batch_limit");
                }
                zprava("MIGRATE_DB_STEP=9");
                zprava("MIGRATE_DB_NEXT_START_ID=$next_start_id");
                zprava("MIGRATE_DB_DONE=0");
            } else {
                zprava("");
                zprava("📌 Zpracovány obrázky uživatelů ID: $first_id - $last_id");
                if ($total_users > 0 && $total_users == count($users)) {
                    zprava("✅ Všechny obrázky uživatelů v rozsahu byly zpracovány!");
                    if ($start_id > 0) {
                        $next_start_id = $last_id + 1;
                        zprava("💡 Pro pokračování od ID $next_start_id použij:");
                        zprava("   ?step=9&start_id=$next_start_id&limit=$batch_limit");
                    }
                }
                zprava("MIGRATE_DB_STEP=9");
                zprava("MIGRATE_DB_DONE=1");
            }
        }
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci obrázků uživatelů: " . $e->getMessage());
    }
}

// ============================================================================
// KROK 10: AUDIO SOUBORY
// ============================================================================
if ($step == 'all' || $step == '10') {
    zprava("\n=== KROK 10: Migrace audio souborů ===");
    
    try {
        // Načtení audio záznamů ze staré DB
        $sql = "
            SELECT id, nazev_souboru, id_clanku 
            FROM audio 
            WHERE id_clanku IS NOT NULL AND id_clanku > 0
        ";
        
        // Přidat filtrování podle id_clanku, pokud je zadáno
        $params = [];
        if ($min_id > 0 || $max_id > 0) {
            $conditions = [];
            if ($min_id > 0) {
                $conditions[] = "id_clanku >= :min_id";
                $params[':min_id'] = $min_id;
            }
            if ($max_id > 0) {
                $conditions[] = "id_clanku <= :max_id";
                $params[':max_id'] = $max_id;
            }
            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }
        }
        
        // Pokud je zadán start_id, použít ho místo min_id (filtrovat podle id_clanku)
        if ($start_id > 0) {
            $sql .= " AND id_clanku >= :start_id";
            $params[':start_id'] = $start_id;
        }
        
        $sql .= " ORDER BY id_clanku ASC";
        
        $stmt_old = $pdo_old->prepare($sql);
        $stmt_old->execute($params);
        $audio_records = $stmt_old->fetchAll(PDO::FETCH_ASSOC);
        
        $total_audio = count($audio_records);
        zprava("Načteno " . $total_audio . " audio záznamů ze staré DB.");
        
        if ($total_audio == 0) {
            zprava("⚠️ Žádné audio záznamy k zpracování!");
            zprava("   SQL dotaz: " . $sql);
            if (!empty($params)) {
                zprava("   Parametry: " . print_r($params, true));
            }
            zprava("");
            zprava("🔍 Zkouším zjistit, kolik je celkem audio záznamů v DB...");
            try {
                $stmt_count = $pdo_old->query("SELECT COUNT(*) as total FROM audio WHERE id_clanku IS NOT NULL AND id_clanku > 0");
                $count_result = $stmt_count->fetch(PDO::FETCH_ASSOC);
                zprava("   Celkem audio záznamů v DB: " . $count_result['total']);
                
                if ($count_result['total'] > 0) {
                    zprava("   Zobrazuji prvních 10 záznamů:");
                    $stmt_sample = $pdo_old->query("SELECT id, nazev_souboru, id_clanku FROM audio WHERE id_clanku IS NOT NULL AND id_clanku > 0 LIMIT 10");
                    $samples = $stmt_sample->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($samples as $sample) {
                        zprava("      - ID: {$sample['id']}, článek: {$sample['id_clanku']}, soubor: {$sample['nazev_souboru']}");
                    }
                }
            } catch (Exception $e) {
                zprava("   ❌ Chyba při kontrole: " . $e->getMessage());
            }
        } else {
            zprava("📋 Prvních 5 záznamů k zpracování:");
            foreach (array_slice($audio_records, 0, 5) as $sample) {
                zprava("   - Článek ID: {$sample['id_clanku']}, soubor: {$sample['nazev_souboru']}");
            }
        }
        
        // Omezit počet audio záznamů na batch_limit, pokud je zadán
        if ($batch_limit > 0 && $total_audio > $batch_limit) {
            $audio_records = array_slice($audio_records, 0, $batch_limit);
            zprava("⚠️ Zpracováno bude jen prvních " . $batch_limit . " audio záznamů (kvůli limitu).");
        }
        
        // Cesty k audio souborům - zkusit více možností
        $old_audio_paths = [
            '/data/web/virtuals/340619/virtual/www/subdom/magazin/assets/audio/', // Absolutní cesta
            'https://www.magazin.cyklistickey.cz/assets/audio/' // HTTP URL
        ];
        $new_audio_path = $uploadsBase . '/audio/';
        
        zprava("📁 Nová cesta: $new_audio_path");
        
        // Zajistit, že nová složka existuje
        if (!is_dir($new_audio_path)) {
            if (mkdir($new_audio_path, 0777, true)) {
                zprava("✓ Vytvořena nová složka: $new_audio_path");
            } else {
                zprava("❌ Nepodařilo se vytvořit složku: $new_audio_path");
            }
        } else {
            zprava("✓ Nová složka existuje: $new_audio_path");
        }
        
        $copied = 0;
        $skipped = 0;
        $skipped_no_article = 0;
        $skipped_no_file = 0;
        $errors = 0;
        
        foreach ($audio_records as $index => $audio) {
            try {
                $id_clanku = $audio['id_clanku'];
                $nazev_souboru = $audio['nazev_souboru'];
                
                // Oddělovač mezi články
                if ($index > 0) {
                    zprava("─────────────────────────────────────────────────────────");
                }
                zprava("📄 Načten článek ID: $id_clanku");
                
                // Zkontrolovat, zda článek existuje v nové DB
                $stmt_check = $pdo_new->prepare("SELECT id FROM clanky WHERE id = :id");
                $stmt_check->execute([':id' => $id_clanku]);
                if (!$stmt_check->fetch()) {
                    $skipped_no_article++;
                    $skipped++;
                    zprava("❌ Článek neexistuje v nové DB - přeskočeno");
                    continue;
                }
                
                // Nový název souboru: {id_clanku}.mp3
                $new_filename = $id_clanku . '.mp3';
                $new_file = $new_audio_path . $new_filename;
                
                // Zkusit najít a zkopírovat soubor z různých cest
                $file_copied = false;
                $old_file_found = null;
                
                foreach ($old_audio_paths as $old_audio_path) {
                    $old_file = $old_audio_path . $nazev_souboru;
                    
                    if (strpos($old_file, 'http') === 0) {
                        // HTTP URL - stáhnout přes HTTP
                        $context = stream_context_create([
                            'http' => [
                                'timeout' => 30,
                                'user_agent' => 'Mozilla/5.0',
                                'ignore_errors' => true
                            ]
                        ]);
                        
                        $file_content = @file_get_contents($old_file, false, $context);
                        if ($file_content !== false && strlen($file_content) > 0) {
                            if (@file_put_contents($new_file, $file_content)) {
                                $file_copied = true;
                                $old_file_found = $old_file;
                                break;
                            }
                        }
                    } else {
                        // Lokální soubor
                        try {
                            if (@file_exists($old_file)) {
                                if (@copy($old_file, $new_file)) {
                                    $file_copied = true;
                                    $old_file_found = $old_file;
                                    break;
                                }
                            }
                        } catch (Exception $e) {
                            // Ignorovat chyby open_basedir, zkusit další cestu
                            continue;
                        }
                    }
                }
                
                // Zkontrolovat, zda se soubor podařilo zkopírovat
                if ($file_copied && file_exists($new_file)) {
                    zprava("📁 Mám soubor: $nazev_souboru");
                    zprava("🔄 Přejmenoval jsem ho na: $new_filename");
                    zprava("💾 Zkopíroval jsem ho na: $new_file");
                    
                    // Aktualizovat DB
                    $db_updated = false;
                    try {
                        $audioUrl = '/uploads/audio/' . $new_filename;
                        $stmt_update = $pdo_new->prepare("UPDATE clanky SET audio = :audio WHERE id = :id");
                        $stmt_update->execute([
                            ':id' => $id_clanku,
                            ':audio' => $audioUrl
                        ]);
                        $db_updated = true;
                    } catch (PDOException $e) {
                        try {
                            $stmt_update = $pdo_new->prepare("UPDATE clanky SET audio_file = :audio_file WHERE id = :id");
                            $stmt_update->execute([
                                ':id' => $id_clanku,
                                ':audio_file' => $new_filename
                            ]);
                            $db_updated = true;
                        } catch (PDOException $e2) {
                            // Pole neexistuje - OK
                        }
                    }

                    if ($db_updated) {
                        try {
                            $stmt_update2 = $pdo_new->prepare("UPDATE clanky SET audio_file = :audio_file WHERE id = :id");
                            $stmt_update2->execute([
                                ':id' => $id_clanku,
                                ':audio_file' => $new_filename
                            ]);
                        } catch (PDOException $e) {
                        }
                    }
                    
                    $copied++;
                    zprava("✅ Done");
                } else {
                    $skipped_no_file++;
                    $skipped++;
                    zprava("❌ Soubor se nepodařilo najít nebo zkopírovat: $nazev_souboru");
                }
                
                // Progress každých 50 záznamů
                if (($copied + $skipped + $errors) % 50 == 0) {
                    zprava("  Zpracováno " . ($copied + $skipped + $errors) . " audio záznamů...");
                }
            } catch (Exception $e) {
                $errors++;
                zprava("⚠️ Chyba u audio ID {$audio['id']}: " . $e->getMessage());
            }
        }
        
        zprava("✓ Audio soubory: $copied zkopírováno, $skipped přeskočeno ($skipped_no_article článků neexistuje, $skipped_no_file souborů neexistuje), $errors chyb.");
        
        if ($total_audio > 0 && $copied == 0 && $skipped == $total_audio) {
            zprava("");
            zprava("⚠️ POZOR: Žádný soubor nebyl zkopírován!");
            zprava("   Možné příčiny:");
            zprava("   - Soubory neexistují ve staré cestě: " . implode(', ', $old_audio_paths));
            zprava("   - Články neexistují v nové DB");
            zprava("   - Špatná cesta k souborům");
            if ($total_audio <= 5) {
                zprava("");
                zprava("   Prvních " . min(5, $total_audio) . " záznamů:");
                foreach (array_slice($audio_records, 0, 5) as $audio) {
                    $exists = "✗ neexistuje";
                    foreach ($old_audio_paths as $p) {
                        if (strpos($p, 'http') === 0) {
                            continue;
                        }
                        $test_file = $p . $audio['nazev_souboru'];
                        if (file_exists($test_file)) {
                            $exists = "✓ existuje";
                            break;
                        }
                    }
                    zprava("     - ID článku: {$audio['id_clanku']}, soubor: {$audio['nazev_souboru']} (" . $exists . ")");
                }
            }
        }
        
        // Zobrazit informaci o pokračování
        if (count($audio_records) > 0) {
            $last_id = end($audio_records)['id_clanku'];
            $first_id = reset($audio_records)['id_clanku'];
            
            if ($batch_limit > 0 && $total_audio > $batch_limit) {
                $next_start_id = $last_id + 1;
                zprava("");
                zprava("📌 Zpracovány audio soubory pro články ID: $first_id - $last_id (z celkem $total_audio)");
                zprava("📌 Pro pokračování v migraci použij:");
                if ($max_id > 0) {
                    zprava("   ?step=10&start_id=$next_start_id&max_id=$max_id&limit=$batch_limit");
                } else {
                    zprava("   ?step=10&start_id=$next_start_id&limit=$batch_limit");
                }
                zprava("MIGRATE_DB_STEP=10");
                zprava("MIGRATE_DB_NEXT_START_ID=$next_start_id");
                zprava("MIGRATE_DB_DONE=0");
            } else {
                zprava("");
                zprava("📌 Zpracovány audio soubory pro články ID: $first_id - $last_id");
                if ($total_audio > 0 && $total_audio == count($audio_records)) {
                    zprava("✅ Všechny audio soubory v rozsahu byly zpracovány!");
                    if ($start_id > 0) {
                        $next_start_id = $last_id + 1;
                        zprava("💡 Pro pokračování od ID $next_start_id použij:");
                        zprava("   ?step=10&start_id=$next_start_id&limit=$batch_limit");
                    }
                }
                zprava("MIGRATE_DB_STEP=10");
                zprava("MIGRATE_DB_DONE=1");
            }
        }
        
    } catch (Exception $e) {
        zprava("❌ Chyba při migraci audio souborů: " . $e->getMessage());
    }
}

zprava("\n=== ✅ Migrace dokončena! ===");
zprava("Stará databáze zůstala nezměněná, data byla zkopírována do nové DB.");
zprava("\nPro spuštění jednotlivých kroků použijte: ?step=1 až ?step=10");
zprava("Pro spuštění všech kroků použijte: ?step=all (nebo bez parametru)");
zprava("\nPro zpracování článků od určitého ID použijte: ?step=3&start_id=821");
zprava("   (zpracuje články 821, 822, 823... směrem nahoru)");
zprava("Pro filtrování článků v rozsahu: ?step=3&start_id=821&max_id=1062&limit=50");
?>
