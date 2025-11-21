<?php

namespace App\Models;

class LinkClickEvent
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Uloží detailní informace o kliku
     */
    public function recordEvent($linkClickId, $articleId, $url, $data)
    {
        $query = "INSERT INTO link_click_events (
            link_click_id, id_clanku, url, clicked_at, ip_address, user_agent, 
            referrer, session_id, device_type, browser, os, country, city,
            time_on_page, link_position, scroll_depth, link_type,
            viewport_width, viewport_height
        ) VALUES (
            :link_click_id, :id_clanku, :url, NOW(), :ip_address, :user_agent,
            :referrer, :session_id, :device_type, :browser, :os, :country, :city,
            :time_on_page, :link_position, :scroll_depth, :link_type,
            :viewport_width, :viewport_height
        )";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':link_click_id', $linkClickId, \PDO::PARAM_INT);
        $stmt->bindValue(':id_clanku', $articleId, \PDO::PARAM_INT);
        $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
        $stmt->bindValue(':ip_address', $data['ip_address'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':user_agent', $data['user_agent'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':referrer', $data['referrer'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':session_id', $data['session_id'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':device_type', $data['device_type'] ?? 'unknown', \PDO::PARAM_STR);
        $stmt->bindValue(':browser', $data['browser'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':os', $data['os'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':country', $data['country'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':city', $data['city'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':time_on_page', $data['time_on_page'] ?? null, \PDO::PARAM_INT);
        $stmt->bindValue(':link_position', $data['link_position'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':scroll_depth', $data['scroll_depth'] ?? null, \PDO::PARAM_INT);
        $stmt->bindValue(':link_type', $data['link_type'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':viewport_width', $data['viewport_width'] ?? null, \PDO::PARAM_INT);
        $stmt->bindValue(':viewport_height', $data['viewport_height'] ?? null, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            // Logování chyby pro debugging
            $logFile = dirname(dirname(dirname(__DIR__))) . '/logs/link_tracking.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            @file_put_contents($logFile, date('Y-m-d H:i:s') . " - LinkClickEvent PDO error: " . $e->getMessage() . "\n", FILE_APPEND);
            @file_put_contents($logFile, date('Y-m-d H:i:s') . " - SQL: " . $query . "\n", FILE_APPEND);
            @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Data: " . print_r($data, true) . "\n", FILE_APPEND);
            throw $e; // Znovu vyhodit, aby to zachytil controller
        }
    }

    /**
     * Získá všechny eventy pro článek
     */
    public function getEventsByArticle($articleId, $limit = 100)
    {
        $query = "SELECT * FROM link_click_events 
                  WHERE id_clanku = :article_id 
                  ORDER BY clicked_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá statistiky zařízení pro článek
     */
    public function getDeviceStatsByArticle($articleId)
    {
        $query = "SELECT device_type, COUNT(*) as count 
                  FROM link_click_events 
                  WHERE id_clanku = :article_id 
                  GROUP BY device_type 
                  ORDER BY count DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá statistiky prohlížečů pro článek
     */
    public function getBrowserStatsByArticle($articleId)
    {
        $query = "SELECT browser, COUNT(*) as count 
                  FROM link_click_events 
                  WHERE id_clanku = :article_id AND browser IS NOT NULL
                  GROUP BY browser 
                  ORDER BY count DESC 
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá statistiky zemí pro článek
     */
    public function getCountryStatsByArticle($articleId)
    {
        $query = "SELECT country, COUNT(*) as count 
                  FROM link_click_events 
                  WHERE id_clanku = :article_id AND country IS NOT NULL
                  GROUP BY country 
                  ORDER BY count DESC 
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá časové rozložení kliků (po hodinách)
     */
    public function getHourlyStatsByArticle($articleId)
    {
        $query = "SELECT HOUR(clicked_at) as hour, COUNT(*) as count 
                  FROM link_click_events 
                  WHERE id_clanku = :article_id 
                  GROUP BY HOUR(clicked_at) 
                  ORDER BY hour";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá průměrný čas strávený na stránce před kliknutím
     */
    public function getAvgTimeOnPageByArticle($articleId)
    {
        $query = "SELECT AVG(time_on_page) as avg_time, 
                         MIN(time_on_page) as min_time, 
                         MAX(time_on_page) as max_time,
                         COUNT(*) as total_clicks
                  FROM link_click_events 
                  WHERE id_clanku = :article_id AND time_on_page IS NOT NULL";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá unikátní IP adresy pro článek
     */
    public function getUniqueIPsByArticle($articleId)
    {
        $query = "SELECT COUNT(DISTINCT ip_address) as unique_ips 
                  FROM link_click_events 
                  WHERE id_clanku = :article_id AND ip_address IS NOT NULL";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['unique_ips'] ?? 0;
    }
}

