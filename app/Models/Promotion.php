<?php

namespace App\Models;

use PDO;

class Promotion
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Získá aktuální propagace
     * 
     * @return array
     */
    public function getCurrentPromotions()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.nazev, c.url 
            FROM propagace p 
            JOIN clanky c ON p.id_clanku = c.id 
            WHERE p.zacatek <= NOW() AND p.konec >= NOW() 
            ORDER BY p.zacatek DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá budoucí propagace
     * 
     * @return array
     */
    public function getUpcomingPromotions()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.nazev, c.url 
            FROM propagace p 
            JOIN clanky c ON p.id_clanku = c.id 
            WHERE p.zacatek > NOW() 
            ORDER BY p.zacatek ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá historické propagace
     * 
     * @return array
     */
    public function getHistoricalPromotions()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.nazev, c.url 
            FROM propagace p 
            JOIN clanky c ON p.id_clanku = c.id 
            WHERE p.konec < NOW() 
            ORDER BY p.konec DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá propagaci podle ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getPromotionById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nazev, c.url 
            FROM propagace p 
            JOIN clanky c ON p.id_clanku = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Získá aktuální a budoucí propagace
     * 
     * @return array
     */
    public function getCurrentAndFuturePromotions()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.nazev, c.url 
            FROM propagace p 
            JOIN clanky c ON p.id_clanku = c.id 
            WHERE p.konec >= NOW() 
            ORDER BY p.zacatek ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá poslední propagaci pro daný článek
     * 
     * @param int $articleId
     * @return array|false
     */
    public function getLastPromotionForArticle($articleId)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM propagace 
            WHERE id_clanku = ? 
            ORDER BY konec DESC 
            LIMIT 1
        ");
        $stmt->execute([$articleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Zkontroluje, zda se propagace překrývá s jinou propagací stejného článku
     * 
     * @param int $articleId
     * @param string $start
     * @param string $end
     * @return array
     */
    public function getOverlappingPromotions($articleId, $start, $end)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM propagace 
            WHERE id_clanku = ? 
            AND (
                (zacatek <= ? AND konec >= ?) OR
                (zacatek <= ? AND konec >= ?) OR
                (zacatek >= ? AND konec <= ?)
            )
        ");
        $stmt->execute([
            $articleId, 
            $start, $start,  // Začátek nové propagace je během existující
            $end, $end,      // Konec nové propagace je během existující
            $start, $end     // Nová propagace zcela obsahuje existující
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vytvoří novou propagaci
     * 
     * @param int $articleId
     * @param int $userId
     * @param string $start
     * @param string $end
     * @return int Identifikátor nově vytvořené propagace
     */
    public function createPromotion($articleId, $userId, $start, $end)
    {
        $stmt = $this->db->prepare("
            INSERT INTO propagace (id_clanku, user_id, zacatek, konec) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$articleId, $userId, $start, $end]);
        return $this->db->lastInsertId();
    }

    /**
     * Smaže propagaci
     * 
     * @param int $id
     * @return bool
     */
    public function deletePromotion($id)
    {
        $stmt = $this->db->prepare("DELETE FROM propagace WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Získá všechny propagace pro daný článek
     * 
     * @param int $articleId
     * @return array
     */
    public function getPromotionsForArticle($articleId)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM propagace 
            WHERE id_clanku = ? 
            ORDER BY zacatek ASC
        ");
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 