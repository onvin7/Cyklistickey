<?php

namespace App\Controllers\Admin;

use App\Models\FlashNewsJSONSimple;
use App\Helpers\CSRFHelper;

class FlashNewsJSONAdminController
{
    private $model;

    public function __construct()
    {
        $this->model = new FlashNewsJSONSimple();
    }

    /**
     * Zobrazí seznam flash news
     */
    public function index()
    {
        try {
            $flashNews = $this->model->getAll();
            $stats = $this->model->getStats();
        } catch (Exception $e) {
            $flashNews = [];
            $stats = [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'news_count' => 0,
                'tech_count' => 0,
                'custom_count' => 0
            ];
            $_SESSION['error'] = 'Chyba při načítání flash news: ' . $e->getMessage();
        }
        
        $view = '../app/Views/Admin/flashnews/index.php';
        require '../app/Views/Admin/layout/base.php';
    }

    /**
     * Zobrazí formulář pro vytvoření nové flash news
     */
    public function create()
    {
        $view = '../app/Views/Admin/flashnews/create.php';
        require '../app/Views/Admin/layout/base.php';
    }

    /**
     * Zpracuje vytvoření nové flash news
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/flashnews');
            exit;
        }

        if (!CSRFHelper::checkPostToken()) {
            $_SESSION['error'] = 'Neplatný CSRF token';
            header('Location: /admin/flashnews/create');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'type' => $_POST['type'] ?? 'custom',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'created_by_name' => $_SESSION['email'] ?? 'Admin'
        ];

        // Validace
        if (empty($data['title'])) {
            $_SESSION['error'] = 'Název je povinný';
            header('Location: /admin/flashnews/create');
            exit;
        }

        if (strlen($data['title']) > 500) {
            $_SESSION['error'] = 'Název je příliš dlouhý (max 500 znaků)';
            header('Location: /admin/flashnews/create');
            exit;
        }

        try {
            if ($this->model->create($data)) {
                $_SESSION['success'] = 'Flash news byla úspěšně vytvořena';
            } else {
                $_SESSION['error'] = 'Chyba při vytváření flash news';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba při vytváření flash news: ' . $e->getMessage();
        }

        header('Location: /admin/flashnews');
        exit;
    }

    /**
     * Zobrazí formulář pro úpravu flash news
     */
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: /admin/flashnews');
            exit;
        }

        try {
            $flashNews = $this->model->getById($id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba při načítání flash news: ' . $e->getMessage();
            header('Location: /admin/flashnews');
            exit;
        }
        
        if (!$flashNews) {
            $_SESSION['error'] = 'Flash news nebyla nalezena';
            header('Location: /admin/flashnews');
            exit;
        }

        $view = '../app/Views/Admin/flashnews/edit.php';
        require '../app/Views/Admin/layout/base.php';
    }

    /**
     * Zpracuje úpravu flash news
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/flashnews');
            exit;
        }

        if (!CSRFHelper::checkPostToken()) {
            $_SESSION['error'] = 'Neplatný CSRF token';
            header('Location: /admin/flashnews');
            exit;
        }

        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            header('Location: /admin/flashnews');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'type' => $_POST['type'] ?? 'custom',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];

        // Validace
        if (empty($data['title'])) {
            $_SESSION['error'] = 'Název je povinný';
            header('Location: /admin/flashnews/edit?id=' . $id);
            exit;
        }

        if (strlen($data['title']) > 500) {
            $_SESSION['error'] = 'Název je příliš dlouhý (max 500 znaků)';
            header('Location: /admin/flashnews/edit?id=' . $id);
            exit;
        }

        try {
            if ($this->model->update($id, $data)) {
                $_SESSION['success'] = 'Flash news byla úspěšně aktualizována';
            } else {
                $_SESSION['error'] = 'Chyba při aktualizaci flash news';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba při aktualizaci flash news: ' . $e->getMessage();
        }

        header('Location: /admin/flashnews');
        exit;
    }

    /**
     * Smaže flash news
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/flashnews');
            exit;
        }

        if (!CSRFHelper::checkPostToken()) {
            $_SESSION['error'] = 'Neplatný CSRF token';
            header('Location: /admin/flashnews');
            exit;
        }

        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            header('Location: /admin/flashnews');
            exit;
        }

        try {
            if ($this->model->delete($id)) {
                $_SESSION['success'] = 'Flash news byla úspěšně smazána';
            } else {
                $_SESSION['error'] = 'Chyba při mazání flash news';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba při mazání flash news: ' . $e->getMessage();
        }

        header('Location: /admin/flashnews');
        exit;
    }

    /**
     * Přepne aktivní stav flash news
     */
    public function toggleActive()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/flashnews');
            exit;
        }

        if (!CSRFHelper::checkPostToken()) {
            $_SESSION['error'] = 'Neplatný CSRF token';
            header('Location: /admin/flashnews');
            exit;
        }

        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            header('Location: /admin/flashnews');
            exit;
        }

        try {
            if ($this->model->toggleActive($id)) {
                $_SESSION['success'] = 'Stav flash news byl změněn';
            } else {
                $_SESSION['error'] = 'Chyba při změně stavu flash news';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba při změně stavu flash news: ' . $e->getMessage();
        }

        header('Location: /admin/flashnews');
        exit;
    }

    /**
     * Aktualizuje pořadí flash news (AJAX)
     */
    public function updateSortOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        if (!CSRFHelper::checkAjaxToken()) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        $sortOrder = $_POST['sort_order'] ?? null;
        
        if (!$id || $sortOrder === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required parameters']);
            exit;
        }

        try {
            if ($this->model->updateSortOrder($id, $sortOrder)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update sort order']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update sort order: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Zobrazí náhled flash news
     */
    public function preview()
    {
        try {
            $flashNews = $this->model->getForDisplay();
        } catch (Exception $e) {
            $flashNews = [];
            $_SESSION['error'] = 'Chyba při načítání flash news: ' . $e->getMessage();
        }
        
        $view = '../app/Views/Admin/flashnews/preview.php';
        require '../app/Views/Admin/layout/base.php';
    }

    /**
     * Aktualizuje data z API
     */
    public function refresh()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/flashnews');
            exit;
        }

        if (!CSRFHelper::checkPostToken()) {
            $_SESSION['error'] = 'Neplatný CSRF token';
            header('Location: /admin/flashnews');
            exit;
        }

        try {
            if ($this->model->refreshFromAPI()) {
                $_SESSION['success'] = 'Flash news byla úspěšně aktualizována z API';
            } else {
                $_SESSION['error'] = 'Chyba při aktualizaci z API';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba při aktualizaci z API: ' . $e->getMessage();
        }

        header('Location: /admin/flashnews');
        exit;
    }
}


