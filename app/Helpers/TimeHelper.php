<?php

namespace App\Helpers;

class TimeHelper
{
    /**
     * Převádí datum na relativní formát času
     * - Pokud je to méně než hodina: "před X minutami"
     * - Pokud je to více než hodina, ale méně než 12 hodin ve stejný den: "před X hodinami"
     * - Pokud to bylo včera, ale uplynulo méně než 6 hodin od napsání: "před X hodinami"
     * - Pokud je to včera (více než 6 hodin od napsání) nebo předvčerejšek: "před 1 dnem" nebo "před 2 dny"
     * - Jinak: normální formát datumu d. m. Y
     * 
     * @param string $dateString Datum ve formátu, který lze zpracovat strtotime
     * @param bool $showTime Zda zobrazit čas u starších příspěvků
     * @return string Formátovaný relativní čas
     */
    public static function getRelativeTime($dateString, $showTime = false)
    {
        $timestamp = strtotime($dateString);
        $now = time();
        $diff = $now - $timestamp;
        
        // Právě teď (méně než 2 minuty)
        if ($diff < 120) {
            return "právě teď";
        }
        
        // Před méně než hodinou
        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "před " . $minutes . " " . self::getCzechMinutesForm($minutes);
        }

        // Před více než hodinou, ale méně než 6 hodinami od napsání
        if ($diff < 21600) { // 6 hodin = 6 * 3600 = 21600 sekund
            $hours = floor($diff / 3600);
            return "před " . $hours . " " . self::getCzechHoursForm($hours);
        }
        
        // Aktuální den a čas
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $dayBeforeYesterday = date('Y-m-d', strtotime('-2 days'));
        $articleDate = date('Y-m-d', $timestamp);
        
        // Před více než 6 hodinami, ale ve stejný den a méně než 12 hodin
        if ($articleDate === $today && $diff < 43200) { // 12 hodin = 12 * 3600 = 43200 sekund
            $hours = floor($diff / 3600);
            return "před " . $hours . " " . self::getCzechHoursForm($hours);
        }
        
        // Včera (po 6. hodině od napsání) nebo předevčírem
        if ($articleDate === $yesterday) {
            return "před 1 dnem";
        }
        
        if ($articleDate === $dayBeforeYesterday) {
            return "před 2 dny";
        }
        
        // Starší příspěvky
        if ($showTime) {
            return date("d. m. Y H:i", $timestamp);
        } else {
            return date("d. m. Y", $timestamp);
        }
    }
    
    /**
     * Vrací správný český tvar slova "minuta" podle počtu
     * 
     * @param int $minutes Počet minut
     * @return string Správný tvar slova
     */
    private static function getCzechMinutesForm($minutes)
    {
        if ($minutes === 1) {
            return "minutou";
        } elseif ($minutes >= 2 && $minutes <= 4) {
            return "minutami";
        } else {
            return "minutami";
        }
    }
    
    /**
     * Vrací správný český tvar slova "hodina" podle počtu
     * 
     * @param int $hours Počet hodin
     * @return string Správný tvar slova
     */
    private static function getCzechHoursForm($hours)
    {
        if ($hours === 1) {
            return "hodinou";
        } elseif ($hours >= 2 && $hours <= 4) {
            return "hodinami";
        } else {
            return "hodinami";
        }
    }

    /**
     * Vrací formátovaný čas do budoucího data
     * 
     * @param string $futureDate Datum ve formátu, který lze zpracovat strtotime nebo DateTime objekt
     * @return string Formátovaný čas do budoucnosti (např. "za 5 minut", "za 2 hodiny", "za 3 dny")
     */
    public static function getTimeUntil($futureDate)
    {
        if ($futureDate instanceof \DateTime) {
            $timestamp = $futureDate->getTimestamp();
        } else {
            $timestamp = strtotime($futureDate);
        }
        
        $now = time();
        $diff = $timestamp - $now;
        
        // Už proběhlo
        if ($diff <= 0) {
            return "již proběhlo";
        }
        
        // Za méně než hodinu
        if ($diff < 3600) {
            $minutes = ceil($diff / 60);
            return "za " . $minutes . " " . self::getCzechMinutesFutureForm($minutes);
        }
        
        // Za méně než den
        if ($diff < 86400) { // 24 hodin = 24 * 3600 = 86400 sekund
            $hours = floor($diff / 3600);
            $minutes = ceil(($diff % 3600) / 60);
            
            if ($minutes > 0) {
                return "za " . $hours . " " . self::getCzechHoursFutureForm($hours) . 
                       " a " . $minutes . " " . self::getCzechMinutesFutureForm($minutes);
            } else {
                return "za " . $hours . " " . self::getCzechHoursFutureForm($hours);
            }
        }
        
        // Za více dní
        $days = floor($diff / 86400);
        $hours = floor(($diff % 86400) / 3600);
        
        if ($hours > 0) {
            return "za " . $days . " " . self::getCzechDaysFutureForm($days) . 
                   " a " . $hours . " " . self::getCzechHoursFutureForm($hours);
        } else {
            return "za " . $days . " " . self::getCzechDaysFutureForm($days);
        }
    }
    
    /**
     * Vrací správný český tvar slova "minuta" pro budoucnost
     * 
     * @param int $minutes Počet minut
     * @return string Správný tvar slova
     */
    private static function getCzechMinutesFutureForm($minutes)
    {
        if ($minutes === 1) {
            return "minutu";
        } elseif ($minutes >= 2 && $minutes <= 4) {
            return "minuty";
        } else {
            return "minut";
        }
    }
    
    /**
     * Vrací správný český tvar slova "hodina" pro budoucnost
     * 
     * @param int $hours Počet hodin
     * @return string Správný tvar slova
     */
    private static function getCzechHoursFutureForm($hours)
    {
        if ($hours === 1) {
            return "hodinu";
        } elseif ($hours >= 2 && $hours <= 4) {
            return "hodiny";
        } else {
            return "hodin";
        }
    }
    
    /**
     * Vrací správný český tvar slova "den" pro budoucnost
     * 
     * @param int $days Počet dní
     * @return string Správný tvar slova
     */
    private static function getCzechDaysFutureForm($days)
    {
        if ($days === 1) {
            return "den";
        } elseif ($days >= 2 && $days <= 4) {
            return "dny";
        } else {
            return "dní";
        }
    }
} 