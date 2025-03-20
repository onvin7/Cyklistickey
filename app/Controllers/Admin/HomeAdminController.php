<?php

namespace App\Controllers\Admin;

use App\Models\Article;
use App\Models\Statistics;
use CodeIgniter\HTTP\Response;

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

    /**
     * Zpracování AJAX požadavku pro data grafu
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function chartData()
    {
        // Získání parametrů z URL
        $articlesLimit = $this->request->getGet('articles') ?? 20;
        $daysCount = $this->request->getGet('days') ?? 7;
        
        // Ověření, zda jsou parametry čísla v rozumném rozmezí
        $articlesLimit = (int)$articlesLimit;
        $daysCount = (int)$daysCount;
        
        // Omezení hodnot pro prevenci zneužití
        $articlesLimit = min(max($articlesLimit, 3), 50);
        $daysCount = min(max($daysCount, 3), 60);
        
        // Získání dat z modelu s parametry
        $topArticlesData = $this->statisticsModel->getTopArticlesForPeriod($articlesLimit, $daysCount);
        
        // Vrácení dat ve formátu JSON
        return $this->response->setJSON($topArticlesData);
    }
}
