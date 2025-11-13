<?php

namespace App\Helpers;

class AISEOHelper
{
    /**
     * Generuje optimalizovan├Ż title pomoc├ş AI logiky
     */
    public static function generateOptimizedTitle($title, $keywords = [], $maxLength = 60)
    {
        // Z├íkladn├ş optimalizace
        $title = trim($title);
        
        // P┼Öidej kl├ş─Źov├í slova pokud se nevejdou
        if (!empty($keywords)) {
            $primaryKeyword = $keywords[0] ?? '';
            if ($primaryKeyword && !stripos($title, $primaryKeyword)) {
                $title = $primaryKeyword . ' - ' . $title;
            }
        }
        
        // Omezen├ş d├ęlky
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3) . '...';
        }
        
        return $title;
    }
    
    /**
     * Generuje optimalizovan├Ż popis pomoc├ş AI logiky
     */
    public static function generateOptimizedDescription($content, $keywords = [], $maxLength = 160)
    {
        // Vy─Źisti obsah
        $content = strip_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Najdi nejlep┼í├ş v─Ťtu obsahuj├şc├ş kl├ş─Źov├í slova
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
        
        // Omezen├ş d├ęlky
        if (strlen($content) > $maxLength) {
            $content = substr($content, 0, $maxLength - 3) . '...';
        }
        
        return $content;
    }
    
    /**
     * Extrahuje kl├ş─Źov├í slova z obsahu pomoc├ş AI logiky
     */
    public static function extractKeywords($content, $limit = 10)
    {
        $content = strip_tags($content);
        $content = strtolower($content);
        
        // Stop slova v ─Źe┼ítin─Ť
        $stopWords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'has', 'he', 'in', 'is', 'it', 'its', 'of', 'on', 'that', 'the', 'to', 'was', 'will', 'with',
            'se', 'na', 'je', 'v', 'z', 'do', 'od', 'pro', 'k', 'o', 'u', 'za', 'po', 'p┼Öi', 'mezi', 'kdy┼ż', 'kde', 'jak', 'co', 'kter├Ż', 'kter├í', 'kter├ę',
            'ale', 'nebo', 'ale', 'proto', 'proto┼że', 'tak├ę', 'tak', 'pak', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy', 'tedy'
        ];
        
        // Rozd─Ťl na slova
        $words = preg_split('/\s+/', $content);
        $words = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords) && preg_match('/^[a-z├í─Ź─Ć├ę─Ť├ş┼ł├│┼Ö┼í┼ą├║┼»├Ż┼ż]+$/i', $word);
        });
        
        // Po─Ź├ştej frekvenci
        $wordCount = array_count_values($words);
        arsort($wordCount);
        
        // Vra┼ą top slova
        return array_slice(array_keys($wordCount), 0, $limit);
    }
    
    /**
     * Generuje meta keywords pro ─Źl├ínek
     */
    public static function generateMetaKeywords($content, $title = '', $customKeywords = [])
    {
        $keywords = array_merge($customKeywords, self::extractKeywords($content, 8));
        
        // P┼Öidej slova z titulku
        if (!empty($title)) {
            $titleWords = self::extractKeywords($title, 3);
            $keywords = array_merge($keywords, $titleWords);
        }
        
        // Odstra┼ł duplicity a omezen├ş
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        
        return implode(', ', $keywords);
    }
    
    /**
     * Analyzuje SEO kvalitu ─Źl├ínku
     */
    public static function analyzeSEOQuality($title, $description, $content, $keywords = [])
    {
        $score = 0;
        $maxScore = 100;
        $issues = [];
        
        // Anal├Żza titulku
        if (strlen($title) < 30) {
            $issues[] = 'Title je p┼Ö├şli┼í kr├ítk├Ż (doporu─Źeno 30-60 znak┼»)';
        } elseif (strlen($title) > 60) {
            $issues[] = 'Title je p┼Ö├şli┼í dlouh├Ż (doporu─Źeno 30-60 znak┼»)';
        } else {
            $score += 20;
        }
        
        // Anal├Żza popisu
        if (strlen($description) < 120) {
            $issues[] = 'Description je p┼Ö├şli┼í kr├ítk├Ż (doporu─Źeno 120-160 znak┼»)';
        } elseif (strlen($description) > 160) {
            $issues[] = 'Description je p┼Ö├şli┼í dlouh├Ż (doporu─Źeno 120-160 znak┼»)';
        } else {
            $score += 20;
        }
        
        // Anal├Żza obsahu
        $wordCount = str_word_count(strip_tags($content));
        if ($wordCount < 300) {
            $issues[] = 'Obsah je p┼Ö├şli┼í kr├ítk├Ż (doporu─Źeno min. 300 slov)';
        } else {
            $score += 20;
        }
        
        // Anal├Żza kl├ş─Źov├Żch slov v titulku
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
                $issues[] = 'Kl├ş─Źov├í slova nejsou v titulku';
            }
        }
        
        // Anal├Żza kl├ş─Źov├Żch slov v popisu
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
                $issues[] = 'Kl├ş─Źov├í slova nejsou v popisu';
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
     * Generuje doporu─Źen├ş pro zlep┼íen├ş SEO
     */
    public static function generateSEORecommendations($analysis)
    {
        $recommendations = [];
        
        if ($analysis['percentage'] < 60) {
            $recommendations[] = 'SEO sk├│re je n├şzk├ę - doporu─Źujeme zlep┼íit z├íkladn├ş elementy';
        }
        
        if ($analysis['wordCount'] < 500) {
            $recommendations[] = 'P┼Öidejte v├şce obsahu - del┼í├ş ─Źl├ínky maj├ş lep┼í├ş SEO';
        }
        
        if (empty($analysis['issues'])) {
            $recommendations[] = 'V├Żborn─Ť! SEO je optimalizov├íno';
        }
        
        return $recommendations;
    }
    
    /**
     * Generuje optimalizovan├Ż URL slug
     */
    public static function generateOptimizedSlug($title, $maxLength = 50)
    {
        // Odstra┼ł diakritiku
        $slug = self::removeDiacritics($title);
        
        // P┼Öeve─Ć na mal├í p├şsmena
        $slug = strtolower($slug);
        
        // Nahra─Ć mezery a speci├íln├ş znaky poml─Źkami
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Odstra┼ł poml─Źky na za─Ź├ítku a konci
        $slug = trim($slug, '-');
        
        // Omezen├ş d├ęlky
        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
            $slug = rtrim($slug, '-');
        }
        
        return $slug;
    }
    
    /**
     * Odstran├ş diakritiku z textu
     */
    private static function removeDiacritics($text)
    {
        $diacritics = [
            '├í' => 'a', '─Ź' => 'c', '─Ć' => 'd', '├ę' => 'e', '─Ť' => 'e', '├ş' => 'i', '┼ł' => 'n', '├│' => 'o', '┼Ö' => 'r', '┼í' => 's', '┼ą' => 't', '├║' => 'u', '┼»' => 'u', '├Ż' => 'y', '┼ż' => 'z',
            '├ü' => 'A', '─î' => 'C', '─Ä' => 'D', '├ë' => 'E', '─Ü' => 'E', '├Ź' => 'I', '┼ç' => 'N', '├ô' => 'O', '┼ś' => 'R', '┼á' => 'S', '┼Ą' => 'T', '├Ü' => 'U', '┼«' => 'U', '├Ł' => 'Y', '┼Ż' => 'Z'
        ];
        
        return strtr($text, $diacritics);
    }
}
