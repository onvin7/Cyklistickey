<?php

namespace App\Controllers\Web;

use App\Models\Category;
use App\Models\Article;

class CategoryController
{
    private $categoryModel;
    private $articleModel;

    public function __construct($db)
    {
        $this->categoryModel = new Category($db);
        $this->articleModel = new Article($db);
    }

    public function index()
    {
        $categories = $this->categoryModel->getAll();

        $css = ["main-page", "kategorie"];
        
        // SEO nastavení
        $title = "Kategorie | Cyklistický magazín";
        $description = "Prohlédněte si články z našeho magazínu rozdělené do tematických kategorií.";
        $ogTitle = "Tematické kategorie | Cyklistický magazín";
        $ogDescription = "Vyberte si z tematických kategorií a objevte články, které vás zajímají.";
        $canonicalUrl = "https://vincenon21.mp.spse-net.cz/categories";
        
        // Structured data pro seznam kategorií
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Kategorie Cyklistického magazínu",
            "url" => $canonicalUrl,
            "description" => $description
        ];

        $view = '../app/Views/Web/category/index.php';
        include '../app/Views/Web/layouts/base.php';
    }

    public function listByCategory($url)
    {
        $category = $this->categoryModel->getByUrl($url);
        
        if (!$category) {
            header("HTTP/1.0 404 Not Found");
            // SEO pro 404
            $title = "Kategorie nenalezena | Cyklistický magazín";
            $description = "Požadovaná kategorie nebyla nalezena. Zkuste navštívit stránku s přehledem kategorií.";
            
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $articles = $this->categoryModel->getArticlesByCategory($category['id']);

        $css = ["kategorie"];
        
        // SEO nastavení
        $title = $category['nazev_kategorie'] . " | Cyklistický magazín";
        $description = $category['popis'] ?? "Přečtěte si články z kategorie " . $category['nazev_kategorie'] . " na Cyklistickém magazínu.";
        $ogTitle = "Kategorie " . $category['nazev_kategorie'] . " | Cyklistický magazín";
        $ogDescription = $description;
        $canonicalUrl = "https://vincenon21.mp.spse-net.cz/category/" . $category['url'];
        
        // Structured data pro kategorii
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Kategorie " . $category['nazev_kategorie'],
            "url" => $canonicalUrl,
            "description" => $description
        ];

        $view = '../app/Views/Web/category/categoryDetail.php';
        include '../app/Views/Web/layouts/base.php';
    }
}
