<?php

namespace App\Models;

class Article
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Získání všech článků
    public function getAll()
    {
        $query = "
            SELECT clanky.id, clanky.nazev, clanky.datum, clanky.viditelnost, users.name AS autor_jmeno, users.surname AS autor_prijmeni
            FROM clanky
            LEFT JOIN users ON clanky.user_id = users.id
            ORDER BY clanky.datum DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllAdmin($limit = null)
    {
        $query = "SELECT * FROM clanky ORDER BY datum DESC";
        if ($limit) {
            $query .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($query);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Získání jednoho článku podle ID
    public function getById($id)
    {
        $query = "SELECT clanky.*, 
                        users.name AS autor_jmeno, 
                        users.surname AS autor_prijmeni, 
                        clanky_kategorie.id_kategorie
                    FROM clanky
                    LEFT JOIN users ON clanky.user_id = users.id
                    LEFT JOIN clanky_kategorie ON clanky.id = clanky_kategorie.id_clanku
                    WHERE clanky.id = :id  
                    AND clanky.viditelnost = 1 
                    AND clanky.datum <= NOW()";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByIdUser($userId)
    {
        $query = "SELECT DISTINCT c.id, c.nazev, c.nahled_foto, c.datum, c.url,
                   GROUP_CONCAT(DISTINCT k.nazev_kategorie) as kategorie_nazvy,
                   GROUP_CONCAT(DISTINCT k.url) as kategorie_urls
            FROM clanky c
            LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
            LEFT JOIN kategorie k ON ck.id_kategorie = k.id
            WHERE c.user_id = :userId
            AND c.viditelnost = 1
            GROUP BY c.id, c.nazev, c.nahled_foto, c.datum, c.url
            ORDER BY c.datum DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
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

    public function getByUser($userId, $limit = null)
    {
        $query = "SELECT clanky.*, 
                        users.name AS autor_jmeno, 
                        users.surname AS autor_prijmeni,
                        GROUP_CONCAT(DISTINCT kategorie.nazev_kategorie) as kategorie_nazvy,
                        GROUP_CONCAT(DISTINCT kategorie.url) as kategorie_urls,
                        COALESCE(views.pocet, 0) as views_count
                    FROM clanky
                    LEFT JOIN users ON clanky.user_id = users.id
                    LEFT JOIN clanky_kategorie ON clanky.id = clanky_kategorie.id_clanku
                    LEFT JOIN kategorie ON clanky_kategorie.id_kategorie = kategorie.id
                    LEFT JOIN views_clanku views ON clanky.id = views.id_clanku
                    WHERE clanky.user_id = :userId
                        AND clanky.viditelnost = 1 
                        AND clanky.datum <= NOW()
                    GROUP BY clanky.id
                    ORDER BY views_count DESC, clanky.datum DESC";

        if ($limit !== null) {
            $query .= " LIMIT :limit";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Převedení řetězců kategorií na pole
        foreach ($articles as &$article) {
            $nazvy = explode(',', $article['kategorie_nazvy'] ?? '');
            $urls = explode(',', $article['kategorie_urls'] ?? '');
            
            $article['category'] = array_map(function($nazev, $url) {
                return [
                    'nazev_kategorie' => $nazev,
                    'url_kategorie' => $url
                ];
            }, $nazvy, $urls);

            unset($article['kategorie_nazvy']);
            unset($article['kategorie_urls']);
        }

        return $articles;
    }

    // Získání kategorií článku
    public function getCategories($articleId)
    {
        $query = "SELECT kategorie.nazev_kategorie FROM clanky_kategorie 
                  JOIN kategorie ON clanky_kategorie.id_kategorie = kategorie.id 
                  WHERE clanky_kategorie.id_clanku = :articleId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Přidání kategorie k článku
    public function addCategory($articleId, $categoryId)
    {
        $query = "INSERT INTO clanky_kategorie (id_clanku, id_kategorie) VALUES (:articleId, :categoryId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Aktualizace viditelnosti článku
    public function updateVisibility($articleId, $visibility)
    {
        $query = "UPDATE clanky SET viditelnost = :visibility WHERE id = :articleId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':visibility', $visibility, \PDO::PARAM_INT);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Získání počtu zobrazení článku
    public function getViews($articleId)
    {
        $query = "SELECT pocet FROM views_clanku WHERE id_clanku = :articleId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)['pocet'] ?? 0;
    }

    // Zvýšení počtu zobrazení článku
    public function incrementViews($articleId)
    {
        $query = "INSERT INTO views_clanku (id_clanku, pocet, datum) 
                  VALUES (:articleId, 1, CURDATE()) 
                  ON DUPLICATE KEY UPDATE pocet = pocet + 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Odstranění článku
    public function delete($articleId)
    {
        $query = "DELETE FROM clanky WHERE id = :articleId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Aktualizace článku
    public function update($data)
    {
        $query = "UPDATE clanky SET 
                      nazev = :nazev, 
                      obsah = :obsah, 
                      datum = :datum, 
                      viditelnost = :viditelnost, 
                      nahled_foto = :nahled_foto, 
                      user_id = :user_id, 
                      autor = :autor, 
                      url = :url 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':nazev', $data['nazev']);
        $stmt->bindValue(':obsah', $data['obsah']);
        $stmt->bindValue(':datum', $data['datum']);
        $stmt->bindValue(':viditelnost', $data['viditelnost'], \PDO::PARAM_INT);
        $stmt->bindValue(':nahled_foto', $data['nahled_foto']);
        $stmt->bindValue(':user_id', $data['user_id'], \PDO::PARAM_INT);
        $stmt->bindValue(':autor', $data['autor'], \PDO::PARAM_INT);
        $stmt->bindValue(':url', $data['url']);
        $stmt->bindValue(':id', $data['id'], \PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Vytvoření nového článku
    public function create($data)
    {
        $query = "INSERT INTO clanky (nazev, obsah, viditelnost, nahled_foto, user_id, autor, url, datum)
                    VALUES (:nazev, :obsah, :viditelnost, :nahled_foto, :user_id, :autor, :url, :datum)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':nazev', $data['nazev'], \PDO::PARAM_STR);
        $stmt->bindValue(':obsah', $data['obsah'], \PDO::PARAM_STR);
        $stmt->bindValue(':viditelnost', $data['viditelnost'], \PDO::PARAM_INT);
        $stmt->bindValue(':nahled_foto', $data['nahled_foto'], \PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $data['user_id'], \PDO::PARAM_INT);
        $stmt->bindValue(':autor', $data['autor'], \PDO::PARAM_INT);
        $stmt->bindValue(':url', $data['url'], \PDO::PARAM_STR);
        $stmt->bindValue(':datum', $data['datum'], \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function store($postData)
    {
        $title = $postData['title'];
        $category = $postData['category'];
        $publishDate = $postData['publish_date'];
        $isPublic = isset($postData['is_public']) ? 1 : 0;
        $showAuthor = isset($postData['show_author']) ? 1 : 0;
        $content = $postData['content'];
        $thumbnail = $_FILES['thumbnail']['name'] ?? null;

        // Nahrání souboru
        if ($thumbnail) {
            $targetDir = '../../uploads/thumbnails/';
            $targetFile = $targetDir . basename($thumbnail);
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        }

        // Vložení dat do databáze
        $query = "INSERT INTO clanky (nazev, id_kategorie, datum, viditelnost, nahled_foto, obsah, autor)
                    VALUES (:title, :category, :publishDate, :isPublic, :thumbnail, :content, :showAuthor)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':publishDate', $publishDate);
        $stmt->bindParam(':isPublic', $isPublic, \PDO::PARAM_INT);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':showAuthor', $showAuthor, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: /admin/articles');
            exit();
        } else {
            echo "Došlo k chybě při ukládání článku.";
        }
    }

    public function getNewestArticle()
    {
        $stmt = $this->db->prepare("
            SELECT c.*, GROUP_CONCAT(k.nazev_kategorie SEPARATOR ', ') AS kategorie
            FROM clanky c
            LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
            LEFT JOIN kategorie k ON ck.id_kategorie = k.id
            WHERE c.viditelnost = 1 AND c.datum <= NOW()
            GROUP BY c.id
            ORDER BY c.datum DESC LIMIT 1
        ");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Další 3 články s kategoriemi
    public function getLatestArticles($limit, $offset)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, GROUP_CONCAT(k.nazev_kategorie SEPARATOR ', ') AS kategorie
            FROM clanky c
            LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
            LEFT JOIN kategorie k ON ck.id_kategorie = k.id
            WHERE c.viditelnost = 1 AND c.datum <= NOW()
            GROUP BY c.id
            ORDER BY c.datum DESC
            LIMIT :offset, :limit
        ");
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCategoriesWithArticlesSorted()
    {
        $stmt = $this->db->query("
            SELECT k.id, k.nazev_kategorie, k.url, MAX(c.datum) as posledni_clanek
            FROM kategorie k
            LEFT JOIN clanky_kategorie ck ON k.id = ck.id_kategorie
            LEFT JOIN clanky c ON ck.id_clanku = c.id
            WHERE c.viditelnost = 1 AND c.datum <= NOW()
            GROUP BY k.id
            ORDER BY posledni_clanek DESC
        ");

        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($categories as &$category) {
            $articlesStmt = $this->db->prepare("
                SELECT c.*
                FROM clanky c
                LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                WHERE ck.id_kategorie = :id AND c.viditelnost = 1 AND c.datum <= NOW()
                ORDER BY c.datum DESC
                LIMIT 3
            ");
            $articlesStmt->execute(['id' => $category['id']]);
            $category['articles'] = $articlesStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        }

        return $categories;
    }
    

    public function getByUrl($url)
    {
        $query = "SELECT c.*, GROUP_CONCAT(k.nazev_kategorie) as kategorie_nazvy, 
                         GROUP_CONCAT(k.id) as kategorie_ids,
                         GROUP_CONCAT(k.url) as kategorie_urls
                  FROM clanky c 
                  LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                  LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                  WHERE c.url = :url 
                  AND c.viditelnost = 1 
                  AND c.datum <= NOW()
                  GROUP BY c.id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
        $stmt->execute();
        
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($article) {
            // Převedení řetězců kategorií na pole
            $article['kategorie_nazvy'] = $article['kategorie_nazvy'] ? explode(',', $article['kategorie_nazvy']) : [];
            $article['kategorie_ids'] = $article['kategorie_ids'] ? explode(',', $article['kategorie_ids']) : [];
            $article['kategorie_urls'] = $article['kategorie_urls'] ? explode(',', $article['kategorie_urls']) : [];
            
            // Vytvoření pole kategorií pro snadnější použití ve view
            $article['kategorie'] = [];
            for ($i = 0; $i < count($article['kategorie_ids']); $i++) {
                $article['kategorie'][] = [
                    'id' => $article['kategorie_ids'][$i],
                    'nazev_kategorie' => $article['kategorie_nazvy'][$i],
                    'url' => $article['kategorie_urls'][$i]
                ];
            }
            
            // Odstranění pomocných polí
            unset($article['kategorie_nazvy']);
            unset($article['kategorie_ids']);
            unset($article['kategorie_urls']);
        }
        
        return $article;
    }

    public function getAllWithSortingAndFiltering($sortBy, $order, $filter)
    {
        $validSortColumns = ['id', 'nazev', 'datum', 'viditelnost', 'user_id', 'pocet_zobrazeni'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'datum';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $query = "SELECT clanky.*, 
                    users.name AS autor_jmeno, 
                    users.surname AS autor_prijmeni, 
                    SUM(views_clanku.pocet) AS pocet_zobrazeni 
            FROM clanky
            LEFT JOIN users ON clanky.user_id = users.id
            LEFT JOIN views_clanku ON clanky.id = views_clanku.id_clanku
            WHERE clanky.nazev LIKE :filter
            GROUP BY clanky.id, users.name, users.surname
            ORDER BY $sortBy $order";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':filter', '%' . $filter . '%', \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM articles WHERE url = :slug";
        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function saveArticleAudio($articleId, $audioPath)
    {
        $query = "UPDATE clanky SET audio = :audio_path WHERE id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':audio_path' => $audioPath,
            ':article_id' => $articleId
        ]);
    }

    public function getRelatedArticles($articleId, $limit = 3)
    {
        // Nejdřív získáme všechny kategorie aktuálního článku
        $query = "
            SELECT DISTINCT c.id, c.nazev, c.nahled_foto, c.datum, c.url,
                   GROUP_CONCAT(k.nazev_kategorie) as kategorie_nazvy,
                   GROUP_CONCAT(k.url) as kategorie_urls
            FROM clanky c
            INNER JOIN clanky_kategorie ck1 ON c.id = ck1.id_clanku
            LEFT JOIN clanky_kategorie ck2 ON c.id = ck2.id_clanku
            LEFT JOIN kategorie k ON ck2.id_kategorie = k.id
            WHERE ck1.id_kategorie IN (
                SELECT id_kategorie 
                FROM clanky_kategorie 
                WHERE id_clanku = :articleId
            )
            AND c.id != :articleId
            AND c.viditelnost = 1 
            AND c.datum <= NOW()
            GROUP BY c.id
            ORDER BY c.datum DESC
            LIMIT 10";  // Nejdřív vybereme 10 nejnovějších

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Zpracování kategorií pro každý článek
        foreach ($articles as &$article) {
            $article['kategorie'] = [];
            if ($article['kategorie_nazvy'] && $article['kategorie_urls']) {
                $nazvy = explode(',', $article['kategorie_nazvy']);
                $urls = explode(',', $article['kategorie_urls']);
                
                for ($i = 0; $i < count($nazvy); $i++) {
                    $article['kategorie'][] = [
                        'nazev_kategorie' => $nazvy[$i],
                        'url' => $urls[$i]
                    ];
                }
            }
            
            // Odstraníme pomocná pole
            unset($article['kategorie_nazvy']);
            unset($article['kategorie_urls']);
        }

        // Náhodně vybereme 3 články z těch 10
        shuffle($articles);
        return array_slice($articles, 0, $limit);
    }
}
