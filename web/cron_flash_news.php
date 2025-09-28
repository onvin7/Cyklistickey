<?php

// Cron job pro automatickou aktualizaci flash news
// Spustit každých 30 minut: */30 * * * * php /path/to/web/cron_flash_news.php

require_once '../config/autoloader.php';
use App\Models\FlashNewsJSON;

try {
    $flashNewsModel = new FlashNewsJSON();
    
    if ($flashNewsModel->refreshFromAPI()) {
        error_log('Flash News: Úspěšně aktualizováno z API - ' . date('Y-m-d H:i:s'));
    } else {
        error_log('Flash News: Chyba při aktualizaci z API - ' . date('Y-m-d H:i:s'));
    }
} catch (Exception $e) {
    error_log('Flash News Cron Error: ' . $e->getMessage() . ' - ' . date('Y-m-d H:i:s'));
}
