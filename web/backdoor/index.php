<?php

// Konfigurace
$inputFile    = 'dump.sql';           // Vstupní dump
$maxSize      = 2 * 1024 * 1024;        // Maximální velikost výstupního souboru v bajtech (2 MB)
$outputPrefix = 'dump_part_';           // Prefix názvu výstupních souborů

/**
 * Zapíše obsah do souboru s daným prefixem a číslem části.
 *
 * @param string $content Obsah k zápisu.
 * @param int    $partNumber Číslo části.
 * @param string $prefix Prefix názvu souboru.
 */
function writePart($content, $partNumber, $prefix) {
    $filename = $prefix . $partNumber . '.sql';
    file_put_contents($filename, $content);
    echo "Vytvořen soubor: $filename\n";
}

/**
 * Rozdělí dlouhý INSERT příkaz na více částí tak, že každá část obsahuje kompletní hlavičku
 * a potom podmnožinu hodnot. Předpokládáme, že příkaz vypadá přibližně jako:
 * 
 *   INSERT INTO tabulka (sloupce) VALUES (hodnoty1), (hodnoty2), ...;
 *
 * @param string $statement Celý INSERT příkaz.
 * @param int    $maxSize Maximální velikost souboru.
 * @param string $outputPrefix Prefix pro názvy výstupních souborů.
 * @param int    &$currentPart Čítač číslování souborů (bude se aktualizovat).
 * @return array Vrací pole vytvořených částí (pro interní potřeby).
 */
function splitInsertStatement($statement, $maxSize, $outputPrefix, &$currentPart) {
    // Najdeme pozici klíčového slova VALUES (bez ohledu na velikost písmen)
    $pattern = '/\bVALUES\b/i';
    if (!preg_match($pattern, $statement, $matches, PREG_OFFSET_CAPTURE)) {
        // Pokud nenajdeme VALUES, vrátíme příkaz beze změny.
        return [$statement];
    }
    // Rozdělíme příkaz na hlavičku a zbytek (seznam hodnot)
    $valuesPos = $matches[0][1];
    $header = substr($statement, 0, $valuesPos + strlen($matches[0][0])); // včetně "VALUES"
    $tail = trim(substr($statement, $valuesPos + strlen($matches[0][0])));
    // Odstraníme případné středník na konci
    if (substr($tail, -1) == ';') {
        $tail = substr($tail, 0, -1);
    }
    
    // Nyní parsujeme tail na jednotlivé hodnotové skupiny (předpokládáme, že každá začíná závorkou).
    $tuples = [];
    $tuple = '';
    $parenCount = 0;
    $inString = false;
    $escape = false;
    $len = strlen($tail);
    
    for ($i = 0; $i < $len; $i++) {
        $char = $tail[$i];
        $tuple .= $char;
        
        // Ošetření escape sekvence
        if ($escape) {
            $escape = false;
            continue;
        }
        if ($char === '\\') {
            $escape = true;
            continue;
        }
        // Řetězec – předpokládáme, že se používají apostrofy
        if ($char === "'") {
            $inString = !$inString;
            continue;
        }
        if (!$inString) {
            if ($char === '(') {
                $parenCount++;
            } else if ($char === ')') {
                $parenCount--;
            } else if ($char === ',' && $parenCount === 0) {
                // Pokud jsme na úrovni oddělovače, jedná se o konec jedné hodnotové sady
                $tuple = rtrim($tuple, ", \n\r\t");
                $tuples[] = $tuple;
                $tuple = '';
                continue;
            }
        }
    }
    // Přidáme poslední tuple, pokud nějaký zůstal
    if (trim($tuple) !== '') {
        $tuples[] = trim($tuple);
    }
    
    // Sestavíme INSERT příkazy po částech
    $parts = [];
    $currentPartContent = $header . " ";
    $firstTuple = true;
    foreach ($tuples as $tuplePart) {
        // Pokud již není první, přidáme před tuple čárku
        $prefixComma = $firstTuple ? '' : ',';
        $addition = $prefixComma . $tuplePart;
        // Otestujeme, zda přidání dalšího tuple nepřekročí limit
        if (strlen($currentPartContent . $addition . ';') > $maxSize && !$firstTuple) {
            // Dokončíme aktuální část a uložíme ji
            $currentPartContent .= ';';
            writePart($currentPartContent, $currentPart, $outputPrefix);
            $currentPart++;
            // Začneme novou část s hlavičkou a aktuálním tuplem
            $currentPartContent = $header . " " . $tuplePart;
            $firstTuple = false;
        } else {
            $currentPartContent .= $addition;
            $firstTuple = false;
        }
    }
    // Uložíme poslední část, pokud obsahuje nějaké tuple
    if (trim($currentPartContent) !== $header) {
        $currentPartContent .= ';';
        writePart($currentPartContent, $currentPart, $outputPrefix);
        $currentPart++;
    }
    
    return $parts;
}

