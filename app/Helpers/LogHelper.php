<?php

namespace App\Helpers;

class LogHelper
{
    /**
     * Získá cestu k log souboru
     */
    private static function getLogPath($logFileName)
    {
        // Cesta k log souborům - logs/ může být v rootu projektu nebo o úroveň výš
        // app/Helpers -> app -> root -> logs/
        // NEBO app/Helpers -> app -> root -> .. -> logs/ (subdom/logs/)
        try {
            $helperDir = realpath(__DIR__);
            if (!$helperDir) {
                return null;
            }
            
            $rootPath = dirname(dirname(dirname($helperDir)));
            
            // Zkusíme různé možné cesty - nejdřív o úroveň výš (subdom/logs/), pak v rootu (bicenc/logs/)
            $possiblePaths = [
                dirname($rootPath) . '/logs/',  // logs/ o úroveň výš (subdom/logs/) - PRIORITA
                $rootPath . '/logs/',           // logs/ v rootu projektu (bicenc/logs/)
            ];
            
            $logsDir = null;
            foreach ($possiblePaths as $path) {
                $realPath = @realpath($path);
                if ($realPath && is_dir($realPath)) {
                    $logsDir = $realPath . '/';
                    break;
                }
            }
            
            // Pokud žádná cesta neexistuje, použijeme první (subdom/logs/) a vytvoříme ji
            if (!$logsDir) {
                $logsDir = $possiblePaths[0];
                if (!is_dir($logsDir)) {
                    @mkdir($logsDir, 0755, true);
                }
                $logsDir = @realpath($logsDir) ? @realpath($logsDir) . '/' : $logsDir;
            }
            
            return $logsDir . $logFileName;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Zapíše zprávu do log souboru
     */
    public static function write($logFileName, $message)
    {
        try {
            $logPath = self::getLogPath($logFileName);
            if (!$logPath) {
                return; // Pokud nemůžeme zjistit cestu, prostě nebudeme logovat
            }
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = $timestamp . " - " . $message . "\n";
            @file_put_contents($logPath, $logMessage, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // Tichá chyba - nechceme přerušit běh aplikace kvůli logování
            // error_log("LogHelper error: " . $e->getMessage());
        }
    }

    /**
     * Zapíše admin operaci do admin.log
     */
    public static function admin($action, $details = '')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? 'unknown';
        $userEmail = $_SESSION['email'] ?? 'unknown';
        $message = "User ID: {$userId} ({$userEmail}) - Action: {$action}";
        
        if (!empty($details)) {
            $message .= " - " . $details;
        }

        self::write('admin.log', $message);
    }

    /**
     * Zapíše login pokus do login.log
     */
    public static function login($message)
    {
        self::write('login.log', $message);
    }

    /**
     * Zapíše API požadavek do api.log
     */
    public static function api($message)
    {
        self::write('api.log', $message);
    }
}

