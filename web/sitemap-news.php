<?php
require_once '../config/autoloader.php';
require_once '../config/db.php';

use App\Helpers\SEOHelper;
use App\Helpers\RateLimitHelper;

// Rate limiting pro sitemap
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!RateLimitHelper::checkAndSetHeaders($ip, 'sitemap-news', 10, 3600)) {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";
    echo '</urlset>';
    exit;
}

// Cache mechanismus pro sitemap
$cacheFile = __DIR__ . '/cache/sitemap-news.xml';
$cacheTime = 1800; // 30 minut (news sitemap se aktualizuje častěji)

// Zkontroluj cache
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    header('Content-Type: application/xml; charset=utf-8');
    readfile($cacheFile);
    exit;
}

// Nastavení content type pro XML
header('Content-Type: application/xml; charset=utf-8');

$config = SEOHelper::getConfig();
$baseUrl = $config['site']['url'];
$publicationName = $config['site']['name'];
$publicationLanguage = $config['site']['language'] ?? 'cs';

// Začni output buffering
ob_start();

// XML hlavička
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

try {
    // Získání nejnovějších článků (poslední 2-3 dny, max 1000)
    $stmt = $db->query("
        SELECT id, url, nazev, datum, updated_at
        FROM clanky 
        WHERE viditelnost = 1 
        AND datum <= NOW()
        AND datum >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ORDER BY datum DESC
        LIMIT 1000
    ");
    $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    foreach ($articles as $article) {
        $articleUrl = $baseUrl . '/article/' . $article['url'];
        $publicationDate = date('c', strtotime($article['datum']));
        $title = htmlspecialchars($article['nazev'] ?? '', ENT_XML1, 'UTF-8');
        
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($articleUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
        echo "    <news:news>\n";
        echo "      <news:publication>\n";
        echo "        <news:name>" . htmlspecialchars($publicationName, ENT_XML1, 'UTF-8') . "</news:name>\n";
        echo "        <news:language>" . htmlspecialchars($publicationLanguage, ENT_XML1, 'UTF-8') . "</news:language>\n";
        echo "      </news:publication>\n";
        echo "      <news:publication_date>" . htmlspecialchars($publicationDate, ENT_XML1, 'UTF-8') . "</news:publication_date>\n";
        echo "      <news:title>" . $title . "</news:title>\n";
        echo "    </news:news>\n";
        echo "  </url>\n";
    }
} catch (\Exception $e) {
    error_log('Sitemap News Error: ' . $e->getMessage());
}

echo '</urlset>';

// Ulož do cache
$sitemapContent = ob_get_contents();
if (!is_dir(dirname($cacheFile))) {
    if (!mkdir(dirname($cacheFile), 0755, true)) {
        error_log('Sitemap News Cache Error: Cannot create cache directory');
    }
}
if (!file_put_contents($cacheFile, $sitemapContent)) {
    error_log('Sitemap News Cache Error: Cannot write cache file');
}

// Vyčisti buffer a pošli obsah
ob_end_flush();

// Cleanup starých rate limit souborů (pouze 1x za 100 požadavků)
if (rand(1, 100) === 1) {
    RateLimitHelper::cleanup();
}
?>

