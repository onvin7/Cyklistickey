<?php

namespace App\Controllers\Admin;

use App\Models\Article;
use App\Models\Statistics;

class HomeAdminController
{
    private $db;
    private $articleModel;
    private $statisticsModel;

    public function __construct($db)
    {
        $this->db = $db; // Připojení k databázi
        $this->articleModel = new Article($db); // Inicializace modelu článků
        $this->statisticsModel = new Statistics($db); // Inicializace modelu statistik
    }

    // Metoda pro zobrazení hlavní stránky admin panelu
    public function index()
    {
        $latestArticles = $this->articleModel->getAllAdmin(5); // 5 nejnovějších článků
        
        // Druhý výpis - články z posledních 7 dnů
        $lastWeekArticles = $this->articleModel->getLastWeekArticles();
        
        // Třetí výpis - 20 nejčtenějších článků za posledních 7 dní s denními daty pro ApexCharts
        $topArticlesData = $this->statisticsModel->getTopArticlesForPeriod(20, 7);

        $view = '../app/Views/Admin/home/index.php';
        include '../app/Views/Admin/layout/base.php';
    }
}
