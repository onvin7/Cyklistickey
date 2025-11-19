<?php

namespace App\Models;

use PDO;

class Event
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Získá všechny závody seřazené podle roku (nejnovější první)
     * 
     * @return array
     */
    public function getAllByYear()
    {
        $stmt = $this->db->query("
            SELECT * 
            FROM events 
            ORDER BY year DESC, date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá závody podle roku
     * 
     * @param int $year
     * @return array
     */
    public function getByYear($year)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM events 
            WHERE year = ? 
            ORDER BY date ASC
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá jednotlivé závody (ne seriál) podle roku
     * 
     * @param int $year
     * @return array
     */
    public function getIndividualByYear($year)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM events 
            WHERE year = ? AND type = 'individual'
            ORDER BY date ASC
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá závody v seriálu podle roku
     * 
     * @param int $year
     * @return array
     */
    public function getSeriesByYear($year)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, es.title as series_title, es.name as series_name
            FROM events e
            JOIN event_series es ON e.series_id = es.id
            WHERE e.year = ? AND e.type = 'series'
            ORDER BY e.series_order ASC, e.date ASC
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá informace o seriálu podle roku
     * 
     * @param int $year
     * @return array|false
     */
    public function getSeriesInfoByYear($year)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM event_series 
            WHERE year = ?
            LIMIT 1
        ");
        $stmt->execute([$year]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Získá závod podle roku a názvu
     * 
     * @param int $year
     * @param string $name
     * @return array|false
     */
    public function getByYearAndName($year, $name)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM events 
            WHERE year = ? AND name = ?
        ");
        $stmt->execute([$year, $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Získá závod podle ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM events 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vytvoří nový závod
     * 
     * @param array $data
     * @return int
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO events (
                name, title, year, type, series_id, series_order,
                date, location, address, description, content,
                registration_url, is_active, background_image, created_by
            ) VALUES (
                :name, :title, :year, :type, :series_id, :series_order,
                :date, :location, :address, :description, :content,
                :registration_url, :is_active, :background_image, :created_by
            )
        ");
        
        $stmt->execute([
            ':name' => $data['name'],
            ':title' => $data['title'],
            ':year' => $data['year'],
            ':type' => $data['type'] ?? 'individual',
            ':series_id' => $data['series_id'] ?? null,
            ':series_order' => $data['series_order'] ?? null,
            ':date' => $data['date'],
            ':location' => $data['location'],
            ':address' => $data['address'] ?? null,
            ':description' => $data['description'] ?? null,
            ':content' => $data['content'] ?? null,
            ':registration_url' => $data['registration_url'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':background_image' => $data['background_image'] ?? null,
            ':created_by' => $data['created_by'] ?? null,
        ]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Aktualizuje závod
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "`$key` = :$key";
            $values[":$key"] = $value;
        }
        
        $stmt = $this->db->prepare("
            UPDATE events 
            SET " . implode(', ', $fields) . "
            WHERE id = :id
        ");
        
        return $stmt->execute($values);
    }

    /**
     * Smaže závod
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Vytvoří nový seriál závodů
     * 
     * @param array $data
     * @return int
     */
    public function createSeries($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO event_series (name, title, year, description)
            VALUES (:name, :title, :year, :description)
        ");
        
        $stmt->execute([
            ':name' => $data['name'],
            ':title' => $data['title'],
            ':year' => $data['year'],
            ':description' => $data['description'] ?? null,
        ]);
        
        return $this->db->lastInsertId();
    }
}



