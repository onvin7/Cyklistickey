<?php

namespace App\Helpers;

class RedirectHelper
{
    /**
     * Permanent redirect (301) - pro SEO a staré odkazy
     * 
     * @param string $url Nová URL kam přesměrovat
     * @param bool $permanent True = 301, False = 302
     */
    public static function redirect($url, $permanent = true)
    {
        $statusCode = $permanent ? 301 : 302;
        
        // Ensure URL starts with /
        if (!str_starts_with($url, 'http') && !str_starts_with($url, '/')) {
            $url = '/' . $url;
        }
        
        // Log redirect for debugging
        if (function_exists('error_log')) {
            error_log("Redirect ($statusCode): " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . " -> $url");
        }
        
        header("Location: $url", true, $statusCode);
        exit;
    }
    
    /**
     * 301 Permanent redirect (pro zachování SEO)
     */
    public static function permanent($url)
    {
        self::redirect($url, true);
    }
    
    /**
     * 302 Temporary redirect
     */
    public static function temporary($url)
    {
        self::redirect($url, false);
    }
    
    /**
     * Mapování starých URL na nové
     * Vrátí novou URL nebo null pokud není redirect potřeba
     * 
     * @param string $oldUrl Stará URL
     * @return string|null Nová URL nebo null
     */
    public static function getNewUrl($oldUrl)
    {
        // Normalizace URL (odstranění leading/trailing slashes)
        $oldUrl = trim($oldUrl, '/');
        
        // Mapování starých URL na nové
        $redirectMap = [
            // Race -> Events
            'race' => '/events',
            'race/' => '/events',
            'race/cyklistickey' => '/events',
            'race/cyklistickey/' => '/events',
            'race/bezeckey' => '/events',
            'race/bezeckey/' => '/events',
            
            // Staré článkové URL (pokud byly)
            // 'clanky' => '/articles',
            // 'clanek' => '/article',
            
            // Přidat další staré URL podle potřeby
        ];
        
        return $redirectMap[$oldUrl] ?? null;
    }
    
    /**
     * Automatický redirect pokud existuje mapování
     * Zavolat na začátku routingu
     * 
     * @param string $requestUri Aktuální REQUEST_URI
     * @return bool True pokud byl proveden redirect, False pokud ne
     */
    public static function handleOldUrls($requestUri = null)
    {
        $requestUri = $requestUri ?? $_SERVER['REQUEST_URI'] ?? '';
        
        // Odstranění query stringu
        $path = parse_url($requestUri, PHP_URL_PATH);
        
        // Kontrola mapování
        $newUrl = self::getNewUrl($path);
        
        if ($newUrl !== null) {
            self::permanent($newUrl);
            return true;
        }
        
        return false;
    }
}

