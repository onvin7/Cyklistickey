<?php

namespace App\Controllers\Web;

use App\Models\Category;
use App\Models\Article;

class HomeController
{
    private $db;
    private $articleModel;
    private $categoryModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->articleModel = new Article($db);
        $this->categoryModel = new Category($db);
    }

    public function index()
    {
        $main_article = $this->articleModel->getNewestArticle();
        $articles = $this->articleModel->getLatestArticles(4, 0);
        $categories = $this->articleModel->getCategoriesWithArticlesSorted();

        if (!is_array($categories)) {
            $categories = [];
        }

        $css = ['main-page'];
        
        // SEO nastavení pro hlavní stránku
        $title = "Cyklistický magazín – Novinky, závody a technika";
        $description = "Sledujte nejnovější zprávy, tréninkové tipy, technické novinky a rozhovory ze světa cyklistiky.";
        $canonicalPath = "";
        $keywords = ["cyklistika", "kolo", "závody", "trénink", "technika", "novinky"];
        
        // Breadcrumbs pro hlavní stránku
        $breadcrumbs = [
            ['name' => 'Domů', 'url' => '/']
        ];
        
        // Structured data pro webovou stránku
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => "Cyklistický magazín",
            "url" => "https://www.cyklistickey.cz",
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => "https://www.cyklistickey.cz/search?q={search_term_string}",
                "query-input" => "required name=search_term_string"
            ]
        ];

        $view = '../app/Views/Web/home/index.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function kontakt()
    {
        $css = ['kontakt'];
        $script = ['kontakt'];
        
        // SEO nastavení
        $title = "Kontakt | Cyklistický magazín";
        $description = "Kontaktujte redakci Cyklistického magazínu. Jsme tu pro vaše dotazy, návrhy, či spolupráci.";
        $ogTitle = "Kontaktujte nás | Cyklistický magazín";
        $ogDescription = "Máte dotaz nebo návrh? Kontaktujte redakci Cyklistického magazínu a budeme rádi za vaši zpětnou vazbu.";
        $canonicalUrl = "https://www.cyklistickey.cz/kontakt";
        
        // Structured data pro kontaktní stránku
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "ContactPage",
            "name" => "Kontaktní stránka - Cyklistický magazín",
            "url" => $canonicalUrl
        ];

        $view = '../app/Views/Web/home/kontakt.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function race()
    {
        // SEO nastavení
        $title = "Cyklistickey Race | Cyklistický magazín";
        $description = "Informace o závodech Cyklistickey Race, registrace, výsledky a fotogalerie z akcí.";
        $ogTitle = "Cyklistickey Race - Naše závody pro všechny nadšence cyklistiky";
        $ogDescription = "Přehled závodů Cyklistickey Race, pravidla, trasy a možnosti registrace.";
        $canonicalUrl = "https://www.cyklistickey.cz/race";
        $css = ['race', 'race-main'];

        $view = '../app/Views/Web/race/race.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function raceCyklistickey()
    {
        // SEO nastavení
        $title = "Cyklistickey Race | Cyklistický magazín";
        $description = "Detailní informace o závodě Cyklistickey Race, trasy, pravidla a praktické informace pro závodníky.";
        $ogTitle = "Cyklistickey Race - Závod pro všechny cyklistické nadšence";
        $ogDescription = "Kompletní informace o závodě Cyklistickey Race - trasy, registrace, pravidla a praktické informace.";
        $canonicalUrl = "https://www.cyklistickey.cz/race/cyklistickey";
        $css = ['race'];

        $view = '../app/Views/Web/race/cyklistickey_race.php';
        require '../app/Views/Web/layouts/base.php';
    }
    
    public function raceBezeckey()
    {
        // SEO nastavení
        $title = "Běžeckey Race | Cyklistický magazín";
        $description = "Vše o běžeckém závodu Běžeckey Race - termíny, trasy, podmínky účasti a registrace.";
        $ogTitle = "Běžeckey Race - Běžecký závod pro každého";
        $ogDescription = "Detailní informace o běžeckém závodu Běžeckey Race - registrace, pravidla a další praktické informace.";
        $canonicalUrl = "https://www.cyklistickey.cz/race/bezeckey";
        $css = ['race'];

        $view = '../app/Views/Web/race/bezeckey_race.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function events()
    {
        // SEO nastavení
        $title = "Events | Cyklistický magazín";
        $description = "Přehled všech akcí a událostí souvisejících s cyklistikou - závody, výstavy, workshopy a další.";
        $ogTitle = "Events - Cyklistické akce a události";
        $ogDescription = "Kalendář cyklistických akcí, závodů, výstav a dalších událostí pro všechny milovníky cyklistiky.";
        $canonicalUrl = "https://www.cyklistickey.cz/events";
        $css = ['kategorie', 'events'];

        $view = '../app/Views/Web/events/index.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function eventDetail($year, $name)
    {
        // Mapování názvů eventů na existující views
        $eventViews = [
            'cyklistickey' => '../app/Views/Web/race/cyklistickey_race.php',
            'bezeckey' => '../app/Views/Web/race/bezeckey_race.php',
        ];

        if (!isset($eventViews[$name])) {
            http_response_code(404);
            $view = "../app/Views/Web/templates/404.php";
            require "../app/Views/Web/layouts/base.php";
            exit;
        }

        // SEO nastavení
        $eventTitle = ucfirst($name) . " Race " . $year;
        $title = $eventTitle . " | Cyklistický magazín";
        $description = "Detailní informace o závodě " . $eventTitle . ", trasy, pravidla a praktické informace pro závodníky.";
        $ogTitle = $eventTitle . " - Závod pro všechny cyklistické nadšence";
        $ogDescription = "Kompletní informace o závodě " . $eventTitle . " - trasy, registrace, pravidla a praktické informace.";
        $canonicalUrl = "https://www.cyklistickey.cz/events/" . $year . "/" . $name;
        $css = ['race'];

        $view = $eventViews[$name];
        require '../app/Views/Web/layouts/base.php';
    }

    public function appka()
    {
        // SEO nastavení
        $title = "Appka | Cyklistickey";
        $description = "Články a aktuality ze všech koutů cyklistiky. Vše hezky na jednom místě.";
        $ogTitle = "Cyklistickey App - Mobilní aplikace pro cyklisty";
        $ogDescription = "Mobilní aplikace Cyklistickey - novinky, trasy, závody a vše o cyklistice na jednom místě.";
        $canonicalUrl = "https://www.cyklistickey.cz/appka";
        $css = ['appka'];

        $view = '../app/Views/Web/home/appka.php';
        require '../app/Views/Web/layouts/base.php';
    }
}
