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
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $this->articleModel->incrementViews($article['id']);

        $relatedArticles = $this->articleModel->getRelatedArticles($article['id'], 3);

        if ($article['autor'] == 1) {
            $author = $this->userModel->getById($article['user_id']);
        } else {
            $author = $this->userModel->getById(0);
        }

        // Cesta k empty_clanek.php pro případ, že nejsou nalezeny žádné články
        $emptyArticlePath = '../app/Views/Web/templates/empty_clanek.php';

        $css = ["main-page", "clanek", "autor_clanku"];

        $view = '../app/Views/Web/articles/article.php';
        require '../app/Views/Web/layouts/base.php';
    }

    public function index2()
    {
        $main_article = $this->articleModel->getNewestArticle();
        $articles = $this->articleModel->getLatestArticles(4, 1);
        $categories = $this->articleModel->getCategoriesWithArticlesSorted();
        //echo '<pre>', var_dump($categories), '</pre>';

        if (!is_array($categories)) {
            $categories = [];
        }

        $css = ['main-page'];

        $view = '../app/Views/Web/home/index.php';
        require '../app/Views/Web/layouts/base.php';
    }
}
