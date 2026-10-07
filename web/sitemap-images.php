<?php
// 1. ZAPNEME BUFFER ÚPLNĚ NA ZAČÁTKU
ob_start();

use App\Helpers\SEOHelper;
use App\Helpers\RateLimitHelper;

require_once '../config/autoloader.php';
require_once '../config/db.php';

// Vytvoření připojení k DB
$database = new Database();
$db = $database->connect();

// Pojistka: Pokud se DB nepřipojí, skript se zastaví a nevytvoří chybnou cache
if (!$db) {
    ob_end_clean();
    die('Kriticka chyba: Nepodarilo se pripojit k databazi.');
}

// Rate limiting pro sitemap
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!RateLimitHelper::checkAndSetHeaders($ip, 'sitemap-images', 10, 3600)) {
    ob_clean();
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    echo '</urlset>';
    exit;
}

// Cache mechanismus pro sitemap
$cacheFile = __DIR__ . '/cache/sitemap-images.xml';
$cacheTime = 3600; // 1 hodina

// Zkontroluj cache
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    ob_clean();
    header('Content-Type: application/xml; charset=utf-8');
    readfile($cacheFile);
    exit;
}

// Získání URL webu
$config = SEOHelper::getConfig();
$baseUrl = $config['site']['url'] ?? 'https://www.cyklistickey.cz';

// Nastavení content type pro XML
header('Content-Type: application/xml; charset=utf-8');

// XML hlavička
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

try {
    // Získání článků s obrázky
    $stmt = $db->query("
        SELECT id, url, nazev, obsah, nahled_foto, datum, updated_at
        FROM clanky 
        WHERE viditelnost = 1 
        AND datum <= NOW()
        AND nahled_foto IS NOT NULL 
        AND nahled_foto != ''
        ORDER BY datum DESC
        LIMIT 1000
    ");
    $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    if ($articles) {
        foreach ($articles as $article) {
            $articleUrl = rtrim($baseUrl, '/') . '/article/' . $article['url'];
            $lastmod = $article['updated_at'] ?? $article['datum'];
            
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($articleUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
            echo "    <lastmod>" . htmlspecialchars(date('Y-m-d', strtotime($lastmod)), ENT_XML1, 'UTF-8') . "</lastmod>\n";
            
            // Image data - obrázky jsou v /uploads/thumbnails/velke/
            $imageUrl = rtrim($baseUrl, '/') . '/uploads/thumbnails/velke/' . $article['nahled_foto'];
            $imageTitle = htmlspecialchars($article['nazev'] ?? '', ENT_XML1, 'UTF-8');
            $imageCaption = htmlspecialchars(strip_tags(substr($article['obsah'] ?? '', 0, 200)), ENT_XML1, 'UTF-8');
            
            echo "    <image:image>\n";
            echo "      <image:loc>" . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . "</image:loc>\n";
            if ($imageTitle) {
                echo "      <image:title>" . $imageTitle . "</image:title>\n";
            }
            if ($imageCaption) {
                echo "      <image:caption>" . $imageCaption . "</image:caption>\n";
            }
            echo "    </image:image>\n";
            
            echo "  </url>\n";
        }
    }
} catch (\Exception $e) {
    error_log('Sitemap Images Error: ' . $e->getMessage());
}

echo '</urlset>';

// Ulož do proměnné a bezpečně zavři buffer
$sitemapContent = ob_get_clean();

// Ulož do cache
if (!is_dir(dirname($cacheFile))) {
    if (!mkdir(dirname($cacheFile), 0755, true)) {
        error_log('Sitemap Images Cache Error: Cannot create cache directory');
    }
}
if (!file_put_contents($cacheFile, $sitemapContent)) {
    error_log('Sitemap Images Cache Error: Cannot write cache file');
}

// Finální výpis
echo $sitemapContent;

// Cleanup starých rate limit souborů (pouze 1x za 100 požadavků)
if (rand(1, 100) === 1) {
    RateLimitHelper::cleanup();
}