<?php
// Pokud uživatel požaduje /admin, přesměruj na /web/admin
if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0) {
require __DIR__ . '/web/admin/index.php';
exit;
}

// Jinak přesměruj všechny požadavky na /web/index.php
require __DIR__ . '/web/index.php';
exit;