// Hlavní zpracování dumpu
$currentContent = ''; // Akumulátor pro příkazy, které nejsou dělené INSERTy
$currentSize = 0;
$currentPart = 1;   // Počáteční číslo části

$handle = fopen($inputFile, 'r');
if (!$handle) {
    die("Nelze otevřít soubor: $inputFile");
}

$statement = '';  // Akumulace aktuálního SQL příkazu

while (($line = fgets($handle)) !== false) {
    $statement .= $line;
    // Když řádek (po oříznutí) končí středníkem, považujeme to za konec příkazu
    if (preg_match('/;\s*$/', trim($line))) {
        $trimmedStmt = trim($statement);
        // Pokud se jedná o INSERT příkaz
        if (stripos($trimmedStmt, 'INSERT') === 0) {
            // Pokud je INSERT příkaz větší než limit, rozdělujeme jej
            if (strlen($trimmedStmt) > $maxSize) {
                // Pokud máme nahromaděné jiné příkazy, zapíšeme je dříve
                if ($currentContent !== '') {
                    writePart($currentContent, $currentPart, $outputPrefix);
                    $currentPart++;
                    $currentContent = '';
                    $currentSize = 0;
                }
                splitInsertStatement($trimmedStmt, $maxSize, $outputPrefix, $currentPart);
            } else {
                // U kratších příkazů kontrolujeme, zda nepřekročí akumulátor
                if ($currentSize + strlen($trimmedStmt) > $maxSize && $currentContent !== '') {
                    writePart($currentContent, $currentPart, $outputPrefix);
                    $currentPart++;
                    $currentContent = '';
                    $currentSize = 0;
                }
                $currentContent .= $trimmedStmt . "\n";
                $currentSize += strlen($trimmedStmt . "\n");
            }
        } else {
            // Pro ostatní příkazy (např. CREATE, ALTER, atd.)
            if ($currentSize + strlen($trimmedStmt) > $maxSize && $currentContent !== '') {
                writePart($currentContent, $currentPart, $outputPrefix);
                $currentPart++;
                $currentContent = '';
                $currentSize = 0;
            }
            $currentContent .= $trimmedStmt . "\n";
            $currentSize += strlen($trimmedStmt . "\n");
        }
        // Reset akumulace pro další příkaz
        $statement = '';
    }
}
fclose($handle);

// Pokud zbyl nějaký obsah, uložíme jej
if (!empty($currentContent)) {
    writePart($currentContent, $currentPart, $outputPrefix);
}

// Vytvoření databázového připojení
$db = new PDO("mysql:host=localhost;dbname=cyklistickey", "root", "");

// Vypnutí kontroly cizích klíčů
$db->exec("SET FOREIGN_KEY_CHECKS = 0");

// Získání seznamu všech vytvořených částí
$parts = glob($outputPrefix . "*.sql");

// Import každé části
foreach ($parts as $part) {
    echo "Importuji soubor: $part\n";
    $sql = file_get_contents($part);
    $db->exec($sql);
}

// Zapnutí kontroly cizích klíčů
$db->exec("SET FOREIGN_KEY_CHECKS = 1");

// Smazání dočasných souborů
foreach ($parts as $part) {
    unlink($part);
    echo "Smazán soubor: $part\n";
}

echo "Import dokončen a dočasné soubory smazány.\n";
?>
