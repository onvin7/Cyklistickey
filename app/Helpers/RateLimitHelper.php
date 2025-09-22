<?php

namespace App\Helpers;

class RateLimitHelper
{
    private static $cacheDir = __DIR__ . '/../../web/cache/rate_limit/';
    
    /**
     * Zkontroluje rate limit pro IP adresu
     */
    public static function checkRateLimit($ip, $action = 'default', $maxRequests = 60, $timeWindow = 3600)
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
        
        $cacheFile = self::$cacheDir . md5($ip . '_' . $action) . '.json';
        $now = time();
        
        // Načti existující data
        $data = [];
        if (file_exists($cacheFile)) {
            $content = file_get_contents($cacheFile);
            $data = json_decode($content, true) ?: [];
        }
        
        // Vyčisti staré záznamy
        $data = array_filter($data, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Zkontroluj limit
        if (count($data) >= $maxRequests) {
            return false;
        }
        
        // Přidej nový požadavek
        $data[] = $now;
        
        // Ulož data
        file_put_contents($cacheFile, json_encode($data));
        
        return true;
    }
    
    /**
     * Získá zbývající počet požadavků
     */
    public static function getRemainingRequests($ip, $action = 'default', $maxRequests = 60, $timeWindow = 3600)
    {
        if (!is_dir(self::$cacheDir)) {
            return $maxRequests;
        }
        
        $cacheFile = self::$cacheDir . md5($ip . '_' . $action) . '.json';
        $now = time();
        
        if (!file_exists($cacheFile)) {
            return $maxRequests;
        }
        
        $content = file_get_contents($cacheFile);
        $data = json_decode($content, true) ?: [];
        
        // Vyčisti staré záznamy
        $data = array_filter($data, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        return max(0, $maxRequests - count($data));
    }
    
    /**
     * Získá čas do resetu
     */
    public static function getResetTime($ip, $action = 'default', $timeWindow = 3600)
    {
        if (!is_dir(self::$cacheDir)) {
            return 0;
        }
        
        $cacheFile = self::$cacheDir . md5($ip . '_' . $action) . '.json';
        
        if (!file_exists($cacheFile)) {
            return 0;
        }
        
        $content = file_get_contents($cacheFile);
        $data = json_decode($content, true) ?: [];
        
        if (empty($data)) {
            return 0;
        }
        
        $oldestRequest = min($data);
        return max(0, ($oldestRequest + $timeWindow) - time());
    }
    
    /**
     * Zkontroluje rate limit a vrátí HTTP hlavičky
     */
    public static function checkAndSetHeaders($ip, $action = 'default', $maxRequests = 60, $timeWindow = 3600)
    {
        $remaining = self::getRemainingRequests($ip, $action, $maxRequests, $timeWindow);
        $resetTime = self::getResetTime($ip, $action, $timeWindow);
        
        // Nastav HTTP hlavičky
        header("X-RateLimit-Limit: $maxRequests");
        header("X-RateLimit-Remaining: $remaining");
        header("X-RateLimit-Reset: " . (time() + $resetTime));
        
        if ($remaining <= 0) {
            header("Retry-After: $resetTime");
            http_response_code(429);
            return false;
        }
        
        return self::checkRateLimit($ip, $action, $maxRequests, $timeWindow);
    }
    
    /**
     * Vyčistí staré rate limit soubory
     */
    public static function cleanup()
    {
        if (!is_dir(self::$cacheDir)) {
            return;
        }
        
        $files = glob(self::$cacheDir . '*.json');
        $now = time();
        
        foreach ($files as $file) {
            if (filemtime($file) < ($now - 86400)) { // Starší než 24 hodin
                unlink($file);
            }
        }
    }
}
