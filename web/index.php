<?php

require '../config/db.php';
require '../config/autoloader.php';

use App\Controllers\Web\ArticleController;
use App\Controllers\Web\HomeController;
use App\Controllers\Web\UserController;
use App\Controllers\Web\CategoryController;
use App\Controllers\LoginController;

$db = (new Database())->connect();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

if (preg_match('/^\/uploads\/(.+)$/', $uri, $matches)) {
    $filePath = __DIR__ . $uri;
    if (file_exists($filePath)) {
        // ✅ Správné nastavení hlavičky pro zobrazení obrázků
        $mimeType = mime_content_type($filePath);
        header("Content-Type: $mimeType");
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        $view = "../app/Views/Web/templates/404.php";
        require "../app/Views/Web/layouts/base.php";
        exit;
    }
}

// Handling pro hunspell soubory
if (preg_match('/^\/js\/hunspell\/(.+)$/', $uri, $matches)) {
    $filePath = __DIR__ . $uri;
    if (file_exists($filePath)) {
        // Nastavení správných hlaviček pro hunspell soubory
        if (strpos($filePath, '.aff') !== false) {
            header("Content-Type: text/plain; charset=utf-8");
        } elseif (strpos($filePath, '.dic') !== false) {
            header("Content-Type: text/plain; charset=utf-8");
        }
        header("Access-Control-Allow-Origin: *");
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        exit;
    }
}

$routes = [
    '/' => [HomeController::class, 'index'],
    '' => [HomeController::class, 'index'],
    '/category/([^/]+)' => [CategoryController::class, 'listByCategory'],
    '/article/([^/]+)' => [ArticleController::class, 'articleDetail'],
    '/categories' => [CategoryController::class, 'index'],
    '/articles' => [ArticleController::class, 'index'],
    '/authors' => [UserController::class, 'index'],
    '/login' => [LoginController::class, 'showLoginForm'],
    '/login/submit' => [LoginController::class, 'login'],
    '/logout' => [LoginController::class, 'logout'],
    '/kontakt' => [HomeController::class, 'kontakt'],
    '/race' => [HomeController::class, 'race'],
    '/race/cyklistickey' => [HomeController::class, 'raceCyklistickey'],
    '/race/bezeckey' => [HomeController::class, 'raceBezeckey'],
    '/events' => [HomeController::class, 'events'],
    '/appka' => [HomeController::class, 'appka'],
    '/register' => [LoginController::class, 'create'],
    '/register/submit' => [LoginController::class, 'store'],
    '/reset-password' => [LoginController::class, isset($_GET['token']) ? 'confirmResetPassword' : 'reset'],
    '/reset-password/submit' => [LoginController::class, 'resetPassword'],
    '/reset-password/link' => [LoginController::class, 'showResetLink'],
    '/reset-password/save' => [LoginController::class, 'saveNewPassword'],
    '/user/([^/]+)' => [UserController::class, 'userDetail'],
    '/user/([^/]+)/articles' => [UserController::class, 'userArticles'],
    '/sitemap.xml' => ['sitemap', 'generate'],
    '/robots.txt' => ['robots', 'generate'],
];

$routeFound = false;

foreach ($routes as $path => $route) {
    if (preg_match('#^' . $path . '$#', $uri, $matches)) {
        // Speciální handling pro sitemap a robots
        if ($route[0] === 'sitemap') {
            include 'sitemap.php';
            exit;
        }
        
        if ($route[0] === 'robots') {
            header('Content-Type: text/plain');
            readfile('robots.txt');
            exit;
        }
        
        $controllerClass = $route[0];
        $method = $route[1];
        $param = $matches[1] ?? null;

        $controller = new $controllerClass($db);

        if ($method === 'login') {
            $controller->$method($_POST['email'], $_POST['password']);
        } else if ($param) {
            $controller->$method($param);
        } else {
            $controller->$method();
        }

        $routeFound = true;
        break;
    }
}

if (!$routeFound) {
    http_response_code(404);
    $view = "../app/Views/Web/templates/404.php";
    require "../app/Views/Web/layouts/base.php";
    exit;
}
