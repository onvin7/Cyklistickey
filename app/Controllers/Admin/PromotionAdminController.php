<?php

namespace App\Controllers\Admin;

use PDO;

class PromotionAdminController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Zobrazení stránky pro přidání propagace
    public function create()
    {
        // Načtení dostupných článků, které ještě nejsou propagované
        $stmt = $this->db->query("SELECT * FROM clanky WHERE id NOT IN (SELECT id_clanku FROM propagace WHERE konec >= NOW()) ORDER BY clanky.datum DESC");
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/Admin/promotions/create.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Uložení nové propagace
    public function store()
    {
        $id_clanku = $_POST['id_clanku'];
        $zacatek = $_POST['zacatek'];
        $konec = $_POST['konec'];
        $user_id = $_SESSION['user_id'];

        // Uloží propagaci do databáze
        $stmt = $this->db->prepare("INSERT INTO propagace (id_clanku, user_id, zacatek, konec) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_clanku, $user_id, $zacatek, $konec]);

        header("Location: /admin/promotions");
        exit();
    }

    // Zobrazení aktuálně propagovaných článků
    public function index()
    {
        $stmt = $this->db->query("SELECT p.*, c.nazev FROM propagace p JOIN clanky c ON p.id_clanku = c.id WHERE p.zacatek <= NOW() AND p.konec >= NOW() ORDER BY p.zacatek DESC");
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/Admin/promotions/index.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Zobrazení budoucích propagací
    public function upcoming()
    {
        $stmt = $this->db->query("SELECT p.*, c.nazev FROM propagace p JOIN clanky c ON p.id_clanku = c.id WHERE p.zacatek > NOW() ORDER BY p.zacatek ASC");
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/Admin/promotions/upcoming.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Zobrazení historie propagací
    public function history()
    {
        $stmt = $this->db->query("SELECT p.*, c.nazev FROM propagace p JOIN clanky c ON p.id_clanku = c.id WHERE p.konec < NOW() ORDER BY p.konec DESC");
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = '../app/Views/Admin/promotions/history.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Smazání propagace
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM propagace WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /admin/promotions");
        exit();
    }
}
