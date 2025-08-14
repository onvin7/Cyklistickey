<?php
ini_set('memory_limit', '1024M');

// Funkce pro výpis zpráv podle prostředí
function zprava($text) {
    echo $text . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
    flush();
}

// Přidáme div pro zobrazení posledního ID
echo '<div id="lastId" style="position: fixed; top: 10px; right: 10px; background: #333; color: white; padding: 10px; border-radius: 5px; z-index: 1000;">Poslední zpracované ID: 0</div>';

// Funkce pro připojení k databázi s kontrolou chyb
function connectDB($dsn, $user, $password, $dbLabel) {
    try {
        zprava("Připojování k databázi $dbLabel...");
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES 'utf8mb4'");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET SESSION collation_connection = 'utf8mb4_general_ci'");
        $pdo->exec("SET SESSION wait_timeout = 28800");
        zprava("Připojení k databázi $dbLabel úspěšné.");
        return $pdo;
    } catch (PDOException $e) {
        die("Chyba připojení k databázi $dbLabel: " . $e->getMessage());
    }
}

function is_url_alive($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

function safeExecute(&$stmt, $data, &$pdo, $retry = 1) {
    try {
        $stmt->execute($data);
    } catch (PDOException $e) {
        if ($retry > 0 && strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
            zprava("⚠️ Ztráta spojení s DB, pokus o znovupřipojení...");
            sleep(2);
            $pdo = connectDB('mysql:host=md413.wedos.net;dbname=d340619_blog;charset=utf8mb4', 'w340619_blog', 'kaYak714?', 'NOVÁ DB');
            $stmt = $pdo->prepare($stmt->queryString);
            safeExecute($stmt, $data, $pdo, $retry - 1);
        } else {
            throw $e;
        }
    }
}

$pdo_new = connectDB('mysql:host=md413.wedos.net;dbname=d340619_blog;charset=utf8mb4', 'w340619_blog', 'kaYak714?', 'NOVÁ DB');
$pdo_old = connectDB('mysql:host=md396.wedos.net;dbname=d340619_clanky;charset=utf8mb4', 'w340619_clanky', 'bqsUuxcr', 'STARÁ DB');

$pdo_new->exec("SET FOREIGN_KEY_CHECKS=0");

// Nastavení rozsahu a načtení posledního ID z logu
$minId = 881;
$maxId = 925;
$lastIdFile = 'last_id.txt';
$lastProcessedId = file_exists($lastIdFile) ? (int)file_get_contents($lastIdFile) : $minId - 1;

$stmt_new = $pdo_new->prepare("SELECT id FROM clanky WHERE id BETWEEN :minId AND :maxId AND id > :lastProcessedId ORDER BY id ASC");
$stmt_new->execute([':minId' => $minId, ':maxId' => $maxId, ':lastProcessedId' => $lastProcessedId]);
zprava("Stahuji HTML obsah článků v rozsahu ID $minId - $maxId, od ID > $lastProcessedId...");

$i = 0;
while ($row = $stmt_new->fetch(PDO::FETCH_ASSOC)) {
    if (++$i % 50 == 0) {
        zprava("⏸ Pauza po 50 článcích...");
        sleep(1);
        $pdo_new = connectDB('mysql:host=md413.wedos.net;dbname=d340619_blog;charset=utf8mb4', 'w340619_blog', 'kaYak714?', 'NOVÁ DB');
    }
    $url = 'https://www.magazin.cyklistickey.cz/assets/html/clanek_' . $row['id'] . '.php';
    if (is_url_alive($url)) {
        $html = @file_get_contents($url);
        if ($html !== false) {
            $update = $pdo_new->prepare("UPDATE clanky SET obsah = :obsah WHERE id = :id");
            try {
                safeExecute($update, [':obsah' => $html, ':id' => $row['id']], $pdo_new);
                file_put_contents($lastIdFile, $row['id']);
                echo "<script>document.getElementById('lastId').innerHTML = 'Poslední zpracované ID: {$row['id']}';</script>";
            } catch (Exception $e) {
                zprava("❌ Chyba u ID {$row['id']}: " . $e->getMessage());
                continue;
            }
        } else {
            zprava("❌ Nepodařilo se stáhnout obsah článku ID {$row['id']}, načtení selhalo.");
        }
    } else {
        zprava("❌ Nepodařilo se stáhnout obsah článku ID {$row['id']}, soubor neexistuje nebo je nedostupný.");
    }
    usleep(100000);
}
zprava("HTML obsah článků nastaven.");

$pdo_new->exec("SET FOREIGN_KEY_CHECKS=1");
zprava("Převod dokončen!");
?>
