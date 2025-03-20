<?php

namespace App\Models;

class Statistics
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
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

    // Ponecháme původní metodu pro zpětnou kompatibilitu, ale implementujeme ji pomocí nové metody
    public function getTop20ArticlesLastWeek()
    {
        return $this->getTopArticlesForPeriod(20, 7);
    }
}
