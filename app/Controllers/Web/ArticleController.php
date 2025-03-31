<?php

namespace App\Controllers\Web;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

class ArticleController
{
    private $articleModel;
    private $categoryModel;
    private $userModel;

    public function __construct($db)
    {
        $this->articleModel = new Article($db);
        $this->categoryModel = new Category($db);
        $this->userModel = new User($db);
    }

    // Zobrazení všech článků
    public function index()
    {
        $articles = $this->articleModel->getAllWithAuthors();
        $css = ["main-page", "kategorie"];

        $view = '../app/Views/Web/articles/index.php';
        require '../app/Views/Web/layouts/base.php';
    }

    // Zobrazení jednoho článku
    public function articleDetail($url)
    {
        $article = $this->articleModel->getByUrl($url);
        if (!$article) {
            header("HTTP/1.0 404 Not Found");
            // SEO pro 404
            $title = "Stránka nenalezena | Cyklistický magazín";
            $description = "Hledaný článek nebyl nalezen. Zkuste navštívit hlavní stránku nebo použít vyhledávání.";
            
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $this->articleModel->incrementViews($article['id']);

        // Získání souvisejících článků s ošetřením, pokud nejsou žádné nalezeny
        $relatedArticles = $this->articleModel->getRelatedArticles($article['id'], 3);
        // Kontrola, zda jsou related articles pole
        if (!is_array($relatedArticles)) {
            $relatedArticles = [];
        }

        $author = $this->userModel->getById($article['user_id']);
        
        // SEO nastavení
        $title = isset($article['nazev']) ? $article['nazev'] . " | Cyklistický magazín" : "Cyklistický magazín";
        $description = isset($article['obsah']) ? substr(strip_tags($article['obsah']), 0, 155) . "..." : "Cyklistický magazín";
        $ogTitle = isset($article['nazev']) ? $article['nazev'] : "Cyklistický magazín";
        $ogDescription = $description;
        $canonicalUrl = "https://vincenon21.mp.spse-net.cz/article/" . (isset($article['url']) ? $article['url'] : "");
        $ogImage = isset($article['nahled_foto']) && $article['nahled_foto'] ? "https://vincenon21.mp.spse-net.cz/" . $article['nahled_foto'] : null;
        
        // Structured data pro článek
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => isset($article['nazev']) ? $article['nazev'] : "Cyklistický magazín",
            "image" => $ogImage,
            "datePublished" => isset($article['datum']) ? $article['datum'] : date("Y-m-d"),
            "dateModified" => isset($article['updated_at']) ? $article['updated_at'] : (isset($article['datum']) ? $article['datum'] : date("Y-m-d")),
            "author" => [
                "@type" => "Person",
                "name" => $author['name'] . " " . $author['surname'],
                "url" => "https://vincenon21.mp.spse-net.cz/author/" . $author['name'] . "-" . $author['surname']
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => "Cyklistický magazín",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => "https://vincenon21.mp.spse-net.cz/assets/graphics/logo_text_cyklistickey.png"
                ]
            ],
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $canonicalUrl
            ]
        ];
        
        // Absolutní cesta k audio souboru
        $audioFilePath = __DIR__ . '/../../../web/uploads/audio/' . $article['id'] . '.mp3';
            
        // Vypneme zobrazení chyb při kontrole existence souboru
        $fileExists = @file_exists($audioFilePath);
        
        // Nastavíme cestu pro přehrávač pouze pokud soubor existuje
        if ($fileExists) {
            $audioUrl = '/uploads/audio/' . $article['id'] . '.mp3';
        } else {
            $audioUrl = null;
        }
        
        // Cesta k empty_clanek.php pro případ, že nejsou nalezeny žádné články
        $emptyArticlePath = '../app/Views/Web/templates/empty_clanek.php';

        $css = ["main-page", "clanek", "autor_clanku"];

        $view = '../app/Views/Web/articles/article.php';
        require '../app/Views/Web/layouts/base.php';
    }
}