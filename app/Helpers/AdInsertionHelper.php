<?php

namespace App\Helpers;

class AdInsertionHelper
{
    public static function countContentBlocks(string $html): int
    {
        if (trim($html) === '') return 0;

        $pattern = '/(<\/p>|<\/figure>|<\/blockquote>|<\/iframe>|<\/h2>|<\/h3>|<\/ul>|<\/ol>|<div[^>]*class="[^"]*(video-container|youtube-|fb-|strava-)[^"]*"[^>]*>.*?<\/div>)/is';
        
        preg_match_all($pattern, $html, $matches);
        return count($matches[0]);
    }

    public static function getInsertAfterBlocksForLength(int $blockCount, int $maxAds = 2): array
    {
        if ($blockCount <= 0 || $maxAds <= 0) {
            return [];
        }

        // Standardní nastavení: první reklama po 3. bloku, další po každých 6 blocích
        $firstPos = 3; 
        
        $insertAfter = [];
        
        // Pokud je článek moc krátký na 3. blok, ale má aspoň 1 blok, vložíme to na konec 1. bloku
        if ($blockCount < $firstPos && $blockCount >= 1) {
            $insertAfter[] = $blockCount;
        } else {
            // Standardní cyklus
            for ($i = 0; $i < $maxAds; $i++) {
                $pos = $firstPos + ($i * 6);
                if ($pos > $blockCount) {
                    break;
                }
                $insertAfter[] = $pos;
            }
        }

        $insertAfter = array_values(array_unique(array_map('intval', $insertAfter)));
        sort($insertAfter);
        return $insertAfter;
    }

    public static function insertAdsIntoHtml(string $html, array $ads, array $afterBlocks): string
    {
        if (empty($ads) || empty($afterBlocks)) return $html;

        $ads = array_values(array_filter($ads, function ($ad) {
            return is_array($ad) && (!empty($ad['obrazek']) || !empty($ad['kod']));
        }));

        if (empty($ads)) return $html;

        // Regex pro nalezení konců bloků (odstavce, obrázky, nadpisy, vnořená média)
        $pattern = '/(<\/p>|<\/figure>|<\/blockquote>|<\/iframe>|<\/h2>|<\/h3>|<\/ul>|<\/ol>|<div[^>]*class="[^"]*(video-container|youtube-|fb-|strava-)[^"]*"[^>]*>.*?<\/div>)/is';
        
        $parts = preg_split($pattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false || count($parts) <= 1) {
            // Pokud regex selhal, zkusíme aspoň vložit na konec
            return $html . self::renderAdHtml($ads[0]);
        }

        $newHtml = '';
        $blockCount = 0;
        $adIndex = 0;
        
        for ($i = 0; $i < count($parts); $i++) {
            $newHtml .= $parts[$i];
            
            // Každý lichý index v $parts je zachycený tag (konec bloku)
            if ($i % 2 === 1) {
                $blockCount++;
                
                if ($adIndex < count($afterBlocks) && $blockCount === $afterBlocks[$adIndex]) {
                    $adToUse = $ads[$adIndex] ?? $ads[0];
                    $newHtml .= self::renderAdHtml($adToUse);
                    $adIndex++;
                }
            }
        }
        
        return $newHtml;
    }

    /**
     * Vyrenderuje HTML kód reklamy (banner nebo script)
     */
    private static function renderAdHtml(array $ad): string
    {
        $kod = trim((string) ($ad['kod'] ?? ''));
        $id = (string)($ad['id'] ?? '');
        
        $output = '<div class="article-ad" data-ad-id="' . $id . '">';

        if ($kod !== '') {
            $output .= $kod;
        } else {
            $image = (string) ($ad['obrazek'] ?? '');
            $href = trim((string) ($ad['odkaz'] ?? ''));
            $title = (string) ($ad['nazev'] ?? 'Reklama');
            
            $imgTag = '<img src="/uploads/ads/' . ltrim($image, '/') . '" alt="' . htmlspecialchars($title) . '" loading="lazy">';
            
            if ($href !== '') {
                $output .= '<a href="' . htmlspecialchars($href) . '" target="_blank" rel="noopener noreferrer sponsored">' . $imgTag . '</a>';
            } else {
                $output .= $imgTag;
            }
        }

        $output .= '</div>';
        return $output;
    }
}
