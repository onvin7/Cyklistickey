<?php

namespace App\Controllers\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Helpers\TextHelper;
use App\Helpers\LogHelper;

use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromgif;

class ArticleAdminController
{
    private $model;
    private $articleModel;

    public function __construct($db)
    {
        $this->model = $db;
        $this->articleModel = new Article($db);
    }

    public function index()
    {
        $sortBy = $_GET['sort_by'] ?? 'datum'; // Výchozí řazení podle data
        $order = $_GET['order'] ?? 'DESC';    // Výchozí sestupné řazení
        $filter = $_GET['filter'] ?? '';      // Výchozí bez filtru

        $articles = $this->articleModel->getAllWithSortingAndFiltering($sortBy, $order, $filter);

        $adminTitle = "Články | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/articles/index.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Formulář pro vytvoření článku
    public function create()
    {
        $categoryModel = new Category($this->model); // Použití modelu kategorie
        $categories = $categoryModel->getAll(); // Načtení kategorií
        
        $adminTitle = "Vytvořit článek | Admin Panel - Cyklistickey magazín";
        
        $view = '../app/Views/Admin/articles/create.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Ukládání nového článku
    public function store($postData)
    {
        if (empty($postData['nazev'])) {
            echo "Název článku je povinný.";
            return;
        }
        if (empty($postData['content'])) {
            echo "Obsah článku je povinný.";
            return;
        }

        // Zpracování nahrání souboru
        $nahledFoto = "default.jpg";
        $targetDir = __DIR__ . '/../../../web/uploads/thumbnails/';

        if (isset($_FILES['nahled_foto']) && $_FILES['nahled_foto']['error'] === UPLOAD_ERR_OK) {
            // Kontrola typu souboru - povolujeme pouze obrázky
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['nahled_foto']['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                echo "<div class='alert alert-danger'>Chyba: Nepodporovaný formát souboru. Povolené formáty jsou JPEG, PNG a GIF.</div>";
                return;
            }
            
            $uniqueName = basename($_FILES['nahled_foto']['name']);

            $largeDir = $targetDir . 'velke/';
            $smallDir = $targetDir . 'male/';
            $largeFilePath = $largeDir . $uniqueName;
            $smallFilePath = $smallDir . $uniqueName;

            // Vytvoření adresářů, pokud neexistují
            if (!is_dir($largeDir)) {
                mkdir($largeDir, 0777, true);
            }
            if (!is_dir($smallDir)) {
                mkdir($smallDir, 0777, true);
            }

            // Přesun a zpracování originálního souboru
            if (move_uploaded_file($_FILES['nahled_foto']['tmp_name'], $largeFilePath)) {
                @LogHelper::admin('Article image uploaded (create)', 'File: ' . basename($largeFilePath) . ', Size: ' . $_FILES['nahled_foto']['size'] . ' bytes');
                // Pro velké fotky použijeme optimalizovanou velikost
                $this->createThumbnail($largeFilePath, $largeFilePath, 1600, 1067, 90, true);
                
                // Vytvoření malé verze pro náhledy
                $this->createThumbnail($largeFilePath, $smallFilePath, 600, 400, 85, false);

                $nahledFoto = $uniqueName;
                echo "<p>Fotka byla úspěšně nahrána:</p>";
                echo "<img src='/uploads/thumbnails/male/$nahledFoto' alt='Náhled' style='max-width: 150px;'>";
            } else {
                echo "❌ Chyba při nahrávání souboru!";
            }
        }

        // Zpracování nahrání zvukového souboru
        $audioFile = null;
        $audioDir = __DIR__ . '/../../../web/uploads/audio/';
        
        // Zajistíme, že adresář pro audio existuje
        if (!is_dir($audioDir)) {
            mkdir($audioDir, 0777, true);
        }

        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
            // Kontrola typu souboru - povolujeme pouze MP3
            $allowedAudioTypes = ['audio/mpeg', 'audio/mp3'];
            $audioFileType = $_FILES['audio_file']['type'];
            
            if (!in_array($audioFileType, $allowedAudioTypes)) {
                echo "<div class='alert alert-danger'>Chyba: Nepodporovaný formát zvukového souboru. Povolený formát je MP3.</div>";
                return;
            }
            
            // ID získáme až po vytvoření článku, proto zatím uložíme do dočasného souboru
            $tempAudioName = uniqid() . '.mp3';
            $tempAudioPath = $audioDir . $tempAudioName;
            
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $tempAudioPath)) {
                @LogHelper::admin('Article audio uploaded', 'File: ' . $tempAudioPath . ', Size: ' . $_FILES['audio_file']['size'] . ' bytes');
                $audioFile = $tempAudioName;
                echo "<p>Zvukový soubor byl úspěšně nahrán.</p>";
            } else {
                echo "<div class='alert alert-danger'>❌ Chyba při nahrávání zvukového souboru!</div>";
            }
        }

