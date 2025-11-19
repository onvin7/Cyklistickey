<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Helpers\LogHelper;

class CategoryAdminController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Category($db);
    }

    public function index()
    {
        $sortBy = $_GET['sort_by'] ?? 'id';      // Výchozí řazení podle ID
        $order = $_GET['order'] ?? 'ASC';       // Výchozí vzestupné řazení
        $filter = $_GET['filter'] ?? '';        // Výchozí bez filtru

        // Načtení kategorií s filtrováním a řazením
        $categories = $this->model->getAllWithSortingAndFiltering($sortBy, $order, $filter);

        $adminTitle = "Kategorie | Admin Panel - Cyklistickey magazín";

        // Zobrazení view
        $view = '../app/Views/Admin/categories/index.php';
        include '../app/Views/Admin/layout/base.php';
    }


    public function create()
    {
        $adminTitle = "Vytvořit kategorii | Admin Panel - Cyklistickey magazín";
        
        $view = '../app/Views/Admin/categories/create.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function store($postData)
    {
        if (empty($postData['nazev_kategorie'])) {
            echo "Název kategorie je povinný.";
            return;
        }

        $data = [
            'nazev_kategorie' => $postData['nazev_kategorie'],
            'url' => strtolower(preg_replace('/\s+/', '-', trim($postData['nazev_kategorie'])))
        ];

        $result = $this->model->create($data);

        if ($result) {
            @LogHelper::admin('Category created', 'Name: ' . ($data['nazev_kategorie'] ?? 'N/A'));
            header("Location: /admin/categories");
            exit;
        } else {
            @LogHelper::admin('Category create failed', 'Name: ' . ($data['nazev_kategorie'] ?? 'N/A'));
            echo "Chyba při ukládání kategorie.";
        }
    }

    public function edit($id)
    {
        $category = $this->model->getById($id);
        if (!$category) {
            echo "Kategorie nenalezena.";
            return;
        }

        $adminTitle = "Upravit kategorii: " . $category['nazev_kategorie'] . " | Admin Panel - Cyklistickey magazín";

        $view = '../app/Views/Admin/categories/edit.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function update($id, $postData)
    {
        if (empty($postData['nazev_kategorie'])) {
            echo "Název kategorie je povinný.";
            return;
        }

        $data = [
            'id' => $id,
            'nazev_kategorie' => $postData['nazev_kategorie'],
            'url' => strtolower(preg_replace('/\s+/', '-', trim($postData['nazev_kategorie'])))
        ];

        $result = $this->model->update($data);

        if ($result) {
            @LogHelper::admin('Category updated', 'ID: ' . $id . ', Name: ' . ($data['nazev_kategorie'] ?? 'N/A'));
            header("Location: /admin/categories");
            exit;
        } else {
            @LogHelper::admin('Category update failed', 'ID: ' . $id);
            echo "Chyba při aktualizaci kategorie.";
        }
    }

    public function delete($id)
    {
        $result = $this->model->delete($id);

        if ($result) {
            @LogHelper::admin('Category deleted', 'ID: ' . $id);
            header("Location: /admin/categories");
            exit;
        } else {
            @LogHelper::admin('Category delete failed', 'ID: ' . $id);
            echo "Chyba při mazání kategorie.";
        }
    }
}
