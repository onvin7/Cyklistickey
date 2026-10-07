<?php
/**
 * Přenese audio názvy ze staré DB (tabulka audio) do nové DB (clanky.audio).
 * Mapování: audio.id_clanku -> clanky.id, hodnota: audio.nazev_souboru.
 */

$internalRun = defined('MIGRATION_INTERNAL_RUN') && MIGRATION_INTERNAL_RUN;

$credentialsFile = __DIR__ . '/../config/db_credentials.php';
if (file_exists($credentialsFile)) {
    require_once $credentialsFile;
}

if (!$internalRun && php_sapi_name() !== 'cli') {
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
ini_set('memory_limit', '512M');

if (php_sapi_name() === 'cli' && !$internalRun && isset($argv) && is_array($argv)) {
    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            echo "Použití: php migrate_audio_table_to_clanky_audio.php [--limit=0] [--start_id=0]\n";
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

function out_msg(string $text): void
{
    global $internalRun;
    echo $text . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
    if (!$internalRun && php_sapi_name() !== 'cli') {
        flush();
        if (ob_get_level() > 0) {
            ob_flush();
        }
    }
}

try {
    $oldHost = defined('OLD_DB_HOST') ? (string)OLD_DB_HOST : '';
    $oldName = defined('OLD_DB_NAME') ? (string)OLD_DB_NAME : '';
    $oldUser = defined('OLD_DB_USER') ? (string)OLD_DB_USER : '';
    $oldPass = defined('OLD_DB_PASS') ? (string)OLD_DB_PASS : '';

    $newHost = defined('DB_HOST') ? (string)DB_HOST : '';
    $newName = defined('DB_NAME') ? (string)DB_NAME : '';
    $newUser = defined('DB_USER') ? (string)DB_USER : '';
    $newPass = defined('DB_PASS') ? (string)DB_PASS : '';

    if ($oldHost === '' || $oldName === '' || $oldUser === '') {
        throw new RuntimeException('Chybí OLD_DB_* konfigurace ve config/db_credentials.php');
    }
    if ($newHost === '' || $newName === '' || $newUser === '') {
        throw new RuntimeException('Chybí DB_* konfigurace ve config/db_credentials.php');
    }

    $pdoOld = new PDO(
        "mysql:host={$oldHost};dbname={$oldName};charset=utf8mb4",
        $oldUser,
        $oldPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    $pdoNew = new PDO(
        "mysql:host={$newHost};dbname={$newName};charset=utf8mb4",
        $newUser,
        $newPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $limit = isset($_GET['limit']) ? max(0, (int)$_GET['limit']) : 0;
    $startId = isset($_GET['start_id']) ? max(0, (int)$_GET['start_id']) : 0;

    out_msg('=== MIGRACE audio.nazev_souboru -> clanky.audio ===');
    out_msg("Parametry: limit={$limit}, start_id={$startId}");

    $sql = "
        SELECT a.id_clanku, a.nazev_souboru
        FROM audio a
        WHERE a.id_clanku IS NOT NULL
          AND a.id_clanku > 0
          AND a.nazev_souboru IS NOT NULL
          AND a.nazev_souboru <> ''
    ";
    if ($startId > 0) {
        $sql .= " AND a.id_clanku >= :start_id";
    }
    $sql .= " ORDER BY a.id_clanku ASC";

    if ($limit > 0) {
        $sql .= " LIMIT :limit";
    }

    $stmtOld = $pdoOld->prepare($sql);
    if ($startId > 0) {
        $stmtOld->bindValue(':start_id', $startId, PDO::PARAM_INT);
    }
    if ($limit > 0) {
        $stmtOld->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $stmtOld->execute();
    $rows = $stmtOld->fetchAll();

    $total = count($rows);
    out_msg("Načteno záznamů ze staré tabulky audio: {$total}");
    if ($total === 0) {
        out_msg('Nic k migraci.');
        exit;
    }

    $stmtExists = $pdoNew->prepare('SELECT 1 FROM clanky WHERE id = :id LIMIT 1');
    $stmtUpdate = $pdoNew->prepare('UPDATE clanky SET audio = :audio WHERE id = :id');

    $updated = 0;
    $missingArticles = 0;
    $errors = 0;

    foreach ($rows as $idx => $row) {
        $idClanku = (int)($row['id_clanku'] ?? 0);
        $nazevSouboru = trim((string)($row['nazev_souboru'] ?? ''));
        if ($idClanku <= 0 || $nazevSouboru === '') {
            continue;
        }

        try {
            $stmtExists->execute([':id' => $idClanku]);
            if (!$stmtExists->fetchColumn()) {
                $missingArticles++;
                continue;
            }

            $stmtUpdate->execute([
                ':id' => $idClanku,
                ':audio' => $nazevSouboru,
            ]);
            $updated++;
        } catch (Throwable $e) {
            $errors++;
        }

        if ((($idx + 1) % 200) === 0) {
            out_msg("Průběh: zpracováno " . ($idx + 1) . "/{$total}");
        }
    }

    out_msg('');
    out_msg('=== HOTOVO ===');
    out_msg("Aktualizováno clanky.audio: {$updated}");
    out_msg("Chybějící článek v nové DB: {$missingArticles}");
    out_msg("Chyby při update: {$errors}");
} catch (Throwable $e) {
    out_msg('❌ Chyba: ' . $e->getMessage());
}

