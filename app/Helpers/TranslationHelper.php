<?php

namespace App\Helpers;

class TranslationHelper
{
    const LANG_AR = 'ar';
    const LANG_EN = 'en';
    const SESSION_KEY = 'app_language';

    /**
     * Get current language preference from Laravel locale
     * 
     * @return string
     */
    public static function getCurrentLanguage(): string
    {
        return app()->getLocale();
    }

    /**
     * Get label based on current Laravel locale
     * 
     * @param string $ar Arabic text
     * @param string $en English text
     * @return string
     */
    public static function label(string $ar, string $en): string
    {
        $lang = app()->getLocale();
        
        if ($lang === self::LANG_EN) {
            return $en;
        }
        
        return $ar;
    }

    /**
     * Get bilingual label (Arabic / English) - for display when both are needed
     * 
     * @param string $ar Arabic text
     * @param string $en English text
     * @return string
     */
    public static function labelBoth(string $ar, string $en): string
    {
        return "{$ar} / {$en}";
    }

    /**
     * Get bilingual option array
     * 
     * @param array $options Array of ['key' => ['ar' => 'Arabic', 'en' => 'English']]
     * @return array
     */
    public static function options(array $options): array
    {
        $result = [];
        $lang = app()->getLocale();
        
        foreach ($options as $key => $translations) {
            if (is_array($translations) && isset($translations['ar']) && isset($translations['en'])) {
                $result[$key] = $lang === self::LANG_EN ? $translations['en'] : $translations['ar'];
            } else {
                $result[$key] = $translations;
            }
        }
        return $result;
    }
}

