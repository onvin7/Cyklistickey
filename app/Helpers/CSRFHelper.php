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
            // Token je platný 1 hodinu
            if (time() - $_SESSION['csrf_token_time'] < 3600) {
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
        
        // Token je platný 1 hodinu
        if (time() - $_SESSION['csrf_token_time'] > 3600) {
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
        
        $token = $_POST['csrf_token'] ?? '';
        if (!self::validateToken($token)) {
            return false;
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
