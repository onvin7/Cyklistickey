<?php

namespace App\Models;

class Statistics
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getTotalViews()
    {
        $query = "SELECT SUM(pocet) AS total FROM views_clanku";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ? $result['total'] : 0;
    }

    public function getAllArticleViews()
    {
        $query = "SELECT clanky.id, clanky.nazev, SUM(views_clanku.pocet) AS total_views
                  FROM clanky
                  LEFT JOIN views_clanku ON clanky.id = views_clanku.id_clanku
                  GROUP BY clanky.id
                  ORDER BY total_views DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getArticleViewsAdmin()
    {
        $query = "SELECT clanky.id, clanky.nazev, SUM(views_clanku.pocet) AS pocet_zobrazeni
                  FROM views_clanku
                  JOIN clanky ON views_clanku.id_clanku = clanky.id
                  GROUP BY clanky.id, clanky.nazev
                  ORDER BY pocet_zobrazeni DESC
                  LIMIT 10";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getArticleViewsById($articleId)
    {
        $query = "SELECT datum, pocet FROM views_clanku WHERE id_clanku = :articleId ORDER BY datum ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTopArticles($limit = 5)
    {
        $query = "SELECT clanky.id, clanky.nazev, SUM(views_clanku.pocet) AS total_views
                  FROM clanky
                  LEFT JOIN views_clanku ON clanky.id = views_clanku.id_clanku
                  GROUP BY clanky.id
                  ORDER BY total_views DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTopArticlesForPeriod($limit = 20, $days = 7)
    {
        // Získáme nejčtenějších X článků za posledních Y dní
        $query = "SELECT 
                    clanky.id, 
                    clanky.nazev, 
                    SUM(views_clanku.pocet) AS total_views
                  FROM views_clanku
                  JOIN clanky ON views_clanku.id_clanku = clanky.id
                  WHERE views_clanku.datum >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  GROUP BY clanky.id, clanky.nazev
                  ORDER BY total_views DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $topArticles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Pro každý článek získáme detailní data o počtu zobrazení za každý den v daném období
        $result = [];
        $dates = [];
        
        // Vygenerujeme seznam dnů pro dané období
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
        }
        
        foreach ($topArticles as $article) {
            $articleId = $article['id'];
            
            // Pro každý článek získáme počty zobrazení za jednotlivé dny
            $query = "SELECT 
                        datum, 
                        pocet
                      FROM views_clanku
                      WHERE id_clanku = :articleId
                      AND datum >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                      ORDER BY datum ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
            $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
            $stmt->execute();
            $dailyViews = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Vytvoříme asociativní pole datum => počet zobrazení
            $viewsByDate = [];
            foreach ($dailyViews as $view) {
                $viewsByDate[$view['datum']] = (int)$view['pocet'];
            }
            
            // Vytvoříme pole s daty, doplníme nuly pro dny bez zobrazení
            $data = [];
            foreach ($dates as $date) {
                $data[] = $viewsByDate[$date] ?? 0;
            }
            
            $result[] = [
                'id' => $articleId,
                'nazev' => $article['nazev'],
                'total_views' => (int)$article['total_views'],
                'dates' => $dates,
                'data' => $data
            ];
        }
        
        return [
            'dates' => $dates,
            'articles' => $result
        ];
    }

    public function getTotalArticles()
    {
        $query = "SELECT COUNT(*) AS total FROM clanky";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ? $result['total'] : 0;
    }

    public function getTotalCategories()
    {
        $query = "SELECT COUNT(*) AS total FROM kategorie";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ? $result['total'] : 0;
    }

    public function getCategoryStatistics()
    {
        $query = "SELECT kategorie.id, kategorie.nazev_kategorie AS name, 
                  COUNT(DISTINCT ck.id_clanku) AS article_count,
                  COALESCE(SUM(v.pocet), 0) AS views
                  FROM kategorie
                  LEFT JOIN clanky_kategorie ck ON kategorie.id = ck.id_kategorie
                  LEFT JOIN clanky ON ck.id_clanku = clanky.id
                  LEFT JOIN views_clanku v ON clanky.id = v.id_clanku
                  GROUP BY kategorie.id
                  ORDER BY views DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAuthorStatistics()
    {
        $query = "SELECT users.id, CONCAT(users.name, ' ', users.surname) AS name, 
                  COUNT(DISTINCT clanky.id) AS article_count, 
                  COALESCE(SUM(v.pocet), 0) AS total_views
                  FROM users
                  LEFT JOIN clanky ON users.id = clanky.user_id
                  LEFT JOIN views_clanku v ON clanky.id = v.id_clanku
                  GROUP BY users.id
                  ORDER BY total_views DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getDetailedArticleStatistics($limit = 10)
    {
        $query = "SELECT c.id, c.nazev, c.datum, 
                  CONCAT(u.name, ' ', u.surname) AS autor,
                  k.nazev_kategorie AS kategorie,
                  COALESCE(SUM(v.pocet), 0) AS total_views,
                  COALESCE(SUM(v.pocet) / DATEDIFF(CURDATE(), c.datum), 0) AS avg_views_per_day
                  FROM clanky c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  GROUP BY c.id
                  ORDER BY total_views DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getViewsTrend($days = 30)
    {
        $dates = [];
        $views = [];
        
        // Generujeme pole dat pro posledních X dní
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            
            $query = "SELECT COALESCE(SUM(pocet), 0) AS daily_views 
                      FROM views_clanku 
                      WHERE datum = :date";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $views[] = (int)$result['daily_views'];
        }
        
        return [
            'dates' => $dates,
            'views' => $views
        ];
    }

    public function getArticleViewsTrend($articleId, $days = 30)
    {
        $views = [];
        
        // Generujeme pole dat pro posledních X dní
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            $query = "SELECT COALESCE(SUM(pocet), 0) AS daily_views 
                      FROM views_clanku 
                      WHERE id_clanku = :articleId AND datum = :date";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $views[] = (int)$result['daily_views'];
        }
        
        return $views;
    }

    public function getAllCategories()
    {
        $query = "SELECT id, nazev_kategorie AS nazev FROM kategorie ORDER BY nazev_kategorie";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllAuthors()
    {
        $query = "SELECT id, CONCAT(name, ' ', surname) AS name FROM users ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getArticleStatistics($dateRange = '30', $categoryId = 0, $authorId = 0, $sort = 'views_desc')
    {
        $query = "SELECT c.id, c.nazev, c.datum, 
                  CONCAT(u.name, ' ', u.surname) AS autor,
                  k.nazev_kategorie AS kategorie,
                  COALESCE(SUM(v.pocet), 0) AS total_views,
                  COALESCE(SUM(v.pocet) / DATEDIFF(CURDATE(), c.datum), 0) AS avg_views_per_day
                  FROM clanky c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku";

        // Filtr podle data
        if ($dateRange != 'all') {
            $query .= " WHERE c.datum >= DATE_SUB(CURDATE(), INTERVAL :dateRange DAY)";
        }

        // Filtr podle kategorie
        if ($categoryId > 0) {
            $query .= ($dateRange != 'all' ? " AND" : " WHERE") . " ck.id_kategorie = :categoryId";
        }

        // Filtr podle autora
        if ($authorId > 0) {
            $query .= (($dateRange != 'all' || $categoryId > 0) ? " AND" : " WHERE") . " c.user_id = :authorId";
        }

        $query .= " GROUP BY c.id";

        // Řazení
        switch ($sort) {
            case 'views_desc':
                $query .= " ORDER BY total_views DESC";
                break;
            case 'views_asc':
                $query .= " ORDER BY total_views ASC";
                break;
            case 'date_desc':
                $query .= " ORDER BY c.datum DESC";
                break;
            case 'date_asc':
                $query .= " ORDER BY c.datum ASC";
                break;
            case 'avg_views_desc':
                $query .= " ORDER BY avg_views_per_day DESC";
                break;
            default:
                $query .= " ORDER BY total_views DESC";
        }

        $stmt = $this->db->prepare($query);

        // Binding parametrů
        if ($dateRange != 'all') {
            $stmt->bindParam(':dateRange', $dateRange, \PDO::PARAM_STR);
        }
        if ($categoryId > 0) {
            $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
        }
        if ($authorId > 0) {
            $stmt->bindParam(':authorId', $authorId, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getViewsDistribution()
    {
        $query = "SELECT 
                  CASE 
                      WHEN views_count = 0 THEN '0'
                      WHEN views_count BETWEEN 1 AND 10 THEN '1-10'
                      WHEN views_count BETWEEN 11 AND 50 THEN '11-50'
                      WHEN views_count BETWEEN 51 AND 100 THEN '51-100'
                      WHEN views_count BETWEEN 101 AND 500 THEN '101-500'
                      WHEN views_count BETWEEN 501 AND 1000 THEN '501-1000'
                      ELSE '1000+' 
                  END AS views_range,
                  COUNT(*) AS article_count
                  FROM (
                      SELECT c.id, COALESCE(SUM(v.pocet), 0) AS views_count
                      FROM clanky c
                      LEFT JOIN views_clanku v ON c.id = v.id_clanku
                      GROUP BY c.id
                  ) AS article_views
                  GROUP BY views_range
                  ORDER BY FIELD(views_range, '0', '1-10', '11-50', '51-100', '101-500', '501-1000', '1000+')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPublishingTrend()
    {
        $query = "SELECT 
                  DATE_FORMAT(datum, '%Y-%m') AS month,
                  COUNT(*) AS article_count
                  FROM clanky
                  WHERE datum >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  GROUP BY month
                  ORDER BY month";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCategoriesExtendedStats($period = '30')
    {
        $query = "SELECT 
                  k.id, 
                  k.nazev_kategorie AS name, 
                  COUNT(DISTINCT c.id) AS articles_count,
                  COALESCE(SUM(v.pocet), 0) AS views,
                  AVG(COALESCE(article_views.avg_per_day, 0)) AS avg_views_per_day
                  FROM kategorie k
                  LEFT JOIN clanky_kategorie ck ON k.id = ck.id_kategorie
                  LEFT JOIN clanky c ON ck.id_clanku = c.id";
                  
        if ($period != 'all') {
            $query .= " LEFT JOIN views_clanku v ON c.id = v.id_clanku AND v.datum >= DATE_SUB(CURDATE(), INTERVAL :period DAY)";
        } else {
            $query .= " LEFT JOIN views_clanku v ON c.id = v.id_clanku";
        }
                  
        $query .= " LEFT JOIN (
                      SELECT 
                          c.id, 
                          COALESCE(SUM(v.pocet) / GREATEST(DATEDIFF(CURDATE(), c.datum), 1), 0) AS avg_per_day
                      FROM clanky c
                      LEFT JOIN views_clanku v ON c.id = v.id_clanku";
                      
        if ($period != 'all') {
            $query .= " WHERE v.datum >= DATE_SUB(CURDATE(), INTERVAL :period2 DAY)";
        }
                      
        $query .= " GROUP BY c.id
                  ) AS article_views ON c.id = article_views.id
                  GROUP BY k.id
                  ORDER BY views DESC";

        $stmt = $this->db->prepare($query);
        
        if ($period != 'all') {
            $stmt->bindParam(':period', $period, \PDO::PARAM_STR);
            $stmt->bindParam(':period2', $period, \PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCategoryTrendPeriods($period = '30')
    {
        $dates = [];
        if ($period == '7' || $period == '30') {
            for ($i = $period - 1; $i >= 0; $i--) {
                $dates[] = date('Y-m-d', strtotime("-$i days"));
            }
        } else if ($period == '90') {
            // Pro 90 dní uděláme týdenní intervaly
            for ($i = 12; $i >= 0; $i--) {
                $week = $i * 7;
                $dates[] = date('Y-m-d', strtotime("-$week days"));
            }
        } else if ($period == '365' || $period == 'all') {
            // Pro rok uděláme měsíční intervaly
            for ($i = 11; $i >= 0; $i--) {
                $dates[] = date('Y-m', strtotime("-$i months")).'-01';
            }
        }
        return $dates;
    }

    public function getCategoriesTrendData($period = '30')
    {
        $categories = $this->getAllCategories();
        $dates = $this->getCategoryTrendPeriods($period);
        
        $result = [];
        foreach ($categories as $category) {
            $data = [];
            
            if ($period == '7' || $period == '30') {
                // Denní data
                foreach ($dates as $date) {
                    $query = "SELECT COALESCE(SUM(v.pocet), 0) AS views
                              FROM views_clanku v
                              JOIN clanky c ON v.id_clanku = c.id
                              JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                              WHERE ck.id_kategorie = :categoryId
                              AND v.datum = :date";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':categoryId', $category['id'], \PDO::PARAM_INT);
                    $stmt->bindParam(':date', $date);
                    $stmt->execute();
                    $result2 = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $data[] = (int)$result2['views'];
                }
            } else if ($period == '90') {
                // Týdenní data
                for ($i = 12; $i >= 0; $i--) {
                    $endDate = date('Y-m-d', strtotime("-".($i*7)." days"));
                    $startDate = date('Y-m-d', strtotime("-".($i*7+6)." days"));
                    
                    $query = "SELECT COALESCE(SUM(v.pocet), 0) AS views
                              FROM views_clanku v
                              JOIN clanky c ON v.id_clanku = c.id
                              JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                              WHERE ck.id_kategorie = :categoryId
                              AND v.datum BETWEEN :startDate AND :endDate";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':categoryId', $category['id'], \PDO::PARAM_INT);
                    $stmt->bindParam(':startDate', $startDate);
                    $stmt->bindParam(':endDate', $endDate);
                    $stmt->execute();
                    $result2 = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $data[] = (int)$result2['views'];
                }
            } else {
                // Měsíční data
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('Y-m', strtotime("-$i months"));
                    
                    $query = "SELECT COALESCE(SUM(v.pocet), 0) AS views
                              FROM views_clanku v
                              JOIN clanky c ON v.id_clanku = c.id
                              JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                              WHERE ck.id_kategorie = :categoryId
                              AND DATE_FORMAT(v.datum, '%Y-%m') = :month";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':categoryId', $category['id'], \PDO::PARAM_INT);
                    $stmt->bindParam(':month', $month);
                    $stmt->execute();
                    $result2 = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $data[] = (int)$result2['views'];
                }
            }
            
            $result[] = [
                'name' => $category['nazev'],
                'data' => $data
            ];
        }
        
        return [
            'dates' => $dates,
            'series' => $result
        ];
    }

    public function getCategoriesCorrelation()
    {
        $categories = $this->getAllCategories();
        $result = [];
        
        foreach ($categories as $cat1) {
            $correlations = [];
            foreach ($categories as $cat2) {
                if ($cat1['id'] == $cat2['id']) {
                    $correlations[] = [
                        'x' => $cat2['nazev'],
                        'y' => 1
                    ];
                    continue;
                }
                
                // Získáme počty článků v obou kategoriích
                $query = "SELECT COUNT(DISTINCT c1.id) AS cat1_count, 
                          COUNT(DISTINCT c2.id) AS cat2_count,
                          COUNT(DISTINCT CASE WHEN c1.id IS NOT NULL AND c2.id IS NOT NULL THEN c1.id END) AS common_count
                          FROM (
                              SELECT c.id FROM clanky c 
                              JOIN clanky_kategorie ck ON c.id = ck.id_clanku 
                              WHERE ck.id_kategorie = :cat1Id
                          ) AS c1
                          CROSS JOIN (
                              SELECT c.id FROM clanky c 
                              JOIN clanky_kategorie ck ON c.id = ck.id_clanku 
                              WHERE ck.id_kategorie = :cat2Id
                          ) AS c2
                          WHERE c1.id = c2.id";
                          
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':cat1Id', $cat1['id'], \PDO::PARAM_INT);
                $stmt->bindParam(':cat2Id', $cat2['id'], \PDO::PARAM_INT);
                $stmt->execute();
                $counts = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                // Vypočteme korelaci
                $correlation = 0;
                if ($counts['cat1_count'] > 0 && $counts['cat2_count'] > 0) {
                    $correlation = $counts['common_count'] / sqrt($counts['cat1_count'] * $counts['cat2_count']);
                }
                
                $correlations[] = [
                    'x' => $cat2['nazev'],
                    'y' => round($correlation, 2)
                ];
            }
            
            $result[] = [
                'name' => $cat1['nazev'],
                'data' => $correlations
            ];
        }
        
        return $result;
    }

    public function getAuthorsExtendedStats($period = '30')
    {
        $query = "SELECT 
                  u.id, 
                  CONCAT(u.name, ' ', u.surname) AS name, 
                  COUNT(c.id) AS article_count,
                  COALESCE(SUM(v.pocet), 0) AS total_views,
                  COALESCE(AVG(article_views.avg_per_day), 0) AS avg_views_per_day,
                  MAX(c.datum) AS last_article_date,
                  (SELECT COUNT(*) FROM clanky WHERE user_id = u.id AND datum >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) AS recent_articles
                  FROM users u
                  LEFT JOIN clanky c ON u.id = c.user_id";
                  
        if ($period != 'all') {
            $query .= " LEFT JOIN views_clanku v ON c.id = v.id_clanku AND v.datum >= DATE_SUB(CURDATE(), INTERVAL :period DAY)";
        } else {
            $query .= " LEFT JOIN views_clanku v ON c.id = v.id_clanku";
        }
                  
        $query .= " LEFT JOIN (
                      SELECT 
                          c.id, 
                          COALESCE(SUM(v.pocet) / GREATEST(DATEDIFF(CURDATE(), c.datum), 1), 0) AS avg_per_day
                      FROM clanky c
                      LEFT JOIN views_clanku v ON c.id = v.id_clanku";
                      
        if ($period != 'all') {
            $query .= " WHERE v.datum >= DATE_SUB(CURDATE(), INTERVAL :period2 DAY)";
        }
                      
        $query .= " GROUP BY c.id
                  ) AS article_views ON c.id = article_views.id
                  GROUP BY u.id
                  ORDER BY total_views DESC";

        $stmt = $this->db->prepare($query);
        
        if ($period != 'all') {
            $stmt->bindParam(':period', $period, \PDO::PARAM_STR);
            $stmt->bindParam(':period2', $period, \PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAuthorsTrend($period = '30')
    {
        // Podobně jako u kategorií, s tím rozdílem, že sledujeme autory
        $authors = $this->getAllAuthors();
        $dates = $this->getCategoryTrendPeriods($period);
        
        $result = [];
        foreach ($authors as $author) {
            $data = [];
            
            if ($period == '7' || $period == '30') {
                // Denní data
                foreach ($dates as $date) {
                    $query = "SELECT COALESCE(SUM(v.pocet), 0) AS views
                              FROM views_clanku v
                              JOIN clanky c ON v.id_clanku = c.id
                              WHERE c.user_id = :authorId
                              AND v.datum = :date";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':authorId', $author['id'], \PDO::PARAM_INT);
                    $stmt->bindParam(':date', $date);
                    $stmt->execute();
                    $result2 = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $data[] = (int)$result2['views'];
                }
            } else if ($period == '90') {
                // Týdenní data
                for ($i = 12; $i >= 0; $i--) {
                    $endDate = date('Y-m-d', strtotime("-".($i*7)." days"));
                    $startDate = date('Y-m-d', strtotime("-".($i*7+6)." days"));
                    
                    $query = "SELECT COALESCE(SUM(v.pocet), 0) AS views
                              FROM views_clanku v
                              JOIN clanky c ON v.id_clanku = c.id
                              WHERE c.user_id = :authorId
                              AND v.datum BETWEEN :startDate AND :endDate";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':authorId', $author['id'], \PDO::PARAM_INT);
                    $stmt->bindParam(':startDate', $startDate);
                    $stmt->bindParam(':endDate', $endDate);
                    $stmt->execute();
                    $result2 = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $data[] = (int)$result2['views'];
                }
            } else {
                // Měsíční data
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('Y-m', strtotime("-$i months"));
                    
                    $query = "SELECT COALESCE(SUM(v.pocet), 0) AS views
                              FROM views_clanku v
                              JOIN clanky c ON v.id_clanku = c.id
                              WHERE c.user_id = :authorId
                              AND DATE_FORMAT(v.datum, '%Y-%m') = :month";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':authorId', $author['id'], \PDO::PARAM_INT);
                    $stmt->bindParam(':month', $month);
                    $stmt->execute();
                    $result2 = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $data[] = (int)$result2['views'];
                }
            }
            
            $result[] = [
                'name' => $author['name'],
                'data' => $data
            ];
        }
        
        return [
            'dates' => $dates,
            'series' => $result
        ];
    }

    public function getAuthorsCategoryDistribution()
    {
        $authors = $this->getAllAuthors();
        $categories = $this->getAllCategories();
        
        $result = [];
        foreach ($authors as $author) {
            $categoryData = [];
            
            foreach ($categories as $category) {
                $query = "SELECT COUNT(*) AS count
                          FROM clanky c
                          JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                          WHERE c.user_id = :authorId
                          AND ck.id_kategorie = :categoryId";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':authorId', $author['id'], \PDO::PARAM_INT);
                $stmt->bindParam(':categoryId', $category['id'], \PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                $categoryData[] = [
                    'x' => $category['nazev'],
                    'y' => (int)$count['count']
                ];
            }
            
            $result[] = [
                'name' => $author['name'],
                'data' => $categoryData
            ];
        }
        
        return $result;
    }

    public function getArticleDetails($articleId)
    {
        // Základní informace o článku
        $query = "SELECT 
                  c.id, c.nazev, c.datum, c.perex, 
                  CONCAT(u.name, ' ', u.surname) AS autor,
                  k.nazev_kategorie AS kategorie,
                  COALESCE(SUM(v.pocet), 0) AS total_views
                  FROM clanky c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  WHERE c.id = :articleId
                  GROUP BY c.id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$article) {
            return null;
        }
        
        // Trend zobrazení článku za posledních 30 dní
        $dates = [];
        $views = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            
            $query = "SELECT COALESCE(SUM(pocet), 0) AS daily_views 
                      FROM views_clanku 
                      WHERE id_clanku = :articleId AND datum = :date";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $views[] = (int)$result['daily_views'];
        }
        
        $article['trend'] = [
            'dates' => $dates,
            'views' => $views
        ];
        
        return $article;
    }

    public function getCategoryDetails($categoryId)
    {
        // Základní informace o kategorii
        $query = "SELECT 
                  k.id, k.nazev_kategorie AS nazev, k.url AS popis,
                  COUNT(DISTINCT c.id) AS article_count,
                  COALESCE(SUM(v.pocet), 0) AS total_views
                  FROM kategorie k
                  LEFT JOIN clanky_kategorie ck ON k.id = ck.id_kategorie
                  LEFT JOIN clanky c ON ck.id_clanku = c.id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  WHERE k.id = :categoryId
                  GROUP BY k.id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
        $stmt->execute();
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$category) {
            return null;
        }
        
        // Trend zobrazení kategorie za posledních 30 dní
        $dates = [];
        $views = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            
            $query = "SELECT COALESCE(SUM(v.pocet), 0) AS daily_views 
                      FROM views_clanku v
                      JOIN clanky c ON v.id_clanku = c.id
                      JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                      WHERE ck.id_kategorie = :categoryId AND v.datum = :date";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $views[] = (int)$result['daily_views'];
        }
        
        $category['trend'] = [
            'dates' => $dates,
            'views' => $views
        ];
        
        // Top 5 článků v kategorii
        $query = "SELECT c.id, c.nazev, COALESCE(SUM(v.pocet), 0) AS total_views
                  FROM clanky c
                  JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  WHERE ck.id_kategorie = :categoryId
                  GROUP BY c.id
                  ORDER BY total_views DESC
                  LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
        $stmt->execute();
        $category['top_articles'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $category;
    }

    public function getAuthorDetails($authorId)
    {
        // Základní informace o autorovi
        $query = "SELECT 
                  u.id, CONCAT(u.name, ' ', u.surname) AS name, u.email,
                  COUNT(c.id) AS article_count,
                  COALESCE(SUM(v.pocet), 0) AS total_views,
                  MAX(c.datum) AS last_article_date
                  FROM users u
                  LEFT JOIN clanky c ON u.id = c.user_id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  WHERE u.id = :authorId
                  GROUP BY u.id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':authorId', $authorId, \PDO::PARAM_INT);
        $stmt->execute();
        $author = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$author) {
            return null;
        }
        
        // Trend zobrazení autora za posledních 30 dní
        $dates = [];
        $views = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            
            $query = "SELECT COALESCE(SUM(v.pocet), 0) AS daily_views 
                      FROM views_clanku v
                      JOIN clanky c ON v.id_clanku = c.id
                      WHERE c.user_id = :authorId AND v.datum = :date";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':authorId', $authorId, \PDO::PARAM_INT);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $views[] = (int)$result['daily_views'];
        }
        
        $author['trend'] = [
            'dates' => $dates,
            'views' => $views
        ];
        
        // Top 5 článků autora
        $query = "SELECT c.id, c.nazev, COALESCE(SUM(v.pocet), 0) AS total_views
                  FROM clanky c
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  WHERE c.user_id = :authorId
                  GROUP BY c.id
                  ORDER BY total_views DESC
                  LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':authorId', $authorId, \PDO::PARAM_INT);
        $stmt->execute();
        $author['top_articles'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Kategorie, ve kterých autor publikuje
        $query = "SELECT k.id, k.nazev_kategorie AS nazev, COUNT(c.id) AS article_count
                  FROM kategorie k
                  JOIN clanky_kategorie ck ON k.id = ck.id_kategorie
                  JOIN clanky c ON ck.id_clanku = c.id
                  WHERE c.user_id = :authorId
                  GROUP BY k.id
                  ORDER BY article_count DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':authorId', $authorId, \PDO::PARAM_INT);
        $stmt->execute();
        $author['categories'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $author;
    }

}
