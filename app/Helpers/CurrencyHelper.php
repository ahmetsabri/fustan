<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function getCurrencies(): array
    {
        return [
            'KWD' => 'KWD - دينار كويتي',
            'SAR' => 'SAR - ريال سعودي',
            'AED' => 'AED - درهم إماراتي',
            'BHD' => 'BHD - دينار بحريني',
            'OMR' => 'OMR - ريال عماني',
            'QAR' => 'QAR - ريال قطري',
            'USD' => 'USD - دولار أمريكي',
        ];
    }

    public static function getCurrencySymbol(string $currency): string
    {
        return match ($currency) {
            'KWD' => 'د.ك',
            'SAR' => 'ر.س',
            'AED' => 'د.إ',
            'BHD' => 'د.ب',
            'OMR' => 'ر.ع',
            'QAR' => 'ر.ق',
            'USD' => '$',
            default => $currency,
        };
    }

    public static function getDefaultCurrency(): string
    {
        return 'KWD';
    }
}

