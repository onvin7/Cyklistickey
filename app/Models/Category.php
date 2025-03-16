<?php

namespace App\Models;

class Category
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll($limit = null)
    {
        $query = "SELECT kategorie.id, kategorie.nazev_kategorie, kategorie.url, COUNT(clanky_kategorie.id_clanku) AS pocet_clanku
            FROM kategorie
            LEFT JOIN clanky_kategorie ON clanky_kategorie.id_kategorie = kategorie.id
            LEFT JOIN clanky ON clanky.id = clanky_kategorie.id_clanku AND clanky.viditelnost = 1
            GROUP BY kategorie.id, kategorie.nazev_kategorie, kategorie.url
            ORDER BY pocet_clanku DESC";

        if ($limit !== null) {
            $query .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($query);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getById($id)
    {
        $query = "SELECT * FROM kategorie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO kategorie (nazev_kategorie, url) VALUES (:nazev_kategorie, :url)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nazev_kategorie', $data['nazev_kategorie'], \PDO::PARAM_STR);
        $stmt->bindValue(':url', $data['url'], \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function update($data)
    {
        $query = "UPDATE kategorie SET nazev_kategorie = :nazev_kategorie, url = :url WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nazev_kategorie', $data['nazev_kategorie'], \PDO::PARAM_STR);
        $stmt->bindValue(':url', $data['url'], \PDO::PARAM_STR);
        $stmt->bindValue(':id', $data['id'], \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        try {
            // Zkontrolovat závislé záznamy
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM clanky_kategorie WHERE id_kategorie = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Opravený název parametru
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                echo "<script>alert('Kategorie nemůže být smazána, protože obsahuje závislé záznamy.');</script>";
                return false;
            }

            // Smazání samotné kategorie
            $stmt = $this->db->prepare("DELETE FROM kategorie WHERE id = :id"); // Opravený parametr
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Přidáno $
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            echo "<script>alert('Chyba při mazání kategorie: " . addslashes($e->getMessage()) . "');</script>";
            return false;
        }
    }



    public function getArticlesByCategory($categoryId)
    {
        $query = "SELECT DISTINCT c.id, c.nazev, c.nahled_foto, c.datum, c.url, c.obsah,
                   GROUP_CONCAT(DISTINCT k.nazev_kategorie) as kategorie_nazvy,
                   GROUP_CONCAT(DISTINCT k.url) as kategorie_urls
            FROM clanky c
            INNER JOIN clanky_kategorie ck1 ON c.id = ck1.id_clanku
            LEFT JOIN clanky_kategorie ck2 ON c.id = ck2.id_clanku
            LEFT JOIN kategorie k ON ck2.id_kategorie = k.id
            WHERE ck1.id_kategorie = :categoryId 
            AND c.viditelnost = 1
            GROUP BY c.id, c.nazev, c.nahled_foto, c.datum, c.url
            ORDER BY c.datum DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':categoryId', $categoryId, \PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Pro debugování
        error_log("Articles before processing: " . print_r($articles, true));

        // Zpracování kategorií pro každý článek
        foreach ($articles as &$article) {
            $article['kategorie'] = [];
            if (!empty($article['kategorie_nazvy']) && !empty($article['kategorie_urls'])) {
                $nazvy = explode(',', $article['kategorie_nazvy']);
                $urls = explode(',', $article['kategorie_urls']);
                
                for ($i = 0; $i < count($nazvy); $i++) {
                    if (!empty($nazvy[$i]) && !empty($urls[$i])) {
                        $article['kategorie'][] = [
                            'nazev_kategorie' => trim($nazvy[$i]),
                            'url' => trim($urls[$i])
                        ];
                    }
                }
            }
            
            // Pro debugování
            error_log("Article after processing: " . print_r($article, true));
            
            // Odstraníme pomocná pole
            unset($article['kategorie_nazvy']);
            unset($article['kategorie_urls']);
        }

        return $articles;
    }


    public function getByUrl($url)
    {
        $query = "SELECT * FROM kategorie WHERE url = :url";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllWithSortingAndFiltering($sortBy = 'id', $order = 'ASC', $filter = '')
    {
        $allowedColumns = ['id', 'nazev_kategorie', 'url']; // Používáme názvy podle tabulky
        $allowedOrder = ['ASC', 'DESC'];                 // Povolené směry řazení

        // Ověření sloupce a směru řazení
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'id';
        }
        if (!in_array($order, $allowedOrder)) {
            $order = 'ASC';
        }

        // SQL dotaz pro filtrování a řazení
        $query = "SELECT * FROM kategorie
        WHERE nazev_kategorie LIKE :filter
        ORDER BY $sortBy $order";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':filter', '%' . $filter . '%', \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
