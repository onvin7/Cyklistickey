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

        $typeClass = 'flash-' . $type;
        $icon = self::getIcon($type);
        $closeButton = $autoClose ? '<button type="button" class="flash-close" onclick="this.parentElement.remove()">&times;</button>' : '';

        return '<div class="flash-message ' . htmlspecialchars($typeClass) . '">' .
               '<span class="flash-icon">' . $icon . '</span>' .
               '<span class="flash-text">' . htmlspecialchars($message) . '</span>' .
               $closeButton .
               '</div>';
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
            'error' => '⚠️',
            'success' => '✓',
            'warning' => '⚠',
            'info' => 'ℹ'
        ];

        return $icons[$type] ?? '•';
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