        $slug = TextHelper::generateFriendlyUrl($postData['nazev']);

        $data = [
            'nazev' => $postData['nazev'],
            'obsah' => $this->fixImagePaths($postData['content']),
            'viditelnost' => isset($postData['viditelnost']) ? 1 : 0,
            'nahled_foto' => $nahledFoto,
            'user_id' => $_SESSION['user_id'],
            'url' => $slug,
            'datum' => date('Y-m-d H:i:s')
        ];

        $articleId = $this->articleModel->create($data);

        if ($articleId) {
            // Zpracování kategorií článku
            if (isset($postData['kategorie']) && is_array($postData['kategorie'])) {
                $this->articleModel->addCategories($articleId, $postData['kategorie']);
            }
            
            // Pokud byl nahrán zvukový soubor, přejmenujeme ho podle ID článku
            if ($audioFile) {
                $finalAudioPath = $audioDir . $articleId . '.mp3';
                rename($audioDir . $audioFile, $finalAudioPath);
            }
            
            LogHelper::admin('Article created', 'ID: ' . $articleId . ', Title: ' . ($postData['nazev'] ?? 'N/A'));
            header("Location: /admin/articles");
            exit;
        } else {
            LogHelper::admin('Article create failed', 'Title: ' . ($postData['nazev'] ?? 'N/A'));
            echo "Chyba při ukládání článku.";
        }
    }

    public function edit($id)
    {
        $article = $this->articleModel->getById($id); // Načtení článku podle ID
        if (!$article) {
            echo "Článek nenalezen.";
            return;
        }

        $categoryModel = new Category($this->model); // Použití modelu kategorie
        $categories = $categoryModel->getAll(); // Načtení všech kategorií
        
        // Načtení kategorií článku
        $article_categories = $this->articleModel->getArticleCategories($id);

        $adminTitle = "Upravit článek: " . $article['nazev'] . " | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/articles/edit.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Aktualizace článku
    public function update($id, $postData)
    {
        if (empty($postData['nazev'])) {
            echo "Název článku je povinný.";
            return;
        }
        if (empty($postData['content'])) {
            echo "Obsah článku je povinný.";
            return;
        }

        $targetDir = __DIR__ . '/../../../web/uploads/thumbnails/';
        $nahledFoto = $postData['current_foto']; // Použijeme aktuální foto, pokud není nové

        // Kontrola a vytvoření složky, pokud neexistuje
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (isset($_FILES['nahled_foto']) && $_FILES['nahled_foto']['error'] === UPLOAD_ERR_OK) {
            // Kontrola typu souboru - povolujeme pouze obrázky
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['nahled_foto']['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                echo "<div class='alert alert-danger'>Chyba: Nepodporovaný formát souboru. Povolené formáty jsou JPEG, PNG a GIF.</div>";
                return;
            }
            
            $noveFoto = basename($_FILES['nahled_foto']['name']);
            
            $largeDir = $targetDir . 'velke/';
            $smallDir = $targetDir . 'male/';
            $largeFilePath = $largeDir . $noveFoto;
            $smallFilePath = $smallDir . $noveFoto;

            // Vytvoření adresářů, pokud neexistují
            if (!is_dir($largeDir)) {
                mkdir($largeDir, 0777, true);
            }
            if (!is_dir($smallDir)) {
                mkdir($smallDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['nahled_foto']['tmp_name'], $largeFilePath)) {
                @LogHelper::admin('Article image uploaded (update)', 'Article ID: ' . $id . ', File: ' . basename($largeFilePath) . ', Size: ' . $_FILES['nahled_foto']['size'] . ' bytes');
                // Pro velké fotky použijeme optimalizovanou velikost
                $this->createThumbnail($largeFilePath, $largeFilePath, 1600, 1067, 90, true);
                
                // Vytvoření malé verze pro náhledy
                $this->createThumbnail($largeFilePath, $smallFilePath, 600, 400, 85, false);

                $nahledFoto = $noveFoto;
                echo "<p>Nová fotka byla úspěšně nahrána</p>";
            }
        }

        // Zpracování nahrání zvukového souboru
        $audioDir = __DIR__ . '/../../../web/uploads/audio/';
        
        // Zajistíme, že adresář pro audio existuje
        if (!is_dir($audioDir)) {
            mkdir($audioDir, 0777, true);
        }

        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
            // Kontrola typu souboru - povolujeme pouze MP3
            $allowedAudioTypes = ['audio/mpeg', 'audio/mp3'];
            $audioFileType = $_FILES['audio_file']['type'];
            
            if (!in_array($audioFileType, $allowedAudioTypes)) {
                echo "<div class='alert alert-danger'>Chyba: Nepodporovaný formát zvukového souboru. Povolený formát je MP3.</div>";
                return;
            }
            
            $audioPath = $audioDir . $id . '.mp3';
            
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $audioPath)) {
                @LogHelper::admin('Article audio uploaded (update)', 'Article ID: ' . $id . ', File: ' . basename($audioPath) . ', Size: ' . $_FILES['audio_file']['size'] . ' bytes');
                echo "<p>Zvukový soubor byl úspěšně aktualizován.</p>";
            } else {
                echo "<div class='alert alert-danger'>❌ Chyba při nahrávání zvukového souboru!</div>";
            }
        }

        // Nejprve získáme původní data článku
        $originalArticle = $this->articleModel->getById($id);
        
        // Použijeme původní datum, pokud není explicitně zadáno nové
        $datum = isset($postData['datum_publikace']) && !empty($postData['datum_publikace']) 
               ? date('Y-m-d H:i:s', strtotime($postData['datum_publikace'])) 
               : $originalArticle['datum'];
        
        $slug = TextHelper::generateFriendlyUrl($postData['nazev']);

        $data = [
            'id' => $id,
            'nazev' => $postData['nazev'],
            'obsah' => $this->fixImagePaths($postData['content']),
            'viditelnost' => isset($postData['viditelnost']) ? 1 : 0,
            'nahled_foto' => $nahledFoto,
            'user_id' => $_SESSION['user_id'], 
            'url' => $slug,
            'datum' => $datum
        ];

        $result = $this->articleModel->update($data);

        if ($result) {
            // Zpracování kategorií článku
            if (isset($postData['kategorie']) && is_array($postData['kategorie'])) {
                $this->articleModel->addCategories($id, $postData['kategorie']);
            } else {
                // Pokud kategorie nebyla vybrána, odebereme všechny kategorie článku
                $this->articleModel->addCategories($id, []);
            }
            
            LogHelper::admin('Article updated', 'ID: ' . $id . ', Title: ' . ($postData['nazev'] ?? 'N/A'));
            header("Location: /admin/articles");
            exit;
        } else {
            LogHelper::admin('Article update failed', 'ID: ' . $id);
            echo "Chyba při aktualizaci článku.";
        }
    }

    public function delete($id)
    {
        // ✅ **Kontrola existence článku v databázi**
        if (!$this->articleModel->getById($id)) {
            die("❌ Chyba: Článek nenalezen.");
        }

        // ✅ **Smazání článku z databáze**
        if ($this->articleModel->delete($id)) {
            LogHelper::admin('Article deleted', 'ID: ' . $id);
            header("Location: /admin/articles"); // Přesměrování na seznam článků
            exit();
        } else {
            LogHelper::admin('Article delete failed', 'ID: ' . $id);
            die("❌ Chyba: Článek se nepodařilo smazat.");
        }
    }

    public function preview($id)
    {
        // Načtení článku - použijeme metodu, která načte i skryté články s kategoriemi
        $query = "SELECT c.*, 
                        u.name AS autor_jmeno, 
                        u.surname AS autor_prijmeni,
                        GROUP_CONCAT(k.nazev_kategorie) as kategorie_nazvy,
                        GROUP_CONCAT(k.id) as kategorie_ids,
                        GROUP_CONCAT(k.url) as kategorie_urls
                    FROM clanky c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN clanky_kategorie ck ON c.id = ck.id_clanku
                    LEFT JOIN kategorie k ON ck.id_kategorie = k.id
                    WHERE c.id = :id
                    GROUP BY c.id";
        
        $stmt = $this->model->prepare($query);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            echo "Článek nenalezen.";
            return;
        }

        // Formátování kategorií do pole objektů (stejně jako getByUrl)
        $article['kategorie'] = [];
        if (!empty($article['kategorie_nazvy'])) {
            $nazvy = $article['kategorie_nazvy'] ? explode(',', $article['kategorie_nazvy']) : [];
            $ids = $article['kategorie_ids'] ? explode(',', $article['kategorie_ids']) : [];
            $urls = $article['kategorie_urls'] ? explode(',', $article['kategorie_urls']) : [];
            
            for ($i = 0; $i < count($nazvy); $i++) {
                if (isset($nazvy[$i]) && isset($urls[$i])) {
                    $article['kategorie'][] = [
                        'nazev_kategorie' => trim($nazvy[$i]),
                        'id' => isset($ids[$i]) ? trim($ids[$i]) : null,
                        'url' => trim($urls[$i])
                    ];
                }
            }
        }

        // Načtení souvisejících článků
        $relatedArticles = $this->articleModel->getRelatedArticles($id, 3);
        if (!is_array($relatedArticles)) {
            $relatedArticles = [];
        }

        // Formátování souvisejících článků
        foreach ($relatedArticles as &$related) {
            if (!empty($related['kategorie_nazvy'])) {
                $nazvy = explode(',', $related['kategorie_nazvy']);
                $urls = explode(',', $related['kategorie_urls']);
                $related['kategorie'] = [];
                for ($i = 0; $i < count($nazvy); $i++) {
                    $related['kategorie'][] = [
                        'nazev_kategorie' => trim($nazvy[$i]),
                        'url' => trim($urls[$i])
                    ];
                }
            }
        }

        // Kontrola audio souboru
        $audioFilePath = __DIR__ . '/../../../web/uploads/audio/' . $article['id'] . '.mp3';
        $fileExists = @file_exists($audioFilePath);
        $audioUrl = $fileExists ? '/uploads/audio/' . $article['id'] . '.mp3' : null;

        // Přidání trackingu k odkazům
        if (isset($article['obsah'])) {
            $article['obsah'] = \App\Helpers\LinkTrackingHelper::addTrackingToLinks($article['obsah'], $article['id']);
        }

        // Cesta k empty_clanek.php
        $emptyArticlePath = '../app/Views/Web/templates/empty_clanek.php';

        // Načtení autora článku (stejně jako na veřejném webu)
        $author = null;
        if (isset($article['user_id'])) {
            $userModel = new \App\Models\User($this->model);
            $author = $userModel->getById($article['user_id']);
        }

        // Použijeme admin layout s veřejnými CSS styly pro náhled
        $adminTitle = "Náhled článku: " . $article['nazev'] . " | Admin Panel - Cyklistickey magazín";
        $css = ["main-page", "clanek", "autor_clanku"];
        $useFullWidth = true; // Pro náhled použijeme plnou šířku
        
        // Nastavíme proměnné pro view
        $view = '../app/Views/Admin/articles/preview.php';
        include '../app/Views/Admin/layout/base.php';
    }

    private function createThumbnail($sourcePath, $targetPath, $maxWidth, $maxHeight, $quality = 85, $highQuality = false) {
        // Načtení EXIF dat pro zjištění orientace
        $exif = @exif_read_data($sourcePath);
        
        // Načtení původního obrázku
        list($originalWidth, $originalHeight, $imageType) = getimagesize($sourcePath);
        
        // Načtení zdrojového obrázku
        $sourceImage = null;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new \Exception('Nepodporovaný formát obrázku: ' . $imageType);
        }
        
        // Oprava orientace podle EXIF dat
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $sourceImage = imagerotate($sourceImage, 180, 0);
                    break;
                case 6:
                    $sourceImage = imagerotate($sourceImage, -90, 0);
                    list($originalWidth, $originalHeight) = array($originalHeight, $originalWidth);
                    break;
                case 8:
                    $sourceImage = imagerotate($sourceImage, 90, 0);
                    list($originalWidth, $originalHeight) = array($originalHeight, $originalWidth);
                    break;
            }
        }

        // Výpočet cílového poměru stran (3:2)
        $targetRatio = 3 / 2;
        $sourceRatio = $originalWidth / $originalHeight;

        // Určení rozměrů pro oříznutí
        if ($sourceRatio < $targetRatio) {
            // Obrázek je vyšší než potřebujeme (např. 2:3)
            // Ořízněte ho na výšku tak, aby vznikl poměr 3:2
            $cropHeight = round($originalWidth / $targetRatio);
            $cropWidth = $originalWidth;
            $cropX = 0;
            $cropY = round(($originalHeight - $cropHeight) / 2); // Ořez ze středu
        } else {
            // Obrázek je širší nebo má správný poměr
            $cropWidth = round($originalHeight * $targetRatio);
            $cropHeight = $originalHeight;
            $cropX = round(($originalWidth - $cropWidth) / 2); // Ořez ze středu
            $cropY = 0;
        }

        // Vytvoření dočasného obrázku pro ořez
        $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);
        
        // Zachování průhlednosti pro PNG
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($croppedImage, false);
            imagesavealpha($croppedImage, true);
        }

        // Provedení ořezu
        imagecopy($croppedImage, $sourceImage, 0, 0, $cropX, $cropY, $cropWidth, $cropHeight);

        // Výpočet finálních rozměrů pro změnu velikosti
        if ($cropWidth > $maxWidth || $cropHeight > $maxHeight) {
            $ratio = min($maxWidth / $cropWidth, $maxHeight / $cropHeight);
            $newWidth = round($cropWidth * $ratio);
            $newHeight = round($cropHeight * $ratio);
        } else {
            $newWidth = $cropWidth;
            $newHeight = $cropHeight;
        }

        // Vytvoření finálního obrázku
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Zachování průhlednosti pro PNG
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // Změna velikosti oříznutého obrázku
        imagecopyresampled(
            $newImage, $croppedImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $cropWidth, $cropHeight
        );

        // Uložení výsledku
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $targetPath, $quality);
                break;
            case IMAGETYPE_PNG:
                $pngQuality = $highQuality ? 1 : 6;
                imagepng($newImage, $targetPath, $pngQuality);
                break;
        }

        // Uvolnění paměti
        imagedestroy($newImage);
        imagedestroy($croppedImage);
        imagedestroy($sourceImage);
    }

    public function uploadImage()
    {
        $uploadDir = __DIR__ . '/../../../web/uploads/articles/';
        $publicPath = '/uploads/articles/';

        // ✅ Kontrola složky
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("❌ Nepodařilo se vytvořit složku: $uploadDir");
                http_response_code(500);
                echo json_encode(['error' => 'Nepodařilo se vytvořit složku.']);
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $file = $_FILES['file'];

            // ✅ Logování informací o souboru
            error_log("📝 Zpracovávám soubor: " . print_r($file, true));

            $fileName = uniqid() . '_' . basename($file['name']);
            $filePath = realpath($uploadDir) . DIRECTORY_SEPARATOR . $fileName; // 🔥 Převod na absolutní cestu
            $relativePath = $publicPath . $fileName;

            // ✅ Logování cílové cesty
            error_log("🛠 Cílová cesta pro obrázek: $filePath");

            // ✅ Ukládání souboru a logování úspěchu nebo chyby
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                @LogHelper::admin('Article image uploaded (editor)', 'File: ' . basename($filePath) . ', Size: ' . $file['size'] . ' bytes');
                error_log("✅ Soubor úspěšně uložen na: $filePath");
                header('Content-Type: application/json');
                echo json_encode(['location' => $relativePath]);
            } else {
                error_log("❌ Chyba při přesunu souboru do: $filePath");
                http_response_code(500);
                echo json_encode(['error' => 'Nepodařilo se přesunout soubor.']);
            }
        } else {
            error_log("❌ Neplatný požadavek nebo soubor chybí.");
            http_response_code(400);
            echo json_encode(['error' => 'Neplatný požadavek nebo soubor chybí.']);
        }
    }

    /**
     * Opraví relativní cesty k obrázkům v HTML obsahu
     * Změní cesty obsahující ../ na absolutní cesty /uploads/articles/
     */
    private function fixImagePaths($html) {
        // Použijeme regulární výraz k nalezení všech obrázků a jejich src atributů
        return preg_replace_callback(
            '/<img[^>]*?src=(["\'])(.*?)\\1/i',
            function($matches) {
                $src = $matches[2];
                
                // Pokud src obsahuje uploads/articles, extrahujeme název souboru
                if (strpos($src, 'uploads/articles/') !== false) {
                    $parts = explode('uploads/articles/', $src);
                    if (isset($parts[1])) {
                        // Vytvoříme absolutní cestu
                        return str_replace($src, '/uploads/articles/' . $parts[1], $matches[0]);
                    }
                }
                
                return $matches[0]; // Pokud se nejedná o náš typ obrázku, vrátíme původní tag
            },
            $html
        );
    }
}
