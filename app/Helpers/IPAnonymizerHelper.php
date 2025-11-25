<?php

namespace App\Helpers;

class IPAnonymizerHelper
{
    /**
     * Anonymizuje IP adresu pro GDPR kompatibilitu
     * IPv4: anonymizuje poslední oktet (192.168.1.123 → 192.168.1.0)
     * IPv6: anonymizuje posledních 80 bitů (poslední 5 skupin) → 2001:0db8:85a3::
     * 
     * @param string|null $ip IP adresa k anonymizaci
     * @return string|null Anonymizovaná IP adresa nebo null pro neplatnou IP
     */
    public static function anonymizeIP(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        // Validace IP adresy
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        // Rozlišení IPv4 a IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return self::anonymizeIPv4($ip);
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return self::anonymizeIPv6($ip);
        }

        return null;
    }

    /**
     * Anonymizuje IPv4 adresu - nastaví poslední oktet na 0
     * 
     * @param string $ip IPv4 adresa
     * @return string Anonymizovaná IPv4 adresa
     */
    private static function anonymizeIPv4(string $ip): string
    {
        $octets = explode('.', $ip);
        
        // Pokud nemáme 4 oktety, vrátíme původní IP
        if (count($octets) !== 4) {
            return $ip;
        }

        // Nastavíme poslední oktet na 0
        $octets[3] = '0';
        
        return implode('.', $octets);
    }

    /**
     * Anonymizuje IPv6 adresu - nastaví posledních 80 bitů na 0
     * Posledních 80 bitů = posledních 5 hexadecimálních skupin (5 * 16 bitů = 80 bitů)
     * 
     * @param string $ip IPv6 adresa
     * @return string Anonymizovaná IPv6 adresa
     */
    private static function anonymizeIPv6(string $ip): string
    {
        // Rozšíření IPv6 zkrácené notace na plnou formu
        $expanded = self::expandIPv6($ip);
        
        if (!$expanded) {
            return $ip;
        }

        // Rozdělení na skupiny
        $groups = explode(':', $expanded);
        
        // Musíme mít 8 skupin
        if (count($groups) !== 8) {
            return $ip;
        }

        // Nastavíme posledních 5 skupin na 0 (80 bitů = 5 * 16 bitů)
        for ($i = 3; $i < 8; $i++) {
            $groups[$i] = '0000';
        }

        // Složení zpět
        $anonymized = implode(':', $groups);

        // Zkrácení podle standardní IPv6 notace (odstranění vedoucích nul v skupinách a zkrácení ::)
        return self::compressIPv6($anonymized);
    }

    /**
     * Rozšíří IPv6 adresu ze zkrácené notace na plnou formu
     * 
     * @param string $ip IPv6 adresa
     * @return string|false Rozšířená IPv6 adresa nebo false při chybě
     */
    private static function expandIPv6(string $ip): string|false
    {
        // Zpracování IPv4-mapped IPv6 adres (::ffff:192.168.1.1)
        if (strpos($ip, '.') !== false) {
            // Nahradíme IPv4 část IPv6 notací
            $parts = explode('::', $ip);
            if (count($parts) === 2 && strpos($parts[1], '.') !== false) {
                $ipv4 = $parts[1];
                $octets = explode('.', $ipv4);
                if (count($octets) === 4) {
                    $ipv6Part = sprintf('%02x%02x:%02x%02x', $octets[0], $octets[1], $octets[2], $octets[3]);
                    $ip = $parts[0] . '::' . $ipv6Part;
                }
            }
        }

        // Pokud IP obsahuje ::, rozšíříme ji
        if (strpos($ip, '::') !== false) {
            // Počet skupin v IP
            $groups = explode(':', $ip);
            $groupCount = count(array_filter($groups, fn($g) => $g !== ''));
            
            // Počet skupin, které chybí (8 - aktuální počet)
            $missing = 8 - $groupCount;
            
            // Nahradíme :: za chybějící skupiny
            $ip = str_replace('::', str_repeat(':0000', $missing) . ':', $ip);
            
            // Odstraníme duplicitní dvojtečky
            while (strpos($ip, '::') !== false) {
                $ip = str_replace('::', ':', $ip);
            }
            
            // Odstraníme vedoucí a koncové dvojtečky
            $ip = trim($ip, ':');
        }

        // Rozdělíme na skupiny
        $groups = explode(':', $ip);
        
        // Rozšíříme každou skupinu na 4 znaky (doplnění nul zleva)
        $expanded = [];
        foreach ($groups as $group) {
            $expanded[] = str_pad($group, 4, '0', STR_PAD_LEFT);
        }

        // Pokud nemáme 8 skupin, přidáme chybějící nuly
        while (count($expanded) < 8) {
            $expanded[] = '0000';
        }

        return implode(':', $expanded);
    }

    /**
     * Zkrátí IPv6 adresu podle standardní notace
     * Odstraní vedoucí nuly v skupinách a zkrátí nejdelší sekvenci nul na ::
     * 
     * @param string $ip IPv6 adresa v plné formě
     * @return string Zkrácená IPv6 adresa
     */
    private static function compressIPv6(string $ip): string
    {
        $groups = explode(':', $ip);
        
        // Odstranění vedoucích nul v každé skupině
        $compressed = array_map(function($group) {
            return ltrim($group, '0');
        }, $groups);
        
        // Pokud je skupina prázdná, nahradíme ji jednou nulou
        $compressed = array_map(function($group) {
            return empty($group) ? '0' : $group;
        }, $compressed);

        // Najdeme nejdelší sekvenci nul (minimálně 2 skupiny)
        $longestZeroStart = -1;
        $longestZeroLength = 0;
        $currentZeroStart = -1;
        $currentZeroLength = 0;

        for ($i = 0; $i < count($compressed); $i++) {
            if ($compressed[$i] === '0') {
                if ($currentZeroStart === -1) {
                    $currentZeroStart = $i;
                    $currentZeroLength = 1;
                } else {
                    $currentZeroLength++;
                }
            } else {
                if ($currentZeroLength > $longestZeroLength && $currentZeroLength >= 2) {
                    $longestZeroStart = $currentZeroStart;
                    $longestZeroLength = $currentZeroLength;
                }
                $currentZeroStart = -1;
                $currentZeroLength = 0;
            }
        }

        // Zkontrolujeme i na konci
        if ($currentZeroLength > $longestZeroLength && $currentZeroLength >= 2) {
            $longestZeroStart = $currentZeroStart;
            $longestZeroLength = $currentZeroLength;
        }

        // Nahradíme nejdelší sekvenci nul za ::
        if ($longestZeroStart !== -1 && $longestZeroLength >= 2) {
            $before = array_slice($compressed, 0, $longestZeroStart);
            $after = array_slice($compressed, $longestZeroStart + $longestZeroLength);
            
            $result = [];
            if (!empty($before)) {
                $result = array_merge($result, $before);
            }
            $result[] = '';
            if (!empty($after)) {
                $result = array_merge($result, $after);
            }
            
            $compressed = $result;
        }

        // Složení zpět
        $result = implode(':', $compressed);
        
        // Pokud začíná nebo končí dvojtečkou, upravíme
        if (strpos($result, '::') === 0) {
            $result = ':' . $result;
        } elseif (substr($result, -2) === '::') {
            $result = $result . ':';
        }

        return $result;
    }
}


