<?php

namespace App\Helpers;

class CSRFHelper
{
    /**
     * Generuje CSRF token
     */
    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Pokud už existuje platný token, vrať ho
        if (isset($_SESSION['csrf_token']) && isset($_SESSION['csrf_token_time'])) {
            // Token je platný 2 hodiny
            if (time() - $_SESSION['csrf_token_time'] < 7200) {
                return $_SESSION['csrf_token'];
            }
        }
        
        // Jinak vygeneruj nový token
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Validuje CSRF token
     */
    public static function validateToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Token je platný 2 hodiny
        if (time() - $_SESSION['csrf_token_time'] > 7200) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generuje CSRF hidden input
     */
    public static function generateHiddenInput()
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Generuje CSRF meta tag
     */
    public static function generateMetaTag()
    {
        $token = self::generateToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Zkontroluje CSRF token z POST dat
     */
    public static function checkPostToken()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = $_POST['csrf_token'] ?? '';
        
        // Pokud není token v POST, vrať false
        if (empty($token)) {
            error_log("DEBUG CSRF: No token in POST data");
            return false;
        }
        
        // Pokud není token v session, vrať false
        if (!isset($_SESSION['csrf_token'])) {
            error_log("DEBUG CSRF: No token in session");
            return false;
        }
        
        // Porovnej tokeny
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            error_log("DEBUG CSRF: Tokens don't match");
            error_log("DEBUG CSRF: POST token: " . substr($token, 0, 20));
            error_log("DEBUG CSRF: SESSION token: " . substr($_SESSION['csrf_token'], 0, 20));
            return false;
        }
        
        // Zkontroluj expiraci
        if (isset($_SESSION['csrf_token_time'])) {
            $age = time() - $_SESSION['csrf_token_time'];
            if ($age > 7200) { // 2 hodiny
                error_log("DEBUG CSRF: Token expired (age: $age seconds)");
                unset($_SESSION['csrf_token']);
                unset($_SESSION['csrf_token_time']);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Zkontroluje CSRF token z AJAX hlavičky
     */
    public static function checkAjaxToken()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }
        
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!self::validateToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
        
        return true;
    }
}
