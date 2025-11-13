<?php

namespace App\Models;

class FlashNews
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Získá všechny flash news
     */
    public function getAll($activeOnly = false)
    {
        try {
            $sql = "SELECT fn.*, u.jmeno as created_by_name 
                    FROM flash_news fn 
                    LEFT JOIN users u ON fn.created_by = u.id";
            
            if ($activeOnly) {
                $sql .= " WHERE fn.is_active = 1";
            }
            
            $sql .= " ORDER BY fn.sort_order ASC, fn.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('FlashNews getAll error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Získá flash news podle ID
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT fn.*, u.jmeno as created_by_name 
                    FROM flash_news fn 
                    LEFT JOIN users u ON fn.created_by = u.id 
                    WHERE fn.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('FlashNews getById error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vytvoří novou flash news
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO flash_news (title, type, is_active, sort_order, created_by) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['title'],
                $data['type'] ?? 'custom',
                $data['is_active'] ?? 1,
                $data['sort_order'] ?? 0,
                $data['created_by'] ?? null
            ]);
        } catch (Exception $e) {
            error_log('FlashNews create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aktualizuje flash news
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE flash_news 
                    SET title = ?, type = ?, is_active = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['title'],
                $data['type'] ?? 'custom',
                $data['is_active'] ?? 1,
                $data['sort_order'] ?? 0,
                $id
            ]);
        } catch (Exception $e) {
            error_log('FlashNews update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Smaže flash news
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM flash_news WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log('FlashNews delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Přepne aktivní stav flash news
     */
    public function toggleActive($id)
    {
        try {
            $sql = "UPDATE flash_news 
                    SET is_active = NOT is_active, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log('FlashNews toggleActive error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aktualizuje pořadí flash news
     */
    public function updateSortOrder($id, $sortOrder)
    {
        try {
            $sql = "UPDATE flash_news 
                    SET sort_order = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$sortOrder, $id]);
        } catch (Exception $e) {
            error_log('FlashNews updateSortOrder error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Získá flash news pro zobrazení na webu
     */
    public function getForDisplay()
    {
        try {
            $sql = "SELECT title, type FROM flash_news 
                    WHERE is_active = 1 
                    ORDER BY sort_order ASC, created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('FlashNews getForDisplay error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Získá statistiky flash news
     */
    public function getStats()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN type = 'news' THEN 1 ELSE 0 END) as news_count,
                        SUM(CASE WHEN type = 'tech' THEN 1 ELSE 0 END) as tech_count,
                        SUM(CASE WHEN type = 'custom' THEN 1 ELSE 0 END) as custom_count
                    FROM flash_news";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('FlashNews getStats error: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'news_count' => 0,
                'tech_count' => 0,
                'custom_count' => 0
            ];
        }
    }
}
