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

$routes = [
    '/' => [HomeController::class, 'index'],
    '' => [HomeController::class, 'index'],
    '/category' => [CategoryController::class, 'listByCategory', 'slug'],
    '/article' => [ArticleController::class, 'articleDetail', 'slug'],
    '/categories' => [CategoryController::class, 'index'],
    '/articles' => [ArticleController::class, 'index'],
    '/login' => [LoginController::class, 'showLoginForm'],
    '/logout' => [LoginController::class, 'logout'],
    '/kontakt' => [HomeController::class, 'kontakt'],
    '/race' => [HomeController::class, 'race'],
    '/race/cyklistickey' => [HomeController::class, 'raceCyklistickey'],
    '/race/bezeckey' => [HomeController::class, 'raceBezeckey'],
    '/register' => [LoginController::class, 'create'],
    '/register/submit' => [LoginController::class, 'store'],
    '/reset-password' => [LoginController::class, 'reset'],
    '/user' => [UserController::class, 'index', 'username'],
];

$routeFound = false;

foreach ($routes as $path => $route) {
    if (preg_match('#^' . preg_quote($path, '#') . '$#', $uri, $matches)) {
        $controllerClass = $route[0];
        $method = $route[1];
        $param = $matches[2] ?? null;

        $controller = new $controllerClass($db);

        if ($param) {
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
