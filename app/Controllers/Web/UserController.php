<?php

namespace App\Controllers\Web;

use App\Models\User;
use App\Models\Article;
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
        $css = ['main-page'];

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
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $socials = $this->userModel->getSocials($user['id']);

        $relatedArticles = $this->articleModel->getByUser($user['id'], 3);

        $css = ["main-page", "autor_clanku"];

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
            $view = '../app/Views/Web/templates/404.php';
            require '../app/Views/Web/layouts/base.php';
            exit;
        }

        $articles = $this->articleModel->getByIdUser($user['id']);

        $css = ["kategorie"];

        $view = '../app/Views/Web/user/article.php';
        include '../app/Views/Web/layouts/base.php';
    }

    // Registrace uživatele
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST['email'], $_POST['heslo'], $_POST['role'], $_POST['name'], $_POST['surname']);
            header('Location: /login');
        } else {
            include '../../app/Views/Web/users/register.php';
        }
    }

    // Přihlášení uživatele
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->model->authenticate($_POST['email'], $_POST['heslo']);
            if ($user) {
                session_start();
                $_SESSION['user'] = $user;
                header('Location: /');
            } else {
                $error = "Špatný e-mail nebo heslo";
                include '../../app/Views/Web/users/login.php';
            }
        } else {
            include '../../app/Views/Web/users/login.php';
        }
    }

    public function showLoginForm()
    {
        include '../../app/Views/Web/users/login.php';
    }

    // Odhlášení uživatele
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /');
    }
}
