<?php

namespace App\Helpers;

class TextHelper
{
    /**
     * Zkrátí text na požadovanou délku a přidá tři tečky na konec, pokud je text zkrácen
     * 
     * @param string $text Text k zkrácení
     * @param int $maxLength Maximální délka textu
     * @param string $suffix Suffix který se přidá na konec zkráceného textu (výchozí "...")
     * @return string Zkrácený text
     */
    public static function truncate(string $text, int $maxLength, string $suffix = "..."): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $maxLength);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            return mb_substr($truncated, 0, $lastSpace) . $suffix;
        }

        return $truncated . $suffix;
    }

    /**
     * Odstraní diakritiku z textu
     * 
     * @param string $string Text k úpravě
     * @return string Text bez diakritiky
     */
    private static function removeAccents(string $string): string
    {
        $table = [
            'á' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e', 'í' => 'i',
            'ň' => 'n', 'ó' => 'o', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u',
            'ů' => 'u', 'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E', 'Ě' => 'E', 'Í' => 'I',
            'Ň' => 'N', 'Ó' => 'O', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U',
            'Ů' => 'U', 'Ý' => 'Y', 'Ž' => 'Z'
        ];
        return strtr($string, $table);
    }

    /**
     * Vygeneruje URL přátelský řetězec z daného textu
     * 
     * @param string $string Text k převedení na URL
     * @return string URL přátelský řetězec
     */
    public static function generateFriendlyUrl(string $string): string
    {
        $string = self::removeAccents($string); // Odstranění diakritiky
        $string = strtolower($string); // Převod na malá písmena
        $string = preg_replace("/[^a-z0-9\s-]/", "", $string); // Odstranění nežádoucích znaků
        $string = preg_replace("/\s+/", "-", $string); // Nahrazení mezer pomlčkou
        return $string;
    }
} 