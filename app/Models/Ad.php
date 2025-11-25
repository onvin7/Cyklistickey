<?php

namespace App\Models;

class Ad
{
    private $db;
    private $tableExists = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Zkontroluje, zda tabulka reklamy existuje
     */
    private function checkTableExists()
    {
        if ($this->tableExists !== null) {
            return $this->tableExists;
        }

        try {
            $query = "SHOW TABLES LIKE 'reklamy'";
            $stmt = $this->db->query($query);
            $this->tableExists = $stmt->rowCount() > 0;
            return $this->tableExists;
        } catch (\PDOException $e) {
            $this->tableExists = false;
            return false;
        }
    }

    /**
     * Získá aktivní reklamy
     * Vrací prázdné pole, pokud tabulka neexistuje
     */
    public function getActiveAds()
    {
        if (!$this->checkTableExists()) {
            return [];
        }

        try {
            $query = "
                SELECT * FROM reklamy 
                WHERE aktivni = 1 
                AND (datum_zacatku IS NULL OR datum_zacatku <= NOW())
                AND (datum_konce IS NULL OR datum_konce >= NOW())
                ORDER BY poradi ASC
            ";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Chyba při načítání aktivních reklam: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Získá výchozí reklamu
     * Vrací null, pokud tabulka neexistuje nebo není žádná výchozí reklama
     */
    public function getDefaultAd()
    {
        if (!$this->checkTableExists()) {
            return null;
        }

        try {
            $query = "
                SELECT * FROM reklamy 
                WHERE vychozi = 1 
                AND aktivni = 1
                LIMIT 1
            ";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result : null;
        } catch (\PDOException $e) {
            error_log("Chyba při načítání výchozí reklamy: " . $e->getMessage());
            return null;
        }
    }
}
