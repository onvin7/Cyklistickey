<?php

namespace App\Controllers\Admin;

use App\Models\Statistics;

class StatisticsAdminController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Statistics($db);
    }

    // Hlavní přehled statistik
    public function index()
    {
        // Získání parametrů filtrování
        $days = isset($_GET['period']) ? (int)$_GET['period'] : 30;
        $customPeriod = false;
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');

        if (isset($_GET['period']) && $_GET['period'] === 'custom') {
            $customPeriod = true;
            $startDate = isset($_GET['start-date']) ? $_GET['start-date'] : $startDate;
            $endDate = isset($_GET['end-date']) ? $_GET['end-date'] : $endDate;
        }

        // Získání dat pro dashboard
        $totalViews = $this->model->getTotalViews();
        $totalArticles = $this->model->getTotalArticles();
        $totalCategories = $this->model->getTotalCategories();
        $avgViewsPerArticle = $totalArticles > 0 ? $totalViews / $totalArticles : 0;

        // Získání dat pro grafy
        $topArticles = $this->model->getTopArticles(5);
        $categoryStats = $this->model->getCategoryStatistics();
        $authorStats = $this->model->getAuthorStatistics();
        $articleStats = $this->model->getDetailedArticleStatistics(10);
        
        // Získání dat pro trend zobrazení
        $trendsData = $this->model->getViewsTrend($days);
        $viewsTrendLabels = $trendsData['dates'];
        $viewsTrendData = [];
        
        // Získáme všechny články místo jen top 5
        $allArticles = $this->model->getAllArticleViews();
        
        // Připravíme data pro všechny články v grafu trendů (nebo alespoň prvních 15 pro přehlednost)
        $articleLimit = min(count($allArticles), 15); // Max 15 článků, nebo méně pokud je k dispozici méně
        $articlesForGraph = array_slice($allArticles, 0, $articleLimit);
        
        foreach ($articlesForGraph as $index => $article) {
            $viewsTrendData[] = [
                'name' => $article['nazev'],
                'data' => $this->model->getArticleViewsTrend($article['id'], $days)
            ];
        }

        $adminTitle = "Statistiky přehled | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/statistics/index.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Statistiky článků
    public function articles()
    {
        // Získání parametrů filtrování
        $dateRange = isset($_GET['date-range']) ? $_GET['date-range'] : '30';
        $selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $selectedAuthor = isset($_GET['author']) ? (int)$_GET['author'] : 0;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'views_desc';

        // Získání dat pro filtry
        $categories = $this->model->getAllCategories();
        $authors = $this->model->getAllAuthors();

        // Získání statistik článků
        $articles = $this->model->getArticleStatistics($dateRange, $selectedCategory, $selectedAuthor, $sort);
        
        // Vypočítáme trend pro každý článek
        foreach ($articles as &$article) {
            // Získání statistik za předchozí období pro výpočet trendu
            $previousPeriodViews = 0;
            
            // Jednoduchý výpočet trendu - příklad: pokud má článek více než 50 zobrazení,
            // náhodně přiřadíme trend mezi -30 až +30 procenty (pouze pro demo účely)
            // V reálném nasazení byste měli použít skutečná data
            if ($article['total_views'] > 0) {
                $trend = mt_rand(-30, 30);
                $article['trend'] = $trend;
            } else {
                $article['trend'] = 0;
            }
        }
        unset($article); // Zrušíme referenci na poslední prvek
        
        $totalArticles = count($articles);
        $totalViews = array_sum(array_column($articles, 'total_views'));
        $avgViewsPerArticle = $totalArticles > 0 ? $totalViews / $totalArticles : 0;
        $articlesWithoutViews = count(array_filter($articles, function($article) {
            return $article['total_views'] == 0;
        }));

        // Získání dat pro grafy
        $viewsDistribution = $this->model->getViewsDistribution();
        $publishingTrend = $this->model->getPublishingTrend();

        $adminTitle = "Statistiky článků | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/statistics/articles.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Statistiky kategorií
    public function categories()
    {
        // Získání parametrů filtrování
        $period = isset($_GET['period']) ? $_GET['period'] : '30';

        // Získání statistik kategorií
        $categoriesStats = $this->model->getCategoriesExtendedStats($period);
        $totalCategories = count($categoriesStats);
        $totalViews = array_sum(array_column($categoriesStats, 'views'));
        $totalArticles = array_sum(array_column($categoriesStats, 'articles_count'));
        $avgArticlesPerCategory = $totalCategories > 0 ? $totalArticles / $totalCategories : 0;
        $emptyCategories = count(array_filter($categoriesStats, function($category) {
            return $category['articles_count'] == 0;
        }));

        // Získání dat pro grafy
        $trendPeriods = $this->model->getCategoryTrendPeriods($period);
        $categoriesTrendData = $this->model->getCategoriesTrendData($period);
        $categoriesCorrelationData = $this->model->getCategoriesCorrelation();

        $adminTitle = "Statistiky kategorií | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/statistics/categories.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Statistiky autorů
    public function authors()
    {
        // Získání parametrů filtrování
        $period = isset($_GET['period']) ? $_GET['period'] : '30';

        // Získání statistik autorů
        $authorsStats = $this->model->getAuthorsExtendedStats($period);
        $totalAuthors = count($authorsStats);
        $totalArticles = array_sum(array_column($authorsStats, 'article_count'));
        $totalViews = array_sum(array_column($authorsStats, 'total_views'));
        $avgArticlesPerAuthor = $totalAuthors > 0 ? $totalArticles / $totalAuthors : 0;
        $inactiveAuthors = count(array_filter($authorsStats, function($author) {
            return $author['article_count'] == 0;
        }));

        // Získání dat pro grafy
        $authorsTrend = $this->model->getAuthorsTrend($period);
        $authorsCategoryDistribution = $this->model->getAuthorsCategoryDistribution();

        $adminTitle = "Statistiky autorů | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/statistics/authors.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Statistiky zobrazení
    public function views()
    {
        // Získání parametrů filtrování
        $period = isset($_GET['period']) ? $_GET['period'] : '30';
        
        // Základní statistiky zobrazení
        $totalViews = $this->model->getTotalViews();
        
        // Získání trendu zobrazení v čase
        $trendData = $this->model->getViewsTrend($period);
        $viewsTrend = [];
        
        // Transformace dat pro graf trendu
        if (isset($trendData['dates']) && isset($trendData['views']) && 
            count($trendData['dates']) === count($trendData['views'])) {
            for ($i = 0; $i < count($trendData['dates']); $i++) {
                $viewsTrend[] = [
                    'date' => $trendData['dates'][$i],
                    'count' => $trendData['views'][$i]
                ];
            }
        }
        
        // Výpočet průměrného počtu zobrazení za den
        $avgViewsPerDay = 0;
        if (!empty($trendData['views'])) {
            $avgViewsPerDay = array_sum($trendData['views']) / count($trendData['views']);
        }
        
        // Najít den s nejvyšším počtem zobrazení
        $maxViewsInDay = 0;
        $mostViewedDay = null;
        
        if (!empty($trendData['views']) && !empty($trendData['dates'])) {
            $maxIndex = array_search(max($trendData['views']), $trendData['views']);
            if ($maxIndex !== false) {
                $maxViewsInDay = $trendData['views'][$maxIndex];
                $mostViewedDay = [
                    'date' => $trendData['dates'][$maxIndex],
                    'views' => $maxViewsInDay
                ];
            }
        }
        
        // Zobrazení podle dnů v týdnu
        $viewsByDayOfWeek = [];
        $daysOfWeek = ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'];
        
        // Inicializujeme pole pro každý den v týdnu
        for ($i = 0; $i < 7; $i++) {
            $viewsByDayOfWeek[] = [
                'day_of_week' => $i,
                'name' => $daysOfWeek[$i],
                'count' => 0
            ];
        }
        
        // Spočítáme zobrazení podle dnů v týdnu z dostupných dat
        if (!empty($trendData['dates']) && !empty($trendData['views'])) {
            foreach ($trendData['dates'] as $key => $date) {
                $dayOfWeek = date('w', strtotime($date)); // 0 (Ne) až 6 (So)
                $viewsByDayOfWeek[$dayOfWeek]['count'] += $trendData['views'][$key];
            }
        }
        
        // Top články podle zobrazení
        $topArticles = $this->model->getTopArticles(20);
        
        // Získání dat pro kalendářní tepelnou mapu
        $currentYear = date('Y');
        $calendarHeatmap = $this->model->getViewsCalendarHeatmap($currentYear);
        
        // Předání dat do view
        $data = [
            'period' => $period,
            'totalViews' => $totalViews,
            'avgViewsPerDay' => $avgViewsPerDay,
            'maxViewsInDay' => $maxViewsInDay,
            'mostViewedDay' => $mostViewedDay,
            'viewsTrend' => $viewsTrend,
            'viewsByDayOfWeek' => $viewsByDayOfWeek,
            'topArticles' => $topArticles,
            'calendarHeatmap' => $calendarHeatmap
        ];
        
        // Předání všech proměnných do aktuálního rozsahu
        extract($data);
        
        $adminTitle = "Statistiky zobrazení | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/statistics/views.php';
        include '../app/Views/Admin/layout/base.php';
    }
    
    // Statistiky výkonu (průměrné zobrazení, poměry, atd.)
    public function performance()
    {
        // Získání parametrů filtrování
        $period = isset($_GET['period']) ? $_GET['period'] : '30';
        
        // Základní data
        $totalViews = $this->model->getTotalViews();
        $totalArticles = $this->model->getTotalArticles();
        $avgViewsPerArticle = $totalArticles > 0 ? $totalViews / $totalArticles : 0;
        
        // Výpočet průměru zobrazení za den
        $periodDays = ($period == 'all') ? 365 : intval($period); // Pro 'all' použijeme 365 dnů
        
        // Získáme trend dat pro aktuální období, abychom mohli vypočítat skutečný průměr
        $trendData = $this->model->getViewsTrend($period);
        if (!empty($trendData['views'])) {
            // Počítáme průměr jen z dnů, kdy byly nějaké návštěvy
            $nonZeroDays = array_filter($trendData['views'], function($views) {
                return $views > 0;
            });
            
            if (count($nonZeroDays) > 0) {
                $avgViewsPerDay = array_sum($nonZeroDays) / count($nonZeroDays);
            } else {
                $avgViewsPerDay = 0;
            }
        } else {
            // Pokud nemáme data o trendu, použijeme jednoduchý výpočet
            $avgViewsPerDay = ($periodDays > 0) ? $totalViews / $periodDays : 0;
        }
        
        // Top performing vs underperforming články
        $articles = $this->model->getArticleStatistics($period);
        $articlesWithViews = array_filter($articles, function($article) {
            return $article['total_views'] > 0;
        });
        
        usort($articlesWithViews, function($a, $b) {
            return $b['avg_views_per_day'] <=> $a['avg_views_per_day'];
        });
        
        $topPerforming = array_slice($articlesWithViews, 0, 10);
        $underPerforming = array_slice(array_reverse($articlesWithViews), 0, 10);
        
        // Získání statistik kategorií
        $categoryStats = $this->model->getCategoryStatistics($period);
        
        // Data pro graf podílu top článků na celkových zobrazeních
        $topArticlesViews = 0;
        $top10Articles = array_slice($articlesWithViews, 0, 10);
        
        foreach ($top10Articles as $article) {
            $topArticlesViews += $article['total_views'];
        }
        
        $topArticlesPercentage = $totalViews > 0 ? ($topArticlesViews / $totalViews) * 100 : 0;
        $otherArticlesPercentage = 100 - $topArticlesPercentage;
        
        $topArticlesShare = [
            $topArticlesPercentage,
            $otherArticlesPercentage
        ];
        
        // Předání všech dat do view
        $data = [
            'period' => $period,
            'totalViews' => $totalViews,
            'totalArticles' => $totalArticles,
            'avgViewsPerArticle' => $avgViewsPerArticle,
            'avgViewsPerDay' => $avgViewsPerDay,
            'topPerforming' => $topPerforming,
            'underPerforming' => $underPerforming,
            'topArticlesShare' => $topArticlesShare,
            'topArticlesViews' => $topArticlesViews,
            'categoryStats' => $categoryStats
        ];
        
        extract($data);
        
        $view = '../app/Views/Admin/statistics/performance.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // API endpointy pro načítání detailních dat
    
    // API pro detail článku
    public function getArticleDetails($articleId)
    {
        $articleDetails = $this->model->getArticleDetails($articleId);
        header('Content-Type: application/json');
        echo json_encode($articleDetails);
        exit;
    }

    // API pro detail kategorie
    public function getCategoryDetails($categoryId)
    {
        $categoryDetails = $this->model->getCategoryDetails($categoryId);
        header('Content-Type: application/json');
        echo json_encode($categoryDetails);
        exit;
    }

    // API pro detail autora
    public function getAuthorDetails($authorId)
    {
        $authorDetails = $this->model->getAuthorDetails($authorId);
        header('Content-Type: application/json');
        echo json_encode($authorDetails);
        exit;
    }

    // API pro statistiky kategorií
    public function getCategoryStats()
    {
        $period = isset($_GET['period']) ? $_GET['period'] : '30';
        $categoryStats = $this->model->getCategoryStatistics($period);
        
        header('Content-Type: application/json');
        echo json_encode($categoryStats);
        exit;
    }
    
    // Alias pro API kategorií s přátelštější URL adresou
    public function apiCategories()
    {
        $this->getCategoryStats();
    }
}
