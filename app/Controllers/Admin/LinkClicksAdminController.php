<?php

namespace App\Controllers\Admin;

use App\Models\LinkClick;
use App\Models\LinkClickEvent;
use App\Models\Article;

class LinkClicksAdminController
{
    private $db;
    private $linkClickModel;
    private $linkClickEventModel;
    private $articleModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->linkClickModel = new LinkClick($db);
        $this->linkClickEventModel = new LinkClickEvent($db);
        $this->articleModel = new Article($db);
    }

    public function index()
    {
        // Kontrola přístupu - pouze administrátoři (role 3)
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 3) {
            http_response_code(403);
            die('Nemáte oprávnění k přístupu na tuto stránku.');
        }

        // Získání všech statistik kliků s informacemi o článcích
        $allClicks = $this->linkClickModel->getAllClicksWithArticles();
        
        // Seskupení podle článku a načtení dat článků
        $clicksByArticle = [];
        $articleIds = [];
        
        foreach ($allClicks as $click) {
            $articleId = $click['id_clanku'];
            if (!isset($clicksByArticle[$articleId])) {
                $articleIds[] = $articleId;
                $clicksByArticle[$articleId] = [
                    'article' => [
                        'id' => $articleId,
                        'nazev' => $click['nazev_clanku'] ?? 'Neznámý článek',
                        'url' => $click['url_clanku'] ?? ''
                    ],
                    'links' => [],
                    'total_clicks' => 0
                ];
            }
            $clicksByArticle[$articleId]['links'][] = $click;
            $clicksByArticle[$articleId]['total_clicks'] += $click['click_count'];
        }

        // Načtení dat článků pro získání data
        if (!empty($articleIds)) {
            $placeholders = implode(',', array_fill(0, count($articleIds), '?'));
            $query = "SELECT id, datum FROM clanky WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($query);
            $stmt->execute($articleIds);
            $articlesData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Přidání data do struktury
            foreach ($articlesData as $articleData) {
                if (isset($clicksByArticle[$articleData['id']])) {
                    $clicksByArticle[$articleData['id']]['article']['datum'] = $articleData['datum'];
                }
            }
        }

        // Seřazení podle data článku (nejnovější první)
        uasort($clicksByArticle, function($a, $b) {
            $dateA = $a['article']['datum'] ?? '1970-01-01';
            $dateB = $b['article']['datum'] ?? '1970-01-01';
            return strtotime($dateB) <=> strtotime($dateA); // DESC - nejnovější první
        });

        $adminTitle = "Statistiky kliků na odkazy | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/link-clicks/index.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function article($articleId)
    {
        // Kontrola přístupu - pouze administrátoři (role 3)
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 3) {
            http_response_code(403);
            die('Nemáte oprávnění k přístupu na tuto stránku.');
        }

        // Načtení článku přímo z DB (bez ohledu na viditelnost)
        $query = "SELECT * FROM clanky WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            echo "Článek nenalezen.";
            return;
        }

        // Získání statistik kliků pro článek
        $clicks = $this->linkClickModel->getClicksByArticle($articleId);
        $totalClicks = $this->linkClickModel->getTotalClicksForArticle($articleId);
        
        // Detailní statistiky z events
        $deviceStats = $this->linkClickEventModel->getDeviceStatsByArticle($articleId);
        $browserStats = $this->linkClickEventModel->getBrowserStatsByArticle($articleId);
        $countryStats = $this->linkClickEventModel->getCountryStatsByArticle($articleId);
        $hourlyStats = $this->linkClickEventModel->getHourlyStatsByArticle($articleId);
        $timeOnPageStats = $this->linkClickEventModel->getAvgTimeOnPageByArticle($articleId);
        $uniqueIPs = $this->linkClickEventModel->getUniqueIPsByArticle($articleId);
        $recentEvents = $this->linkClickEventModel->getEventsByArticle($articleId, 50);

        $adminTitle = "Statistiky kliků: " . $article['nazev'] . " | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/link-clicks/article.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function urlDetails($linkClickId)
    {
        // Kontrola přístupu - pouze administrátoři (role 3)
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 3) {
            http_response_code(403);
            die('Nemáte oprávnění k přístupu na tuto stránku.');
        }

        // Načtení link_click záznamu
        $query = "SELECT lc.*, c.nazev AS nazev_clanku, c.url AS url_clanku 
                  FROM link_clicks lc
                  LEFT JOIN clanky c ON lc.id_clanku = c.id
                  WHERE lc.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $linkClickId, \PDO::PARAM_INT);
        $stmt->execute();
        $linkClick = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$linkClick) {
            echo "Odkaz nenalezen.";
            return;
        }

        // Načtení všech eventů pro tento odkaz
        $query = "SELECT * FROM link_click_events 
                  WHERE link_click_id = :link_click_id 
                  ORDER BY clicked_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':link_click_id', $linkClickId, \PDO::PARAM_INT);
        $stmt->execute();
        $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Statistiky pro tento URL
        $deviceStats = [];
        $browserStats = [];
        $countryStats = [];
        $hourlyStats = [];
        $timeOnPageStats = null;
        $uniqueIPs = 0;

        if (!empty($events)) {
            // Statistiky zařízení
            $deviceCounts = [];
            foreach ($events as $event) {
                $device = $event['device_type'] ?? 'unknown';
                $deviceCounts[$device] = ($deviceCounts[$device] ?? 0) + 1;
            }
            foreach ($deviceCounts as $device => $count) {
                $deviceStats[] = ['device_type' => $device, 'count' => $count];
            }
            usort($deviceStats, fn($a, $b) => $b['count'] <=> $a['count']);

            // Statistiky prohlížečů
            $browserCounts = [];
            foreach ($events as $event) {
                if (!empty($event['browser'])) {
                    $browser = $event['browser'];
                    $browserCounts[$browser] = ($browserCounts[$browser] ?? 0) + 1;
                }
            }
            foreach ($browserCounts as $browser => $count) {
                $browserStats[] = ['browser' => $browser, 'count' => $count];
            }
            usort($browserStats, fn($a, $b) => $b['count'] <=> $a['count']);
            $browserStats = array_slice($browserStats, 0, 10);

            // Statistiky zemí
            $countryCounts = [];
            foreach ($events as $event) {
                if (!empty($event['country'])) {
                    $country = $event['country'];
                    $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;
                }
            }
            foreach ($countryCounts as $country => $count) {
                $countryStats[] = ['country' => $country, 'count' => $count];
            }
            usort($countryStats, fn($a, $b) => $b['count'] <=> $a['count']);
            $countryStats = array_slice($countryStats, 0, 10);

            // Časové rozložení
            $hourlyCounts = [];
            foreach ($events as $event) {
                $hour = (int)date('H', strtotime($event['clicked_at']));
                $hourlyCounts[$hour] = ($hourlyCounts[$hour] ?? 0) + 1;
            }
            ksort($hourlyCounts);
            foreach ($hourlyCounts as $hour => $count) {
                $hourlyStats[] = ['hour' => $hour, 'count' => $count];
            }

            // Průměrný čas na stránce
            $timeOnPageValues = array_filter(array_column($events, 'time_on_page'), fn($v) => $v !== null);
            if (!empty($timeOnPageValues)) {
                $timeOnPageStats = [
                    'avg_time' => array_sum($timeOnPageValues) / count($timeOnPageValues),
                    'min_time' => min($timeOnPageValues),
                    'max_time' => max($timeOnPageValues),
                    'total_clicks' => count($timeOnPageValues)
                ];
            }

            // Unikátní IP adresy
            $uniqueIPs = count(array_unique(array_filter(array_column($events, 'ip_address'), fn($v) => !empty($v))));
        }

        $adminTitle = "Detail odkazu: " . htmlspecialchars(substr($linkClick['url'], 0, 50)) . "... | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/link-clicks/url-details.php';
        include '../app/Views/Admin/layout/base.php';
    }
}

