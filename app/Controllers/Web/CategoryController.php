<?php

namespace App\Controllers\Web;

use App\Models\Category;
use App\Models\Article;
use App\Helpers\SEOHelper;

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
        $keywords = ["kategorie", "témata", "cyklistika", "články"];
        $title = "Kategorie";
        $description = "Prohlédněte si články z našeho magazínu rozdělené do tematických kategorií.";
        $canonicalPath = "categories";
        $canonicalUrl = SEOHelper::generateCanonicalUrl($canonicalPath);
        
        // Breadcrumbs pro kategorie
        $breadcrumbs = [
            ['name' => 'Domů', 'url' => '/'],
            ['name' => 'Kategorie', 'url' => '/categories']
        ];
        
        // Structured data pro seznam kategorií
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Kategorie Cyklistického magazínu",
            "url" => $canonicalUrl,
            "description" => $description
        ];
        
        // Přidání breadcrumb schema
        $structuredData = [
            $structuredData,
            SEOHelper::generateBreadcrumbSchema($breadcrumbs)
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
        $keywords = ["kategorie", $category['nazev_kategorie'], "cyklistika", "články"];
        $title = $category['nazev_kategorie'] . " | Cyklistický magazín";
        $description = $category['popis'] ?? "Přečtěte si články z kategorie " . $category['nazev_kategorie'] . " na Cyklistickém magazínu.";
        $ogTitle = "Kategorie " . $category['nazev_kategorie'] . " | Cyklistický magazín";
        $ogDescription = $description;
        $canonicalUrl = SEOHelper::generateCanonicalUrl("category/" . $category['url']);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Domů', 'url' => '/'],
            ['name' => 'Kategorie', 'url' => '/categories'],
            ['name' => $category['nazev_kategorie'], 'url' => '/category/' . $category['url']]
        ];
        
        // Structured data pro kategorii
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Kategorie " . $category['nazev_kategorie'],
            "url" => $canonicalUrl,
            "description" => $description
        ];
        
        // Přidání breadcrumb schema
        $structuredData = [
            $structuredData,
            SEOHelper::generateBreadcrumbSchema($breadcrumbs)
        ];

        $view = '../app/Views/Web/category/categoryDetail.php';
        include '../app/Views/Web/layouts/base.php';
    }
}
