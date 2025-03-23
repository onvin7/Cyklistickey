<?php
session_start();

// Kontrola, zda není požadavek z /web/admin
if (strpos($_SERVER['REQUEST_URI'], '/web/admin') === 0) {
    $newUri = str_replace('/web/admin', '/admin', $_SERVER['REQUEST_URI']);
    header("Location: " . $newUri);
    exit;
}

require '../config/db.php';
require '../config/autoloader.php';

use App\Middleware\AuthMiddleware;
use App\Controllers\Admin\HomeAdminController;
use App\Controllers\Admin\StatisticsAdminController;
use App\Controllers\Admin\ArticleAdminController;
use App\Controllers\Admin\CategoryAdminController;
use App\Controllers\Admin\UserAdminController;
use App\Controllers\Admin\AccessControlAdminController;
use App\Controllers\Admin\PromotionAdminController;
use App\Controllers\LoginController;

// ✅ **Inicializace připojení k databázi**
$db = (new Database())->connect();

// ✅ **Middleware pro ověření přístupu**
AuthMiddleware::check($db);

// ✅ **Definice dostupných rout**
$routes = [
    'statistics' => [StatisticsAdminController::class, 'index'],
    'statistics/articles' => [StatisticsAdminController::class, 'articles'],
    'statistics/categories' => [StatisticsAdminController::class, 'categories'],
    'statistics/authors' => [StatisticsAdminController::class, 'authors'],
    'statistics/performance' => [StatisticsAdminController::class, 'performance'],
    'statistics/views' => [StatisticsAdminController::class, 'views'],
    'statistics/top' => [StatisticsAdminController::class, 'top'],
    'statistics/view' => [StatisticsAdminController::class, 'view', 'id'],
    'statistics/article-details/(\d+)' => [StatisticsAdminController::class, 'getArticleDetails', 'articleId'],
    'statistics/category-details/(\d+)' => [StatisticsAdminController::class, 'getCategoryDetails', 'categoryId'],
    'statistics/author-details/(\d+)' => [StatisticsAdminController::class, 'getAuthorDetails', 'authorId'],
    'articles' => [ArticleAdminController::class, 'index'],
    'articles/create' => [ArticleAdminController::class, 'create'],
    'articles/store' => [ArticleAdminController::class, 'store', 'data'],
    'articles/edit' => [ArticleAdminController::class, 'edit', 'id'],
    'articles/update' => [ArticleAdminController::class, 'update', 'id'],
    'articles/delete' => [ArticleAdminController::class, 'delete', 'id'],
    'categories' => [CategoryAdminController::class, 'index'],
    'categories/create' => [CategoryAdminController::class, 'create'],
    'categories/store' => [CategoryAdminController::class, 'store'],
    'categories/edit' => [CategoryAdminController::class, 'edit', 'id'],
    'categories/update' => [CategoryAdminController::class, 'update', 'id'],
    'categories/delete' => [CategoryAdminController::class, 'delete', 'id'],
    'users' => [UserAdminController::class, 'index'],
    'users/edit' => [UserAdminController::class, 'edit', 'id'],
    'users/update' => [UserAdminController::class, 'update', 'id'],
    'users/delete' => [UserAdminController::class, 'delete', 'id'],
    'access-control' => [AccessControlAdminController::class, 'index'],
    'access-control/update' => [AccessControlAdminController::class, 'update'],
    'logout' => [LoginController::class, 'logout'],
    'upload-image' => [ArticleAdminController::class, 'uploadImage'],
    'promotions' => [PromotionAdminController::class, 'index'],
    'promotions/create' => [PromotionAdminController::class, 'create'],
    'promotions/store' => [PromotionAdminController::class, 'store'],
    'promotions/upcoming' => [PromotionAdminController::class, 'upcoming'],
    'promotions/history' => [PromotionAdminController::class, 'history'],
    'promotions/delete' => [PromotionAdminController::class, 'delete', 'id'],
    'settings' => [UserAdminController::class, 'settings'],
    'settings/update' => [UserAdminController::class, 'updateSettings'],
    'social-sites' => [UserAdminController::class, 'socialSites'],
    'social-sites/save' => [UserAdminController::class, 'saveSocialSite'],
    'social-sites/delete' => [UserAdminController::class, 'deleteSocialSite', 'id'],
];

// ✅ **Načtení přístupných rout ze session**
$accessibleRoutes = $_SESSION['accessibleRoutes'] ?? array_keys($routes);

// ✅ **Zpracování URI**
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/admin/', '', $uri);
$uri = trim($uri, '/');

// ✅ **Pokud je hlavní stránka, pustíme ji vždy**
if ($uri === '' || $uri === 'home') {
    (new HomeAdminController($db))->index();
    exit();
}

// ✅ **Dynamické zpracování rout** 
$routeFound = false;

foreach ($routes as $path => $route) {
    // Zjistíme, jestli je v cestě přímo regulární výraz
    if (strpos($path, '(') !== false) {
        // Použijeme cestu přímo jako vzor
        $pattern = '#^' . $path . '$#';
    } else {
        // Jinak použijeme původní logiku
        $pattern = '#^' . $path;
        
        // Zjistíme, jaký parametr očekáváme
        $expectedParam = $route[2] ?? 'id';
        
        // Přidáme vzor pro parametr do URL
        if (isset($route[2])) {
            $pattern .= '(/(\d+))';
        }
        
        $pattern .= '$#';
    }
    
    if (preg_match($pattern, $uri, $matches)) {
        $controllerClass = $route[0];
        $method = $route[1];
        
        // Získáme parametr podle toho, jaký typ URL vzoru byl použit
        if (strpos($path, '(') !== false) {
            $param = $matches[1] ?? null;
        } else {
            $param = $matches[2] ?? null;
        }

        // ✅ **Kontrola přístupu k dané stránce pro role 1 a 2**
        if ($accessibleRoutes !== null) {
            $routeBase = $path;
            
            // U cest s regulárními výrazy extrahujeme základní cestu pro kontrolu přístupu
            if (strpos($path, '(') !== false) {
                $routeBase = substr($path, 0, strpos($path, '('));
                $routeBase = rtrim($routeBase, '/');
            }
            
            // Kontrolujeme, zda je základní cesta nebo celá cesta v dostupných cestách
            if (!in_array($routeBase, $accessibleRoutes) && !in_array($path, $accessibleRoutes)) {
                echo "<script>alert('Na tuto stránku nemáte přístup.'); window.history.back();</script>";
                $routeFound = true;
                break;
            }
        }

        $controller = new $controllerClass($db);

        // ✅ **Zpracování metod podle HTTP požadavku**
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'articles/store') {
            $controller->$method($_POST);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'articles/update') {
            $controller->$method($param, $_POST);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'categories/store') {
            $controller->$method($_POST);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'categories/update') {
            $controller->$method($param, $_POST);
        } elseif ($param) {
            // Zpracování GET parametrů podle očekávaného názvu
            $controller->$method($param);
        } else {
            $controller->$method();
        }

        $routeFound = true;
        break;
    }
}

// ✅ **Pokud routa nebyla nalezena, vypíšeme chybu**
if (!$routeFound) {
    die("Err: Stránka nenalezena -> " . $uri);
} 