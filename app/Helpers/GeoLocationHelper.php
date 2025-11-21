<?php

namespace App\Helpers;

class GeoLocationHelper
{
    /**
     * Získá geolokační informace z IP adresy
     * Používá free API: ip-api.com (max 45 requestů/minutu)
     * 
     * @param string $ip IP adresa
     * @return array|null ['country' => 'CZ', 'city' => 'Prague'] nebo null
     */
    public static function getLocationFromIP($ip)
    {
        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        // Ignoruj lokální IP adresy
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return null;
        }

        try {
            // Použití ip-api.com (free, max 45 req/min)
            $url = "http://ip-api.com/json/{$ip}?fields=status,countryCode,city";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2, // 2 sekundy timeout
                    'method' => 'GET',
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return null;
            }

            $data = json_decode($response, true);
            
            if (isset($data['status']) && $data['status'] === 'success') {
                return [
                    'country' => $data['countryCode'] ?? null,
                    'city' => $data['city'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Tichá chyba - geolokace není kritická
            error_log("GeoLocation error: " . $e->getMessage());
        }

        return null;
    }
}

