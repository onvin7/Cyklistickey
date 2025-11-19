<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\CSRFHelper;
use App\Helpers\LogHelper;

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
        // DEBUG LOGY ZAKOMENTOVÁNY - pro debug odkomentovat
        // $possibleLogPaths = [
        //     dirname(dirname(dirname(__DIR__))) . '/logs/debug_test.log',  // bicenc/logs/
        //     dirname(dirname(dirname(dirname(__DIR__)))) . '/logs/debug_test.log',  // subdom/logs/
        // ];
        // 
        // $debugFile = null;
        // foreach ($possibleLogPaths as $path) {
        //     $dir = dirname($path);
        //     if (!is_dir($dir)) {
        //         @mkdir($dir, 0755, true);
        //     }
        //     if (is_writable($dir) || is_writable($path)) {
        //         $debugFile = $path;
        //         break;
        //     }
        // }
        // 
        // if (!$debugFile) {
        //     $debugFile = $possibleLogPaths[1]; // subdom/logs/ jako priorita
        //     @mkdir(dirname($debugFile), 0755, true);
        // }
        // 
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN METHOD CALLED - Email: " . ($email ?? 'NULL') . ", Password: " . (!empty($password) ? 'SET' : 'EMPTY') . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Debug file path: " . $debugFile . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - __DIR__: " . __DIR__ . "\n", FILE_APPEND);
        
        if (session_status() === PHP_SESSION_NONE) {
            // Zajistit, aby se používala stejná session cookie - PŘED session_start()
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params(
                $cookieParams['lifetime'],
                $cookieParams['path'],
                $cookieParams['domain'],
                $cookieParams['secure'],
                $cookieParams['httponly']
            );
            session_start();
            // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Session started, ID: " . session_id() . "\n", FILE_APPEND);
        }
        // else {
        //     @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Session already started, ID: " . session_id() . "\n", FILE_APPEND);
        // }

        // Kontrola CSRF tokenu - dočasně vypnuto pro debug
        // if (!CSRFHelper::checkPostToken()) {
        //     $_SESSION['login_error'] = 'CSRF token validation failed!';
        //     header('Location: /login');
        //     exit();
        // }

        if (empty($email) || empty($password)) {
            @LogHelper::login("Login failed - Empty email or password - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $_SESSION['login_error'] = 'Vyplňte email i heslo!';
            header('Location: /login');
            exit();
        }

        error_log("Looking up user with email: " . $email);
        $user = $this->model->getByEmail($email);

        if (!$user) {
            @LogHelper::login("Login failed - User not found - Email: " . $email . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $_SESSION['login_error'] = 'Uživatel neexistuje!';
            header('Location: /login');
            exit();
        }

        error_log("User found - ID: " . $user['id'] . ", Role: " . $user['role']);
        error_log("Verifying password...");

        if (!password_verify($password, $user['heslo'])) {
            @LogHelper::login("Login failed - Wrong password - Email: " . $email . ", User ID: " . $user['id'] . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $_SESSION['login_error'] = 'Špatné heslo!';
            header('Location: /login');
            exit();  
        }

        error_log("Password verified successfully");

        // Kontrola role - pouze uživatelé s rolí > 0 mají přístup do administrace
        if ($user['role'] <= 0) {
            @LogHelper::login("Login failed - Insufficient permissions - Email: " . $email . ", User ID: " . $user['id'] . ", Role: " . $user['role'] . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $_SESSION['login_error'] = 'Nemáte oprávnění pro přístup do administrace!';
            header('Location: /login');
            exit();
        }

        error_log("Setting session variables...");
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profil_foto'] = $user['profil_foto'];
        error_log("Session variables set - User ID: " . $_SESSION['user_id'] . ", Role: " . $_SESSION['role']);
        
        // DEBUG LOGY ZAKOMENTOVÁNY - pro debug odkomentovat
        // $possibleLogPaths = [
        //     dirname(dirname(dirname(__DIR__))) . '/logs/debug_test.log',  // bicenc/logs/
        //     dirname(dirname(dirname(dirname(__DIR__)))) . '/logs/debug_test.log',  // subdom/logs/
        // ];
        // $debugFile = file_exists($possibleLogPaths[1]) ? $possibleLogPaths[1] : $possibleLogPaths[0];
        // 
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Session ID: " . session_id() . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Session variables set - User ID: " . $_SESSION['user_id'] . ", Role: " . $_SESSION['role'] . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - All session keys: " . implode(', ', array_keys($_SESSION ?? [])) . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - About to redirect to /admin\n", FILE_APPEND);

        // Logování úspěšného přihlášení
        @LogHelper::login("Login successful - Email: " . $email . ", User ID: " . $user['id'] . ", Role: " . $user['role'] . ", Name: " . ($user['jmeno'] ?? 'N/A') . " " . ($user['prijmeni'] ?? 'N/A') . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

        error_log("Redirecting to /admin...");
        
        // Zajistit, že session je uložena a cookie je nastavena
        $sessionId = session_id();
        $sessionName = session_name();
        $cookieParams = session_get_cookie_params();
        
        // DEBUG LOGY ZAKOMENTOVÁNY - pro debug odkomentovat
        // $possibleLogPaths = [
        //     dirname(dirname(dirname(__DIR__))) . '/logs/debug_test.log',  // bicenc/logs/
        //     dirname(dirname(dirname(dirname(__DIR__)))) . '/logs/debug_test.log',  // subdom/logs/
        // ];
        // $debugFile = file_exists($possibleLogPaths[1]) ? $possibleLogPaths[1] : $possibleLogPaths[0];
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Redirecting to /admin with session ID: " . $sessionId . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Session name: " . $sessionName . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Cookie params: " . print_r($cookieParams, true) . "\n", FILE_APPEND);
        
        // Vyčistit output buffer PŘED jakoukoli operací s cookie/header
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Uložit session a zavřít ji
        session_write_close();
        
        // Explicitně nastavit cookie PŘED redirectem
        // Musí být před jakýmkoli header() nebo výstupem
        $cookieExpire = $cookieParams['lifetime'] > 0 ? time() + $cookieParams['lifetime'] : 0;
        $cookiePath = $cookieParams['path'] ?: '/';
        // Pro prázdný domain použijeme null, aby se cookie nastavila pro aktuální hostname
        $cookieDomain = $cookieParams['domain'] ?: null;
        $cookieSecure = $cookieParams['secure'] ?: false;
        $cookieHttpOnly = $cookieParams['httponly'] ?: true;
        
        // Nastavit cookie pomocí setcookie() - musí být před header()
        // Použijeme starší syntaxi s explicitními parametry
        $cookieSet = setcookie(
            $sessionName,
            $sessionId,
            $cookieExpire,
            $cookiePath,
            $cookieDomain ?? '', // Pokud je null, použijeme prázdný string
            $cookieSecure,
            $cookieHttpOnly
        );
        
        // Pokud PHP verze >= 7.3, můžeme použít moderní syntaxi s SameSite
        if ($cookieSet && PHP_VERSION_ID >= 70300) {
            // Přepis cookie s SameSite atributem
            $cookieString = $sessionName . '=' . $sessionId;
            $cookieString .= '; Path=' . $cookiePath;
            if ($cookieDomain) {
                $cookieString .= '; Domain=' . $cookieDomain;
            }
            $cookieString .= '; SameSite=Lax';
            if ($cookieHttpOnly) {
                $cookieString .= '; HttpOnly';
            }
            if ($cookieSecure) {
                $cookieString .= '; Secure';
            }
            if ($cookieExpire > 0) {
                $cookieString .= '; Expires=' . gmdate('D, d M Y H:i:s \G\M\T', $cookieExpire);
            }
            header('Set-Cookie: ' . $cookieString, false);
        }
        
        // DEBUG LOGY ZAKOMENTOVÁNY - pro debug odkomentovat
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Cookie set result: " . ($cookieSet ? 'SUCCESS' : 'FAILED') . "\n", FILE_APPEND);
        // @file_put_contents($debugFile, date('Y-m-d H:i:s') . " - LOGIN - Cookie: " . $sessionName . " = " . $sessionId . " (path=" . $cookiePath . ", domain=" . ($cookieDomain ?? 'null->empty') . ")\n", FILE_APPEND);
        
        // Zkusit použít session ID přímo v URL jako fallback
        // Pokud cookie nefunguje, použijeme session ID v URL
        // POZOR: Toto je dočasné řešení, není to bezpečné pro produkci!
        $redirectUrl = '/admin?PHPSESSID=' . $sessionId;
        
        // Použít meta refresh s okamžitým redirectem
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($redirectUrl) . '"><script>window.location.replace("' . htmlspecialchars($redirectUrl) . '");</script></head><body></body></html>';
        exit();
    }

    // Odhlášení uživatele
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? 'unknown';
        $userEmail = $_SESSION['email'] ?? 'unknown';
        $userRole = $_SESSION['role'] ?? 'unknown';
        @LogHelper::login("User logged out - User ID: " . $userId . ", Email: " . $userEmail . ", Role: " . $userRole . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
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
            @LogHelper::login("User registered - Email: " . $data['email'] . ", Name: " . $data['name'] . " " . $data['surname']);
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
        
        @LogHelper::login("Password reset requested - Email: " . $email . ", User ID: " . $user['id']);
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
            @LogHelper::login("Password reset failed - Invalid or expired token");
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
            @LogHelper::login("Password reset completed - User ID: " . $user['id'] . ", Email: " . $resetData['email']);
            error_log("SUCCESS: Heslo bylo úspěšně změněno pro uživatele ID: " . $user['id']);
            echo "<script>alert('Heslo bylo úspěšně změněno.'); window.location.href='/login';</script>";
        } else {
            @LogHelper::login("Password reset failed - Update error for User ID: " . $user['id']);
            error_log("ERROR: Chyba při změně hesla pro uživatele ID: " . $user['id']);
            echo "<script>alert('Chyba při změně hesla.'); window.location.href='/reset-password';</script>";
        }
    }
}
