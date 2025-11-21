<?php

namespace App\Controllers\Admin;

class LogsAdminController
{
    private $logsDir;

    public function __construct($db)
    {
        // Cesta k log souborům
        $rootPath = dirname(dirname(dirname(__DIR__)));
        $this->logsDir = $rootPath . '/logs/';
    }

    /**
     * Zobrazí seznam všech log souborů
     */
    public function index()
    {
        $logs = [];
        
        if (is_dir($this->logsDir)) {
            $files = scandir($this->logsDir);
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                
                $filePath = $this->logsDir . $file;
                
                if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $logs[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'modified' => filemtime($filePath),
                        'size_formatted' => $this->formatBytes(filesize($filePath))
                    ];
                }
            }
            
            // Seřazení podle data modifikace (nejnovější první)
            usort($logs, function($a, $b) {
                return $b['modified'] <=> $a['modified'];
            });
        }

        $adminTitle = "Logy | Admin Panel - Cyklistickey magazín";
        $view = '../app/Views/Admin/logs/index.php';
        include '../app/Views/Admin/layout/base.php';
    }

    /**
     * Zobrazí obsah konkrétního log souboru
     */
    public function view($logFileName)
    {
        // Bezpečnostní kontrola - povolíme pouze .log soubory
        if (!preg_match('/^[a-zA-Z0-9_-]+\.log$/', $logFileName)) {
            http_response_code(400);
            die('Neplatný název log souboru');
        }

        $logPath = $this->logsDir . $logFileName;

        // Kontrola existence souboru
        if (!file_exists($logPath)) {
            http_response_code(404);
            die('Log soubor nenalezen');
        }

        // Kontrola, že soubor je skutečně v logs/ adresáři (prevence directory traversal)
        $realLogPath = realpath($logPath);
        $realLogsDir = realpath($this->logsDir);
        
        if (!$realLogPath || strpos($realLogPath, $realLogsDir) !== 0) {
            http_response_code(403);
            die('Neplatný přístup k souboru');
        }

        // Načtení obsahu souboru
        $content = file_get_contents($logPath);
        
        // Rozdělení na řádky a obrácení pořadí (nejnovější nahoře)
        $lines = explode("\n", $content);
        $lines = array_reverse($lines);

        // Informace o souboru
        $fileInfo = [
            'name' => $logFileName
        ];

        $adminTitle = "Log: " . htmlspecialchars($logFileName) . " | Admin Panel - Cyklistickey magazín";
        $view = '../app/Views/Admin/logs/view.php';
        include '../app/Views/Admin/layout/base.php';
    }

    /**
     * Parsuje log soubor do jednotlivých událostí
     */
    private function parseLogFile($content, $logFileName)
    {
        $events = [];
        $lines = explode("\n", $content);
        
        // Pro error.log - Apache error log formát
        if (strpos($logFileName, 'error') !== false) {
            $currentEvent = null;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                
                // Nová chyba začíná hranatou závorkou s datem
                if (preg_match('/^\[([^\]]+)\]\s+\[([^\]]+)\]\s+\[([^\]]+)\]\s+\[([^\]]+)\]\s+(.+)$/', $line, $matches)) {
                    // Uložit předchozí event
                    if ($currentEvent !== null) {
                        $events[] = $currentEvent;
                    }
                    
                    // Nový event
                    $currentEvent = [
                        'type' => $this->detectEventType($matches[5]),
                        'date' => $matches[1],
                        'level' => $matches[2],
                        'pid' => $matches[3],
                        'client' => $matches[4],
                        'message' => $matches[5],
                        'stack_trace' => [],
                        'raw' => $line
                    ];
                } elseif ($currentEvent !== null) {
                    // Pokračování stack trace
                    if (strpos($line, '#') === 0 || strpos($line, 'Stack trace:') !== false || strpos($line, 'thrown in') !== false) {
                        $currentEvent['stack_trace'][] = $line;
                    } else {
                        $currentEvent['message'] .= "\n" . $line;
                    }
                }
            }
            
            // Přidat poslední event
            if ($currentEvent !== null) {
                $events[] = $currentEvent;
            }
        }
        // Pro access.log - Apache access log formát
        elseif (strpos($logFileName, 'access') !== false) {
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                
                // Apache access log formát: IP - - [datum] "metoda URL verze" status velikost
                if (preg_match('/^(\S+)\s+-\s+-\s+\[([^\]]+)\]\s+"(\S+)\s+(\S+)\s+([^"]+)"\s+(\d+)\s+(\d+)(?:\s+"([^"]+)"\s+"([^"]+)")?/', $line, $matches)) {
                    $events[] = [
                        'type' => 'access',
                        'ip' => $matches[1],
                        'date' => $matches[2],
                        'method' => $matches[3],
                        'url' => $matches[4],
                        'protocol' => $matches[5],
                        'status' => (int)$matches[6],
                        'size' => (int)$matches[7],
                        'referer' => $matches[8] ?? '',
                        'user_agent' => $matches[9] ?? '',
                        'raw' => $line
                    ];
                }
            }
        }
        // Pro ostatní logy (admin.log, login.log, api.log) - vlastní formát
        else {
            $currentEvent = null;
            $eventBuffer = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                
                // Pokus o detekci nové události (datum na začátku)
                if (preg_match('/^(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $line, $dateMatch)) {
                    // Uložit předchozí event
                    if ($currentEvent !== null) {
                        $events[] = $currentEvent;
                    }
                    
                    // Nový event
                    $currentEvent = [
                        'type' => $this->detectEventType($line, $logFileName),
                        'date' => $dateMatch[1],
                        'message' => $line,
                        'raw' => $line
                    ];
                    $eventBuffer = [$line];
                } elseif ($currentEvent !== null) {
                    // Pokračování eventu
                    $currentEvent['message'] .= "\n" . $line;
                    $eventBuffer[] = $line;
                } else {
                    // Event bez data
                    $events[] = [
                        'type' => $this->detectEventType($line, $logFileName),
                        'date' => '',
                        'message' => $line,
                        'raw' => $line
                    ];
                }
            }
            
            // Přidat poslední event
            if ($currentEvent !== null) {
                $events[] = $currentEvent;
            }
        }
        
        return $events;
    }

    /**
     * Detekuje typ události
     */
    private function detectEventType($message, $logFileName = '')
    {
        $messageLower = strtolower($message);
        
        // Přihlášení
        if (stripos($message, 'login') !== false || stripos($message, 'přihlášení') !== false || 
            stripos($message, 'logged in') !== false || stripos($logFileName, 'login') !== false) {
            return 'login';
        }
        
        // Odhlášení
        if (stripos($message, 'logout') !== false || stripos($message, 'odhlášení') !== false || 
            stripos($message, 'logged out') !== false) {
            return 'logout';
        }
        
        // Fatal error
        if (stripos($message, 'fatal error') !== false || stripos($message, 'php fatal') !== false) {
            return 'fatal';
        }
        
        // Error
        if (stripos($message, 'error') !== false && stripos($message, 'fatal') === false) {
            return 'error';
        }
        
        // Warning
        if (stripos($message, 'warning') !== false || stripos($message, 'php warn') !== false) {
            return 'warning';
        }
        
        // Notice
        if (stripos($message, 'notice') !== false || stripos($message, 'php notice') !== false) {
            return 'notice';
        }
        
        // Debug
        if (stripos($message, 'debug') !== false) {
            return 'debug';
        }
        
        // Admin operace
        if (stripos($logFileName, 'admin') !== false) {
            return 'admin';
        }
        
        // API
        if (stripos($logFileName, 'api') !== false) {
            return 'api';
        }
        
        // Access
        if (stripos($logFileName, 'access') !== false) {
            return 'access';
        }
        
        return 'info';
    }

    /**
     * Formátuje velikost souboru do čitelného formátu
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

