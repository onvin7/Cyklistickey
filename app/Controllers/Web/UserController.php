<?php

namespace App\Controllers\Web;

use App\Models\User;
use App\Models\Article;
use App\Helpers\SEOHelper;
class UserController
{
    private $userModel;
    private $articleModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
        $this->articleModel = new Article($db);
    }

    public function index()
    {
        $users = $this->userModel->getAll();
        $css = ['authors', 'kategorie'];
        
        // SEO nastavení
        $keywords = ["redakce", "autoři", "fotografové", "editoři", "cyklistika"];
        $title = "Redakce";
        $description = "Seznamte se s redakčním týmem Cyklistického magazínu - naši autoři, fotografové a editoři.";
        $canonicalPath = "authors";
        $canonicalUrl = SEOHelper::generateCanonicalUrl($canonicalPath);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Domů', 'url' => '/'],
            ['name' => 'Redakce', 'url' => '/authors']
        ];
        
        // Structured data pro seznam autorů
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Redakce Cyklistického magazínu",
            "url" => $canonicalUrl,
            "description" => $description
        ];
        
        // Přidání breadcrumb schema
        $structuredData = [
            $structuredData,
            SEOHelper::generateBreadcrumbSchema($breadcrumbs)
        ];

        $view = '../app/Views/Web/user/index.php';
        require '../app/Views/Web/layouts/base.php';
    }

    // Zobrazení jednoho článku
    public function userDetail($username)
    {
        $parts = explode('-', $username);
        $name = $parts[0];
        $surname = $parts[1];

        $user = $this->userModel->getByName($name, $surname);
  
        if (!$user) {
            header("HTTP/1.0 404 Not Found"); 
            // SEO pro 404
            $title = "Autor nenalezen | Cyklistický magazín";
            $description = "Požadovaný autor nebyl nalezen. Zkuste navštívit stránku s přehledem redakce.";
            
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $socials = $this->userModel->getSocials($user['id']);

        $relatedArticles = $this->articleModel->getByUser($user['id'], 3);

        $css = ["main-page", "autor_clanku"];
        
        // SEO nastavení
        $keywords = SEOHelper::extractKeywords($user['popis'] ?? '', 5);
        $title = $user['name'] . " " . $user['surname'];
        $description = SEOHelper::generateDescription($user['popis'] ?? null, null, $keywords);
        $canonicalPath = "author/" . $user['name'] . "-" . $user['surname'];
        $canonicalUrl = SEOHelper::generateCanonicalUrl($canonicalPath);
        $ogImage = $user['profil_foto'] ? SEOHelper::generateCanonicalUrl($user['profil_foto']) : null;
        
        // Breadcrumbs pro detail autora
        $breadcrumbs = [
            ['name' => 'Domů', 'url' => '/'],
            ['name' => 'Redakce', 'url' => '/authors'],
            ['name' => $user['name'] . " " . $user['surname'], 'url' => '/author/' . $user['name'] . "-" . $user['surname']]
        ];
        
        // Structured data pro autora
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "Person",
            "name" => $user['name'] . " " . $user['surname'],
            "url" => "https://www.cyklistickey.cz/author/" . $user['name'] . "-" . $user['surname'],
            "jobTitle" => $user['role'] ?? "Autor",
            "description" => strip_tags($user['popis'] ?? ""),
            "image" => $ogImage
        ];
        
        // Pokud máme sociální sítě, přidáme je
        if (!empty($socials)) {
            $sameAs = [];
            foreach ($socials as $social) {
                if (!empty($social['url'])) {
                    $sameAs[] = $social['url'];
                }
            }
            if (!empty($sameAs)) {
                $structuredData["sameAs"] = $sameAs;
            }
        }

        $view = '../app/Views/Web/user/detail.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function userArticles($username)
    {
        $parts = explode('-', $username);
        $name = $parts[0];
        $surname = $parts[1];

        $user = $this->userModel->getByName($name, $surname);
  
        if (!$user) {
            header("HTTP/1.0 404 Not Found"); 
            // SEO pro 404
            $title = "Autor nenalezen | Cyklistický magazín";
            $description = "Požadovaný autor nebyl nalezen. Zkuste navštívit stránku s přehledem redakce.";
            
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $articles = $this->articleModel->getByIdUser($user['id']);

        $css = ["kategorie"];
        
        // SEO nastavení
        $title = "Články od " . $user['name'] . " " . $user['surname'];
        $description = "Kompletní seznam článků, které napsal " . $user['name'] . " " . $user['surname'] . " pro Cyklistický magazín.";
        $canonicalPath = "author/" . $user['name'] . "-" . $user['surname'] . "/articles";
        $keywords = ["články", $user['name'], $user['surname'], "autor", "cyklistika"];
        
        // Breadcrumbs pro články autora
        $breadcrumbs = [
            ['name' => 'Domů', 'url' => '/'],
            ['name' => 'Redakce', 'url' => '/authors'],
            ['name' => $user['name'] . " " . $user['surname'], 'url' => '/author/' . $user['name'] . "-" . $user['surname']],
            ['name' => 'Články', 'url' => '/author/' . $user['name'] . "-" . $user['surname'] . '/articles']
        ];

        $view = '../app/Views/Web/user/article.php';
        include '../app/Views/Web/layouts/base.php';
    }
}
