<?php

namespace App\Controllers;

use App\Models\User;

class LoginController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new User($db);
    }

    // Zobrazení přihlašovacího formuláře
    public function showLoginForm()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Pokud je uživatel již přihlášený, přesměruj ho do adminu
        if (isset($_SESSION['user_id'])) {
            echo "<script>window.location.href='/admin';</script>";
            exit();
        }

        $disableNavbar = true;
        $disableBootstrap = true;
        $css = ['login'];
        $adminTitle = "Přihlášení | Admin Panel - Cyklistickey magazín";
        $view = '../app/Views/login.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Přihlášení uživatele
    public function login($email, $password)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $this->model->getByEmail($email);

        if (!$user) {
            echo "<script>alert('Uživatel neexistuje!'); window.location.href='/login';</script>";
            exit();
        }

        if (!password_verify($password, $user['heslo'])) {
            echo "<script>alert('Špatné heslo!'); window.location.href='/login';</script>";
            exit();
        }

        // Kontrola role - pouze uživatelé s rolí > 0 mají přístup do administrace
        if ($user['role'] <= 0) {
            echo "<script>alert('Nemáte oprávnění pro přístup do administrace!'); window.location.href='/login';</script>";
            exit();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profil_foto'] = $user['profil_foto'];

        echo "<script>window.location.href='/admin';</script>";
        exit();
    }

    // Odhlášení uživatele
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /login');
        exit();
    }

    // Zobrazení registračního formuláře
    public function create()
    {
        $disableNavbar = true;
        $disableBootstrap = true;
        $css = ['login'];
        $adminTitle = "Registrace | Admin Panel - Cyklistickey magazín";
        
        $view = '../app/Views/Admin/users/create.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Zpracování registrace
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $data = [
            'email' => trim($_POST['email']),
            'heslo' => trim($_POST['heslo']),
            'confirm_heslo' => trim($_POST['confirm_heslo']),
            'role' => 0, // Výchozí role uživatele
            'name' => trim($_POST['name']),
            'surname' => trim($_POST['surname']),
        ];

        // Validace povinných polí
        if (empty($data['email']) || empty($data['heslo']) || empty($data['confirm_heslo']) || empty($data['name']) || empty($data['surname'])) {
            $_SESSION['registration_error'] = 'Vyplňte všechna povinná pole.';
            header('Location: /register');
            exit;
        }

        // Ověření shody hesel
        if ($data['heslo'] !== $data['confirm_heslo']) {
            $_SESSION['registration_error'] = 'Hesla se neshodují.';
            header('Location: /register');
            exit;
        }

        // Kontrola, zda e-mail již existuje
        if ($this->model->checkEmailExists($data['email'])) {
            $_SESSION['registration_error'] = 'Účet s tímto e-mailem již existuje.';
            header('Location: /register');
            exit;
        }

        // Uložení uživatele do databáze
        if ($this->model->createUser($data)) {
            $_SESSION['login_success'] = 'Registrace byla úspěšná.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['registration_error'] = 'Chyba při registraci. Zkuste to znovu.';
            header('Location: /register');
            exit;
        }
    }

    // Zobrazení formuláře pro reset hesla
    public function reset()
    {
        $disableNavbar = true;
        $disableBootstrap = true;
        $css = ['login'];
        $adminTitle = "Reset hesla | Admin Panel - Cyklistickey magazín";
        
        $view = '../app/Views/Admin/users/reset_password.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Uloží token a zapíše do logu
    public function resetPassword()
    {
        $email = trim($_POST['email']);
        
        error_log("DEBUG: Začínám reset hesla pro email: " . $email);
        
        $user = $this->model->getByEmail($email);

        if (!$user) {
            error_log("ERROR: Uživatel s emailem " . $email . " neexistuje");
            echo "<script>alert('Účet s tímto e-mailem neexistuje.'); window.location.href='/reset-password';</script>";
            return;
        }

        error_log("DEBUG: Uživatel nalezen, ID: " . $user['id']);

        // Vygenerujeme token a čas expirace
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        error_log("DEBUG: Vygenerován token: " . $token . ", expirace: " . $expiresAt);

        // Nejprve zkontrolujeme, zda token byl úspěšně uložen do databáze
        $tokenSaved = $this->model->storeResetToken($user['id'], $email, $token, $expiresAt);
        
        if (!$tokenSaved) {
            error_log("ERROR: Chyba při ukládání tokenu do databáze");
            echo "<script>alert('Chyba při ukládání tokenu do databáze.'); window.location.href='/reset-password';</script>";
            return;
        }
        
        // Pro účely ladění vypíšeme informace do error_log
        error_log("DEBUG: Token úspěšně uložen do DB: " . $token . " pro uživatele ID: " . $user['id'] . ", email: " . $email);
        
        // Nyní, když víme, že token byl uložen, vytvoříme odkaz
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password?token=" . urlencode($token);
        
        error_log("DEBUG: Vytvořen reset link: " . $resetLink);

        // Místo echo HTML stránky, uložíme odkaz do session a přesměrujeme
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['reset_link'] = $resetLink;
        
        // Přesměrování na novou stránku, která zobrazí resetovací odkaz
        header('Location: /reset-password/link');
        exit;
    }

    // Zobrazení stránky s odkazem pro reset hesla
    public function showResetLink()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['reset_link'])) {
            header('Location: /reset-password');
            exit;
        }
        
        $resetLink = $_SESSION['reset_link'];
        unset($_SESSION['reset_link']); // Smažeme link ze session po zobrazení
        
        $disableNavbar = true;
        $disableBootstrap = true;
        $css = ['login'];
        $adminTitle = "Odkaz pro reset hesla | Admin Panel - Cyklistickey magazín";
        
        $view = '../app/Views/Admin/users/reset_link.php';
        include '../app/Views/Admin/layout/base.php';
    }

    // Zobrazení formuláře pro nastavení nového hesla
    public function confirmResetPassword()
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            header('Location: /reset-password');
            exit;
        }
        
        // Ověření platnosti tokenu
        $resetInfo = $this->model->getValidResetToken($token);
        
        if (!$resetInfo) {
            echo "<script>alert('Neplatný nebo expirovaný token pro reset hesla.'); window.location.href='/reset-password';</script>";
            exit;
        }
        
        $disableNavbar = true;
        $disableBootstrap = true;
        $css = ['login'];
        $adminTitle = "Nastavení nového hesla | Admin Panel - Cyklistickey magazín";
        
        $view = '../app/Views/Admin/users/new_password.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function saveNewPassword()
    {
        $token = $_POST['token'] ?? null;
        $newPassword = $_POST['new_password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;

        // Kontrola přítomnosti povinných údajů
        if (empty($token)) {
            error_log("ERROR: Chybí token v požadavku pro reset hesla.");
            echo "<script>alert('Chybí token pro změnu hesla.'); window.location.href='/reset-password';</script>";
            return;
        }

        if (empty($newPassword) || empty($confirmPassword)) {
            error_log("ERROR: Chybí heslo v požadavku pro reset hesla. Token: " . $token);
            echo "<script>alert('Prosím vyplňte obě pole s heslem.'); window.history.back();</script>";
            return;
        }

        if ($newPassword !== $confirmPassword) {
            error_log("ERROR: Hesla se neshodují při resetu hesla. Token: " . $token);
            echo "<script>alert('Hesla se neshodují.'); window.history.back();</script>";
            return;
        }

        // Získání dat o resetu hesla
        $resetData = $this->model->getValidResetToken($token);
        
        if (!$resetData) {
            error_log("ERROR: Token pro reset hesla nebyl nalezen v databázi nebo expiroval: " . $token);
            echo "<script>alert('Token je neplatný nebo expirovaný.'); window.location.href='/reset-password';</script>";
            return;
        }

        // Kontrola, zda e-mail existuje v DB
        $user = $this->model->getByEmail($resetData['email']);
        if (!$user) {
            error_log("ERROR: Uživatel s emailem " . $resetData['email'] . " nebyl nalezen.");
            echo "<script>alert('Účet s tímto e-mailem neexistuje.'); window.location.href='/reset-password';</script>";
            return;
        }

        // Aktualizace hesla podle user_id
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $this->model->updatePassword($user['id'], $hashedPassword);
        
        if ($updated) {
            // Smazání použitého tokenu
            $this->model->deleteResetToken($token);
            error_log("SUCCESS: Heslo bylo úspěšně změněno pro uživatele ID: " . $user['id']);
            echo "<script>alert('Heslo bylo úspěšně změněno.'); window.location.href='/login';</script>";
        } else {
            error_log("ERROR: Chyba při změně hesla pro uživatele ID: " . $user['id']);
            echo "<script>alert('Chyba při změně hesla.'); window.location.href='/reset-password';</script>";
        }
    }
}
