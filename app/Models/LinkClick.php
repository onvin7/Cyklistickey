<?php

namespace App\Models;

class LinkClick
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Zaznamená klik na odkaz
     */
    public function recordClick($articleId, $url, $linkText = null)
    {
        // Nejprve zkontrolujeme, zda už existuje záznam pro tento odkaz v článku
        $query = "SELECT id, click_count FROM link_clicks 
                  WHERE id_clanku = :article_id AND url = :url 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
        $stmt->execute();
        $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existing) {
            // Aktualizujeme existující záznam
            $query = "UPDATE link_clicks 
                      SET click_count = click_count + 1,
                          link_text = COALESCE(:link_text, link_text)
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $existing['id'], \PDO::PARAM_INT);
            $stmt->bindValue(':link_text', $linkText, \PDO::PARAM_STR);
            $stmt->execute();
            return $existing['id'];
        } else {
            // Vytvoříme nový záznam
            $query = "INSERT INTO link_clicks (id_clanku, url, link_text, click_count) 
                      VALUES (:article_id, :url, :link_text, 1)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
            $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
            $stmt->bindValue(':link_text', $linkText, \PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        }
    }

    /**
     * Získá statistiky kliků pro článek
     */
    public function getClicksByArticle($articleId)
    {
        $query = "SELECT * FROM link_clicks 
                  WHERE id_clanku = :article_id 
                  ORDER BY click_count DESC, created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá všechny statistiky kliků s informacemi o článcích
     */
    public function getAllClicksWithArticles()
    {
        $query = "SELECT lc.*, c.nazev AS nazev_clanku, c.url AS url_clanku
                  FROM link_clicks lc
                  LEFT JOIN clanky c ON lc.id_clanku = c.id
                  ORDER BY lc.click_count DESC, lc.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Získá celkový počet kliků pro článek
     */
    public function getTotalClicksForArticle($articleId)
    {
        $query = "SELECT SUM(click_count) as total FROM link_clicks 
                  WHERE id_clanku = :article_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':article_id', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}

