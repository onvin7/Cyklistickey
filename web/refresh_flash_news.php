<?php

require_once '../config/autoloader.php';
use App\Models\FlashNewsJSONSimple;

try {
    $flashNewsModel = new FlashNewsJSONSimple();
    
    if ($flashNewsModel->refreshFromAPI()) {
        echo "✅ Flash news byla úspěšně aktualizována z API\n";
    } else {
        echo "❌ Chyba při aktualizaci flash news z API\n";
    }
} catch (Exception $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
}
