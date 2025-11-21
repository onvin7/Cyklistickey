<?php

namespace App\Helpers;

class UserAgentHelper
{
    /**
     * Detekuje typ zařízení z User Agent stringu
     */
    public static function detectDeviceType($userAgent)
    {
        if (empty($userAgent)) {
            return 'unknown';
        }

        $userAgent = strtolower($userAgent);

        // Detekce botů
        $bots = ['bot', 'crawler', 'spider', 'scraper', 'facebookexternalhit', 'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver'];
        foreach ($bots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return 'bot';
            }
        }

        // Detekce tabletů
        $tablets = ['ipad', 'tablet', 'playbook', 'silk', 'kindle'];
        foreach ($tablets as $tablet) {
            if (strpos($userAgent, $tablet) !== false) {
                return 'tablet';
            }
        }

        // Detekce mobilních zařízení
        $mobile = ['mobile', 'android', 'iphone', 'ipod', 'blackberry', 'opera mini', 'windows phone', 'windows mobile', 'palm', 'hiptop', 'avantgo', 'plucker', 'xiino', 'blazer', 'elaine', 'windows ce', 'smartphone', 'iemobile'];
        foreach ($mobile as $mob) {
            if (strpos($userAgent, $mob) !== false) {
                return 'mobile';
            }
        }

        return 'desktop';
    }

    /**
     * Detekuje prohlížeč z User Agent stringu
     */
    public static function detectBrowser($userAgent)
    {
        if (empty($userAgent)) {
            return null;
        }

        $userAgent = strtolower($userAgent);

        $browsers = [
            'chrome' => ['chrome', 'crios'],
            'firefox' => ['firefox', 'fxios'],
            'safari' => ['safari'],
            'edge' => ['edg', 'edge'],
            'opera' => ['opera', 'opr', 'opios'],
            'ie' => ['msie', 'trident'],
            'samsung' => ['samsungbrowser'],
            'uc browser' => ['ucbrowser'],
            'yandex' => ['yabrowser'],
        ];

        foreach ($browsers as $browser => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($userAgent, $pattern) !== false) {
                    // Speciální ošetření pro Safari (musí být bez Chrome)
                    if ($browser === 'safari' && strpos($userAgent, 'chrome') !== false) {
                        continue;
                    }
                    // Speciální ošetření pro IE (Trident)
                    if ($browser === 'ie' && strpos($userAgent, 'trident') !== false) {
                        return 'Internet Explorer';
                    }
                    return ucfirst($browser);
                }
            }
        }

        return 'Unknown';
    }

    /**
     * Detekuje operační systém z User Agent stringu
     */
    public static function detectOS($userAgent)
    {
        if (empty($userAgent)) {
            return null;
        }

        $userAgent = strtolower($userAgent);

        $oses = [
            'Windows' => ['windows nt', 'win64', 'wow64'],
            'macOS' => ['mac os x', 'macintosh'],
            'iOS' => ['iphone os', 'ipad', 'ipod'],
            'Android' => ['android'],
            'Linux' => ['linux', 'x11'],
            'Unix' => ['unix'],
            'Chrome OS' => ['cros'],
            'Windows Phone' => ['windows phone'],
            'BlackBerry' => ['blackberry'],
        ];

        foreach ($oses as $os => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($userAgent, $pattern) !== false) {
                    // Speciální ošetření pro Windows verze
                    if ($os === 'Windows') {
                        if (preg_match('/windows nt (\d+\.\d+)/', $userAgent, $matches)) {
                            $version = (float)$matches[1];
                            if ($version >= 10.0) {
                                return 'Windows 10/11';
                            } elseif ($version >= 6.3) {
                                return 'Windows 8.1';
                            } elseif ($version >= 6.2) {
                                return 'Windows 8';
                            } elseif ($version >= 6.1) {
                                return 'Windows 7';
                            } elseif ($version >= 6.0) {
                                return 'Windows Vista';
                            }
                        }
                        return 'Windows';
                    }
                    // Speciální ošetření pro macOS verze
                    if ($os === 'macOS' && preg_match('/mac os x (\d+)[._](\d+)/', $userAgent, $matches)) {
                        return 'macOS ' . $matches[1] . '.' . $matches[2];
                    }
                    // Speciální ošetření pro iOS verze
                    if ($os === 'iOS' && preg_match('/os (\d+)[._](\d+)/', $userAgent, $matches)) {
                        return 'iOS ' . $matches[1] . '.' . $matches[2];
                    }
                    // Speciální ošetření pro Android verze
                    if ($os === 'Android' && preg_match('/android (\d+\.\d+)/', $userAgent, $matches)) {
                        return 'Android ' . $matches[1];
                    }
                    return $os;
                }
            }
        }

        return 'Unknown';
    }

    /**
     * Získá IP adresu uživatele (s ohledem na proxy)
     */
    public static function getClientIP()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
}

