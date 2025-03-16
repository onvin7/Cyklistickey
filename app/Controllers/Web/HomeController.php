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

        $view = '../app/Views/Web/home/index.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function kontakt()
    {
        $title = "Kontakt | Cyklistickey";
        $css = ['kontakt'];
        $script = ['kontakt'];

        $view = '../app/Views/Web/home/kontakt.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function race()
    {
        $title =  "Cyklistickey Race | Cyklistickey";
        $css = ['race', 'race-main'];

        $view = '../app/Views/Web/race/race.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function raceCyklistickey()
    {
        $title =  "Cyklistickey Race | Cyklistickey";
        $css = ['race'];

        $view = '../app/Views/Web/race/cyklistickey_race.php';
        require '../app/Views/Web/layouts/base.php';
    }
    public function raceBezeckey()
    {
        $title =  "Běžeckey Race | Cyklistickey";
        $css = ['race'];

        $view = '../app/Views/Web/race/bezeckey_race.php';
        require '../app/Views/Web/layouts/base.php';
    }
}
