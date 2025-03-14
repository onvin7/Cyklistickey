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

        $view = '../app/Views/Web/category/index.php';
        include '../app/Views/Web/layouts/base.php';
    }

    public function listByCategory($url)
    {
        $category = $this->categoryModel->getByUrl($url);

        $articles = $this->categoryModel->getArticlesByCategory($category['id']);

        $css = ["kategorie"];

        $view = '../app/Views/Web/category/categoryDetail.php';
        include '../app/Views/Web/layouts/base.php';
    }
}
