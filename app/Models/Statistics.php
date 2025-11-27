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

    // Pomocná metoda pro získání SQL podmínky filtrování podle data na základě období
    private function getDateFilter($period = 'all')
    {
        if ($period == 'all') {
            return ''; // Žádný filtr pro všechna data
        }
        
        // Převést období na celé číslo
        $days = intval($period);
        
        // Pokud je období platné číslo, vrátíme SQL podmínku pro filtrování podle data
        if ($days > 0) {
            return "v.datum >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
        }
        
        return '';
    }

    public function getCategoryStatistics($period = 'all')
    {
        $db = $this->db;
        
        // Určení časového období pro filtrování
        $dateFilter = $this->getDateFilter($period);
        
        try {
            // Základní SQL dotaz pro získání statistik kategorií
            $sql = "
                SELECT 
                    k.nazev_kategorie AS name,
                    COALESCE(SUM(v.pocet), 0) AS views
                FROM 
                    kategorie k
                LEFT JOIN 
                    clanky_kategorie ck ON k.id = ck.id_kategorie
                LEFT JOIN 
                    clanky c ON ck.id_clanku = c.id
                LEFT JOIN 
                    views_clanku v ON c.id = v.id_clanku
            ";
            
            // Přidání filtru období, pokud je specifikováno
            if ($dateFilter) {
                $sql .= " WHERE " . $dateFilter;
            }
            
            // Seskupení podle kategorií a seřazení podle počtu zobrazení
            $sql .= "
                GROUP BY k.id
                ORDER BY views DESC
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Pokud nemáme žádné výsledky, vrátíme prázdné pole
            if (empty($results)) {
                return [];
            }
            
            return $results;
            
        } catch (\PDOException $e) {
            error_log("Chyba při získávání statistik kategorií: " . $e->getMessage());
            return [];
        }
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
        $query = "SELECT view_range, COUNT(*) AS article_count
                  FROM (
                      SELECT 
                        CASE 
                          WHEN COALESCE(SUM(v.pocet), 0) = 0 THEN '0'
                          WHEN COALESCE(SUM(v.pocet), 0) BETWEEN 1 AND 10 THEN '1-10'
                          WHEN COALESCE(SUM(v.pocet), 0) BETWEEN 11 AND 50 THEN '11-50'
                          WHEN COALESCE(SUM(v.pocet), 0) BETWEEN 51 AND 100 THEN '51-100'
                          WHEN COALESCE(SUM(v.pocet), 0) BETWEEN 101 AND 500 THEN '101-500'
                          WHEN COALESCE(SUM(v.pocet), 0) BETWEEN 501 AND 1000 THEN '501-1000'
                          ELSE '1000+'
                        END AS view_range
                      FROM clanky c
                      LEFT JOIN views_clanku v ON c.id = v.id_clanku
                      GROUP BY c.id
                  ) AS article_views
                  GROUP BY view_range
                  ORDER BY FIELD(view_range, '0', '1-10', '11-50', '51-100', '101-500', '501-1000', '1000+')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Definujeme všechny očekávané rozsahy (pro případ, že by některé chyběly)
        $expectedRanges = ['0', '1-10', '11-50', '51-100', '101-500', '501-1000', '1000+'];
        $ranges = [];
        $counts = [];
        
        // Inicializujeme pole s nulovými hodnotami pro všechny očekávané rozsahy
        $countsByRange = array_fill_keys($expectedRanges, 0);
        
        // Naplníme skutečnými hodnotami
        foreach ($result as $row) {
            $countsByRange[$row['view_range']] = (int)$row['article_count'];
        }
        
        // Převedeme do formátu pro graf (zachováme pořadí)
        foreach ($expectedRanges as $range) {
            $ranges[] = $range;
            $counts[] = $countsByRange[$range];
        }
        
        return [
            'ranges' => $ranges,
            'counts' => $counts
        ];
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
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Připravíme data ve správném formátu pro graf
        $periods = [];
        $counts = [];
        
        foreach ($result as $row) {
            // Převedeme formát měsíce na čitelnější (např. "2023-01" na "Led 2023")
            $date = \DateTime::createFromFormat('Y-m', $row['month']);
            $formatted = $date ? $date->format('M Y') : $row['month'];
            
            $periods[] = $formatted;
            $counts[] = (int)$row['article_count'];
        }
        
        return [
            'periods' => $periods,
            'counts' => $counts
        ];
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
                  COALESCE(SUM(v.pocet), 0) AS total_views,
                  COALESCE(SUM(lc.click_count), 0) AS total_clicks
                  FROM clanky c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  LEFT JOIN link_clicks lc ON c.id = lc.id_clanku
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
        
        // Zajistíme, že total_clicks je vždy nastavené
        if (!isset($article['total_clicks'])) {
            $article['total_clicks'] = 0;
        }
        
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

    // Získání dat pro tepelnou mapu zobrazení podle dnů v roce
    public function getViewsCalendarHeatmap($year = null) 
    {
        // Pokud není rok specifikován, použijeme aktuální rok
        if (!$year) {
            $year = date('Y');
        }
        
        // Dotaz na získání denních zobrazení pro zadaný rok
        $query = "SELECT DATE_FORMAT(datum, '%Y-%m-%d') AS day, 
                         SUM(pocet) AS views_count
                  FROM views_clanku 
                  WHERE YEAR(datum) = :year
                  GROUP BY day
                  ORDER BY day ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Zpracujeme výsledky do pole, kde klíč je datum a hodnota je počet zobrazení
        $viewsData = [];
        $maxViews = 0;
        
        foreach ($results as $row) {
            $viewsData[$row['day']] = (int)$row['views_count'];
            $maxViews = max($maxViews, (int)$row['views_count']);
        }
        
        // Vytvoříme kompletní pole pro celý rok, včetně dnů bez dat
        $calendarData = [];
        $startDate = new \DateTime($year . '-01-01');
        $endDate = new \DateTime($year . '-12-31');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate);
        
        foreach ($dateRange as $date) {
            $day = $date->format('Y-m-d');
            $calendarData[] = [
                'date' => $day,
                'count' => isset($viewsData[$day]) ? $viewsData[$day] : 0,
                'month' => $date->format('n') - 1, // 0-indexed měsíc pro JS
                'day_of_week' => $date->format('w'), // 0 (neděle) až 6 (sobota)
                'day_of_month' => $date->format('j')
            ];
        }
        
        // Výpočet průměrné intenzity pro určení škálování barev
        $avgViewsPerDay = count($calendarData) > 0 && $maxViews > 0 ? 
            array_sum(array_column($calendarData, 'count')) / count($calendarData) : 0;
        
        return [
            'calendar_data' => $calendarData,
            'max_views' => $maxViews,
            'avg_views' => $avgViewsPerDay,
            'year' => $year
        ];
    }

    // ========== CLICK TRACKING STATISTICS ==========

    /**
     * Získá celkový počet všech kliků
     */
    public function getTotalClicks()
    {
        $query = "SELECT SUM(click_count) AS total FROM link_clicks";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ? (int)$result['total'] : 0;
    }

    /**
     * Získá průměrný počet kliků na článek
     */
    public function getAvgClicksPerArticle()
    {
        $query = "SELECT 
                  COUNT(DISTINCT id_clanku) AS articles_with_clicks,
                  SUM(click_count) AS total_clicks
                  FROM link_clicks";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $articlesWithClicks = (int)($result['articles_with_clicks'] ?? 0);
        $totalClicks = (int)($result['total_clicks'] ?? 0);
        
        return $articlesWithClicks > 0 ? $totalClicks / $articlesWithClicks : 0;
    }

    /**
     * Získá trend kliků v čase (podobně jako getViewsTrend)
     * Používá agregovaná data z link_clicks místo jednotlivých eventů
     */
    public function getClicksTrend($days = 30)
    {
        // Použijeme agregovaná data z link_clicks podle created_at nebo updated_at
        // Pokud nemáme created_at, použijeme data z link_click_events, ale filtrujeme jen validní
        $query = "SELECT DATE(clicked_at) AS date, COUNT(*) AS clicks
                  FROM link_click_events
                  WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  AND clicked_at IS NOT NULL
                  AND clicked_at <= NOW()
                  GROUP BY DATE(clicked_at)
                  ORDER BY date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Vytvoříme pole pro všechny dny v období
        $dates = [];
        $clicks = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            
            // Najdeme kliky pro tento den
            $clicksForDate = 0;
            foreach ($results as $row) {
                if ($row['date'] === $date) {
                    $clicksForDate = (int)$row['clicks'];
                    break;
                }
            }
            $clicks[] = $clicksForDate;
        }
        
        return [
            'dates' => $dates,
            'clicks' => $clicks
        ];
    }

    /**
     * Získá top články podle počtu kliků
     */
    public function getTopArticlesByClicks($limit = 10)
    {
        $query = "SELECT 
                  c.id, c.nazev, c.datum,
                  CONCAT(u.name, ' ', u.surname) AS autor,
                  GROUP_CONCAT(DISTINCT k.nazev_kategorie SEPARATOR ', ') AS kategorie,
                  COALESCE(SUM(lc.click_count), 0) AS total_clicks,
                  COALESCE(SUM(v.pocet), 0) AS total_views
                  FROM clanky c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  LEFT JOIN link_clicks lc ON c.id = lc.id_clanku
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  WHERE c.viditelnost = 1
                  GROUP BY c.id
                  HAVING total_clicks > 0
                  ORDER BY total_clicks DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá top odkazy podle počtu kliků
     */
    public function getTopLinks($limit = 10)
    {
        $query = "SELECT 
                  lc.id, lc.url, lc.link_text, lc.click_count,
                  c.id AS article_id, c.nazev AS article_name, c.url AS article_url
                  FROM link_clicks lc
                  LEFT JOIN clanky c ON lc.id_clanku = c.id
                  WHERE lc.click_count > 0
                  AND lc.url IS NOT NULL
                  AND lc.url != ''
                  ORDER BY lc.click_count DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá rozložení kliků podle kategorií
     */
    public function getClicksByCategory()
    {
        $query = "SELECT 
                  k.id, k.nazev_kategorie AS name,
                  COALESCE(SUM(lc.click_count), 0) AS clicks,
                  COUNT(DISTINCT c.id) AS articles_count
                  FROM kategorie k
                  LEFT JOIN clanky_kategorie ck ON k.id = ck.id_kategorie
                  LEFT JOIN clanky c ON ck.id_clanku = c.id
                  LEFT JOIN link_clicks lc ON c.id = lc.id_clanku
                  GROUP BY k.id
                  ORDER BY clicks DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá rozložení kliků podle autorů
     */
    public function getClicksByAuthor()
    {
        $query = "SELECT 
                  u.id, CONCAT(u.name, ' ', u.surname) AS name,
                  COALESCE(SUM(lc.click_count), 0) AS clicks,
                  COUNT(DISTINCT c.id) AS articles_count
                  FROM users u
                  LEFT JOIN clanky c ON u.id = c.user_id
                  LEFT JOIN link_clicks lc ON c.id = lc.id_clanku
                  GROUP BY u.id
                  HAVING clicks > 0
                  ORDER BY clicks DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá počet kliků pro konkrétní článek
     */
    public function getArticleClicks($articleId)
    {
        $query = "SELECT SUM(click_count) AS total_clicks
                  FROM link_clicks
                  WHERE id_clanku = :article_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_clicks'] ? (int)$result['total_clicks'] : 0;
    }

    /**
     * Získá distribuci kliků (kolik článků má kolik kliků)
     */
    public function getClicksDistribution()
    {
        $query = "SELECT 
                  CASE 
                    WHEN total_clicks = 0 THEN '0'
                    WHEN total_clicks BETWEEN 1 AND 10 THEN '1-10'
                    WHEN total_clicks BETWEEN 11 AND 50 THEN '11-50'
                    WHEN total_clicks BETWEEN 51 AND 100 THEN '51-100'
                    WHEN total_clicks BETWEEN 101 AND 500 THEN '101-500'
                    ELSE '500+'
                  END AS range_label,
                  COUNT(*) AS article_count
                  FROM (
                      SELECT c.id, COALESCE(SUM(lc.click_count), 0) AS total_clicks
                      FROM clanky c
                      LEFT JOIN link_clicks lc ON c.id = lc.id_clanku
                      GROUP BY c.id
                  ) AS article_clicks
                  GROUP BY range_label
                  ORDER BY 
                    CASE range_label
                      WHEN '0' THEN 1
                      WHEN '1-10' THEN 2
                      WHEN '11-50' THEN 3
                      WHEN '51-100' THEN 4
                      WHEN '101-500' THEN 5
                      WHEN '500+' THEN 6
                    END";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá kliky podle dnů v týdnu
     */
    public function getClicksByDayOfWeek()
    {
        $query = "SELECT 
                  DAYOFWEEK(clicked_at) - 1 AS day_of_week,
                  CASE DAYOFWEEK(clicked_at)
                    WHEN 1 THEN 'Neděle'
                    WHEN 2 THEN 'Pondělí'
                    WHEN 3 THEN 'Úterý'
                    WHEN 4 THEN 'Středa'
                    WHEN 5 THEN 'Čtvrtek'
                    WHEN 6 THEN 'Pátek'
                    WHEN 7 THEN 'Sobota'
                  END AS day_name,
                  COUNT(*) AS clicks
                  FROM link_click_events
                  WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                  AND clicked_at IS NOT NULL
                  AND clicked_at <= NOW()
                  GROUP BY DAYOFWEEK(clicked_at)
                  ORDER BY day_of_week";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá kliky podle hodin
     */
    public function getClicksByHour()
    {
        $query = "SELECT 
                  HOUR(clicked_at) AS hour,
                  COUNT(*) AS clicks
                  FROM link_click_events
                  WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                  AND clicked_at IS NOT NULL
                  AND clicked_at <= NOW()
                  GROUP BY HOUR(clicked_at)
                  ORDER BY hour";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá počet článků, které mají alespoň jeden klik
     */
    public function getArticlesWithClicks()
    {
        $query = "SELECT COUNT(DISTINCT id_clanku) AS count
                  FROM link_clicks
                  WHERE click_count > 0";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] ? (int)$result['count'] : 0;
    }

    /**
     * Získá nejklikanější odkaz
     */
    public function getTopLink()
    {
        $query = "SELECT 
                  lc.id, lc.url, lc.link_text, lc.click_count,
                  c.id AS article_id, c.nazev AS article_name, c.url AS article_url
                  FROM link_clicks lc
                  LEFT JOIN clanky c ON lc.id_clanku = c.id
                  WHERE lc.click_count > 0
                  AND lc.url IS NOT NULL
                  AND lc.url != ''
                  ORDER BY lc.click_count DESC
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Pokud není žádný odkaz, vrátíme null
        if (!$result || empty($result['url'])) {
            return null;
        }
        
        return $result;
    }

    /**
     * Získá statistiky kliků pro články (pro použití v articles() metodě)
     */
    public function getArticleStatisticsWithClicks($dateRange = '30', $categoryId = 0, $authorId = 0, $sort = 'views_desc')
    {
        $query = "SELECT c.id, c.nazev, c.datum, 
                  CONCAT(u.name, ' ', u.surname) AS autor,
                  k.nazev_kategorie AS kategorie,
                  COALESCE(SUM(v.pocet), 0) AS total_views,
                  COALESCE(SUM(lc.click_count), 0) AS total_clicks,
                  COALESCE(SUM(v.pocet) / DATEDIFF(CURDATE(), c.datum), 0) AS avg_views_per_day,
                  CASE 
                    WHEN SUM(v.pocet) > 0 THEN (SUM(lc.click_count) / SUM(v.pocet)) * 100
                    ELSE 0
                  END AS ctr
                  FROM clanky c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  LEFT JOIN views_clanku v ON c.id = v.id_clanku
                  LEFT JOIN link_clicks lc ON c.id = lc.id_clanku";

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
            case 'clicks_desc':
                $query .= " ORDER BY total_clicks DESC";
                break;
            case 'clicks_asc':
                $query .= " ORDER BY total_clicks ASC";
                break;
            case 'ctr_desc':
                $query .= " ORDER BY ctr DESC";
                break;
            case 'ctr_asc':
                $query .= " ORDER BY ctr ASC";
                break;
            case 'views_desc':
            default:
                $query .= " ORDER BY total_views DESC";
                break;
            case 'views_asc':
                $query .= " ORDER BY total_views ASC";
                break;
        }

        $stmt = $this->db->prepare($query);
        
        if ($dateRange != 'all') {
            $stmt->bindValue(':dateRange', $dateRange, \PDO::PARAM_INT);
        }
        if ($categoryId > 0) {
            $stmt->bindValue(':categoryId', $categoryId, \PDO::PARAM_INT);
        }
        if ($authorId > 0) {
            $stmt->bindValue(':authorId', $authorId, \PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
