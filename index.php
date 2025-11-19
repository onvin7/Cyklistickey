<?php
// Globální nastavení session cookie - MUSÍ být před jakýmkoli session_start()
// Zajistí konzistentní nastavení napříč celou aplikací
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');
ini_set('session.cookie_secure', false); // Nastav na true, pokud používáš HTTPS
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', true);

// DEBUG LOGY ZAKOMENTOVÁNY - pro debug odkomentovat
// @file_put_contents(__DIR__ . '/logs/debug_test.log', date('Y-m-d H:i:s') . " - MAIN index.php loaded - URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n", FILE_APPEND);

// Pokud uživatel požaduje /admin, přesměruj na /admin
if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0) {
    // @file_put_contents(__DIR__ . '/logs/debug_test.log', date('Y-m-d H:i:s') . " - Routing to admin/index.php\n", FILE_APPEND);
    require __DIR__ . '/admin/index.php';
    exit;
}

// Jinak přesměruj všechny požadavky na /web/index.php
// @file_put_contents(__DIR__ . '/logs/debug_test.log', date('Y-m-d H:i:s') . " - Routing to web/index.php\n", FILE_APPEND);
require __DIR__ . '/web/index.php';
exit;
