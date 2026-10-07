<?php
// 1. ZAPNEME BUFFER ÚPLNĚ NA ZAČÁTKU! 
// Zachytí případné chyby nebo prázdné mezery z configů.
ob_start();

require_once '../config/autoloader.php';
require_once '../config/db.php';

// PŘIDEJ TYTO DVA ŘÁDKY:
$database = new Database();
$db = $database->connect();

use App\Helpers\SEOHelper;
use App\Helpers\RateLimitHelper;

// Rate limiting pro sitemap
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!RateLimitHelper::checkAndSetHeaders($ip, 'sitemap', 10, 3600)) {
    ob_clean(); // Vyčistíme buffer
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <url>' . "\n";
    echo '    <loc>https://www.cyklistickey.cz/</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    echo '  </url>' . "\n";
    echo '</urlset>';
    exit;
}

// Cache mechanismus pro sitemap
$cacheFile = __DIR__ . '/cache/sitemap.xml';
$cacheTime = 3600; // 1 hodina

// Zkontroluj cache
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    ob_clean(); // Vyčistíme buffer
    header('Content-Type: application/xml; charset=utf-8');
    readfile($cacheFile);
    exit;
}

// Generování sitemap dat
// Pokud tohle hodí chybu, bezpečně se teď chytí do bufferu
$sitemapData = SEOHelper::generateSitemapData($db);

// Nastavení content type pro XML
header('Content-Type: application/xml; charset=utf-8');

// XML hlavička
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

// Generování URL záznamů (s pojistkou, pokud by DB vrátila null)
if (is_array($sitemapData) || is_iterable($sitemapData)) {
    foreach ($sitemapData as $url) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($url['url'] ?? '', ENT_XML1, 'UTF-8') . "</loc>\n";
        echo "    <lastmod>" . htmlspecialchars($url['lastmod'] ?? '', ENT_XML1, 'UTF-8') . "</lastmod>\n";
        echo "    <changefreq>" . htmlspecialchars($url['changefreq'] ?? '', ENT_XML1, 'UTF-8') . "</changefreq>\n";
        echo "    <priority>" . htmlspecialchars($url['priority'] ?? '', ENT_XML1, 'UTF-8') . "</priority>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';

// Uložení celého bufferu do proměnné a jeho bezpečné zavření
$sitemapContent = ob_get_clean();

// Ulož do cache
if (!is_dir(dirname($cacheFile))) {
    if (!mkdir(dirname($cacheFile), 0755, true)) {
        error_log('Sitemap Cache Error: Cannot create cache directory');
    }
}
if (!file_put_contents($cacheFile, $sitemapContent)) {
    error_log('Sitemap Cache Error: Cannot write cache file');
}

// Finální vypsání obsahu do prohlížeče
echo $sitemapContent;

// Cleanup starých rate limit souborů
if (rand(1, 100) === 1) {
    RateLimitHelper::cleanup();
}

// ZÁMĚRNĚ ZDE NENÍ UZAVÍRACÍ TAG ? >
// Zabraňuje to nechtěnému propsání prázdných znaků na konec souboru.