<?php

namespace App\Helpers;

class FlashMessageHelper
{
    /**
     * Zobrazí flash zprávu s jednotným stylingem
     * 
     * @param string $type Typ zprávy: 'error', 'success', 'warning', 'info'
     * @param string $message Text zprávy
     * @param bool $autoClose Automaticky zavřít po určité době
     * @return string HTML kód zprávy
     */
    public static function display($type, $message, $autoClose = true)
    {
        if (empty($message)) {
            return '';
        }

        $alertClass = 'alert-' . self::mapTypeToBootstrap($type);
        $icon = self::getIcon($type);
        $closeButton = $autoClose ? '<button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>' : '';

        return '<div class="alert ' . htmlspecialchars($alertClass) . ' alert-dismissible fade show" role="alert">' .
               $icon . ' ' . htmlspecialchars($message) .
               $closeButton .
               '</div>';
    }
    
    private static function mapTypeToBootstrap($type)
    {
        $map = [
            'error' => 'danger',
            'success' => 'success',
            'warning' => 'warning',
            'info' => 'info'
        ];
        
        return $map[$type] ?? 'info';
    }

    /**
     * Zobrazí zprávu ze session, pokud existuje
     * 
     * @param string $sessionKey Klíč v session (např. 'login_error')
     * @param string $type Typ zprávy (default: 'error')
     * @param bool $autoClose Automaticky zavřít
     * @return string HTML kód zprávy nebo prázdný string
     */
    public static function showIfSet($sessionKey, $type = 'error', $autoClose = true)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[$sessionKey])) {
            return '';
        }

        $message = $_SESSION[$sessionKey];
        unset($_SESSION[$sessionKey]);

        return self::display($type, $message, $autoClose);
    }

    /**
     * Vrátí ikonu podle typu zprávy
     * 
     * @param string $type Typ zprávy
     * @return string Ikona
     */
    private static function getIcon($type)
    {
        $icons = [
            'error' => '<i class="fas fa-exclamation-triangle"></i>',
            'success' => '<i class="fas fa-check-circle"></i>',
            'warning' => '<i class="fas fa-exclamation-triangle"></i>',
            'info' => '<i class="fas fa-info-circle"></i>'
        ];

        return $icons[$type] ?? '<i class="fas fa-info-circle"></i>';
    }

    /**
     * Nastaví zprávu do session
     * 
     * @param string $sessionKey Klíč v session
     * @param string $message Text zprávy
     * @return void
     */
    public static function set($sessionKey, $message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[$sessionKey] = $message;
    }
}

