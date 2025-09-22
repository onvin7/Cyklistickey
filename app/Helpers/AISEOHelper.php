<?php

namespace App\Helpers;

class AISEOHelper
{
    /**
     * Generuje optimalizovaný title pomocí AI logiky
     */
    public static function generateOptimizedTitle($title, $keywords = [], $maxLength = 60)
    {
        // Základní optimalizace
        $title = trim($title);
        
        // Přidej klíčová slova pokud se nevejdou
        if (!empty($keywords)) {
            $primaryKeyword = $keywords[0] ?? '';
            if ($primaryKeyword && !stripos($title, $primaryKeyword)) {
                $title = $primaryKeyword . ' - ' . $title;
            }
        }
        
        // Omezení délky
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3) . '...';
        }
        
        return $title;
    }
    
    /**
     * Generuje optimalizovaný popis pomocí AI logiky
     */
    public static function generateOptimizedDescription($content, $keywords = [], $maxLength = 160)
    {
        // Vyčisti obsah
        $content = strip_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Najdi nejlepší větu obsahující klíčová slova
        if (!empty($keywords)) {
            $sentences = preg_split('/[.!?]+/', $content);
            $bestSentence = '';
            $maxScore = 0;
            
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) < 50) continue;
                
                $score = 0;
                foreach ($keywords as $keyword) {
                    if (stripos($sentence, $keyword) !== false) {
                        $score++;
                    }
                }
                
                if ($score > $maxScore) {
                    $maxScore = $score;
                    $bestSentence = $sentence;
                }
            }
            
            if (!empty($bestSentence)) {
                $content = $bestSentence;
            }
        }
        
        // Omezení délky
        if (strlen($content) > $maxLength) {
            $content = substr($content, 0, $maxLength - 3) . '...';
        }
        
        return $content;
    }
    
    /**
     * Extrahuje klíčová slova z obsahu pomocí AI logiky
     */
    public static function extractKeywords($content, $limit = 10)
    {
        $content = strip_tags($content);
        $content = strtolower($content);
        
        // Stop slova v češtině
        $stopWords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'has', 'he', 'in', 'is', 'it', 'its', 'of', 'on', 'that', 'the', 'to', 'was', 'will', 'with',
            'se', 'na', 'je', 'v', 'z', 'do', 'od', 'pro', 'k', 'o', 'u', 'za', 'po', 'při', 'mezi', 'když', 'kde', 'jak', 'co', 'který', 'která', 'které',
            'ale', 'nebo', 'ale', 'proto', 'protože', 'také', 'tak', 'pak', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy'
        ];
        
        // Rozděl na slova
        $words = preg_split('/\s+/', $content);
        $words = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords) && preg_match('/^[a-záčďéěíňóřšťúůýž]+$/i', $word);
        });
        
        // Počítej frekvenci
        $wordCount = array_count_values($words);
        arsort($wordCount);
        
        // Vrať top slova
        return array_slice(array_keys($wordCount), 0, $limit);
    }
    
    /**
     * Generuje meta keywords pro článek
     */
    public static function generateMetaKeywords($content, $title = '', $customKeywords = [])
    {
        $keywords = array_merge($customKeywords, self::extractKeywords($content, 8));
        
        // Přidej slova z titulku
        if (!empty($title)) {
            $titleWords = self::extractKeywords($title, 3);
            $keywords = array_merge($keywords, $titleWords);
        }
        
        // Odstraň duplicity a omezení
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        
        return implode(', ', $keywords);
    }
    
    /**
     * Analyzuje SEO kvalitu článku
     */
    public static function analyzeSEOQuality($title, $description, $content, $keywords = [])
    {
        $score = 0;
        $maxScore = 100;
        $issues = [];
        
        // Analýza titulku
        if (strlen($title) < 30) {
            $issues[] = 'Title je příliš krátký (doporučeno 30-60 znaků)';
        } elseif (strlen($title) > 60) {
            $issues[] = 'Title je příliš dlouhý (doporučeno 30-60 znaků)';
        } else {
            $score += 20;
        }
        
        // Analýza popisu
        if (strlen($description) < 120) {
            $issues[] = 'Description je příliš krátký (doporučeno 120-160 znaků)';
        } elseif (strlen($description) > 160) {
            $issues[] = 'Description je příliš dlouhý (doporučeno 120-160 znaků)';
        } else {
            $score += 20;
        }
        
        // Analýza obsahu
        $wordCount = str_word_count(strip_tags($content));
        if ($wordCount < 300) {
            $issues[] = 'Obsah je příliš krátký (doporučeno min. 300 slov)';
        } else {
            $score += 20;
        }
        
        // Analýza klíčových slov v titulku
        if (!empty($keywords)) {
            $titleLower = strtolower($title);
            $hasKeywordInTitle = false;
            foreach ($keywords as $keyword) {
                if (stripos($titleLower, $keyword) !== false) {
                    $hasKeywordInTitle = true;
                    break;
                }
            }
            
            if ($hasKeywordInTitle) {
                $score += 20;
            } else {
                $issues[] = 'Klíčová slova nejsou v titulku';
            }
        }
        
        // Analýza klíčových slov v popisu
        if (!empty($keywords)) {
            $descLower = strtolower($description);
            $hasKeywordInDesc = false;
            foreach ($keywords as $keyword) {
                if (stripos($descLower, $keyword) !== false) {
                    $hasKeywordInDesc = true;
                    break;
                }
            }
            
            if ($hasKeywordInDesc) {
                $score += 20;
            } else {
                $issues[] = 'Klíčová slova nejsou v popisu';
            }
        }
        
        return [
            'score' => $score,
            'maxScore' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100),
            'issues' => $issues,
            'wordCount' => $wordCount
        ];
    }
    
    /**
     * Generuje doporučení pro zlepšení SEO
     */
    public static function generateSEORecommendations($analysis)
    {
        $recommendations = [];
        
        if ($analysis['percentage'] < 60) {
            $recommendations[] = 'SEO skóre je nízké - doporučujeme zlepšit základní elementy';
        }
        
        if ($analysis['wordCount'] < 500) {
            $recommendations[] = 'Přidejte více obsahu - delší články mají lepší SEO';
        }
        
        if (empty($analysis['issues'])) {
            $recommendations[] = 'Výborně! SEO je optimalizováno';
        }
        
        return $recommendations;
    }
    
    /**
     * Generuje optimalizovaný URL slug
     */
    public static function generateOptimizedSlug($title, $maxLength = 50)
    {
        // Odstraň diakritiku
        $slug = self::removeDiacritics($title);
        
        // Převeď na malá písmena
        $slug = strtolower($slug);
        
        // Nahraď mezery a speciální znaky pomlčkami
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Odstraň pomlčky na začátku a konci
        $slug = trim($slug, '-');
        
        // Omezení délky
        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
            $slug = rtrim($slug, '-');
        }
        
        return $slug;
    }
    
    /**
     * Odstraní diakritiku z textu
     */
    private static function removeDiacritics($text)
    {
        $diacritics = [
            'á' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e', 'í' => 'i', 'ň' => 'n', 'ó' => 'o', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u', 'ů' => 'u', 'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E', 'Ě' => 'E', 'Í' => 'I', 'Ň' => 'N', 'Ó' => 'O', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U', 'Ů' => 'U', 'Ý' => 'Y', 'Ž' => 'Z'
        ];
        
        return strtr($text, $diacritics);
    }
}
