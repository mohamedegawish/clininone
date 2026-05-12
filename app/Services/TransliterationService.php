<?php

namespace App\Services;

class TransliterationService
{
    private static $enToAr = [
        'A' => 'أ', 'B' => 'ب', 'C' => 'ك', 'D' => 'د', 'E' => 'إ', 'F' => 'ف', 'G' => 'ج', 'H' => 'ه',
        'I' => 'ي', 'J' => 'ج', 'K' => 'ك', 'L' => 'ل', 'M' => 'م', 'N' => 'ن', 'O' => 'و', 'P' => 'ب',
        'Q' => 'ق', 'R' => 'ر', 'S' => 'س', 'T' => 'ت', 'U' => 'و', 'V' => 'ف', 'W' => 'و', 'X' => 'كس',
        'Y' => 'ي', 'Z' => 'ز',
        'a' => 'ا', 'b' => 'ب', 'c' => 'ك', 'd' => 'د', 'e' => 'ه', 'f' => 'ف', 'g' => 'ج', 'h' => 'ه',
        'i' => 'ي', 'j' => 'ج', 'k' => 'ك', 'l' => 'ل', 'm' => 'م', 'n' => 'ن', 'o' => 'و', 'p' => 'ب',
        'q' => 'ق', 'r' => 'ر', 's' => 'س', 't' => 'ت', 'u' => 'و', 'v' => 'ف', 'w' => 'و', 'x' => 'كس',
        'y' => 'ي', 'z' => 'ز',
        'sh' => 'ش', 'ch' => 'تش', 'th' => 'ث', 'kh' => 'خ', 'gh' => 'غ', 'ee' => 'ي', 'oo' => 'و', 'ou' => 'و',
    ];

    public static function enToAr(string $text): string
    {
        $text = str_replace(['Dr.', 'Dr', 'dr.'], 'د.', $text);
        
        // Simple transliteration logic
        $result = '';
        $i = 0;
        $len = strlen($text);
        
        while ($i < $len) {
            $found = false;
            // Check for double chars first
            if ($i + 1 < $len) {
                $pair = strtolower(substr($text, $i, 2));
                if (isset(self::$enToAr[$pair])) {
                    $result .= self::$enToAr[$pair];
                    $i += 2;
                    $found = true;
                }
            }
            
            if (!$found) {
                $char = substr($text, $i, 1);
                $result .= self::$enToAr[$char] ?? $char;
                $i++;
            }
        }
        
        return $result;
    }
}
