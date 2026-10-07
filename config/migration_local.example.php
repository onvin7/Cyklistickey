<?php
/**
 * Příklad lokální konfigurace pro migraci článků z externích HTML.
 *
 * Zkopíruj řádky s define() do config/db_credentials.php (vedle DB konstant),
 * nebo tento soubor případně require_once z db_credentials.php.
 *
 * Cesta musí ukazovat na složku .../assets/html (kde leží clanek_123.php).
 */

// Lokální kopie starého webu (uprav podle svého PC):
// define('OLD_HTML_BASE_PATH', 'C:/Users/onvin/OneDrive/Dokumenty/WEB/Cyklistickey-final/subdom/magazin/assets/html');

// Volitelně vlastní veřejná URL magazínu (pokud stahuješ obsah přes HTTP místo přes disk):
// define('OLD_MAGAZIN_PUBLIC_URL', 'https://www.magazin.cyklistickey.cz');
