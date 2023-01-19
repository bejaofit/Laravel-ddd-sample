<?php

namespace Bejao\Shared\Framework;

use Exception;
use RuntimeException;
use function count;

final class StringHelper
{

    /** @var string[][] */
    public static array $allowedCurrencies = [
        'EUR' => ['EUROS', 'EURO', 'EUR', '€'],
    ];

    /**
     * @param string $string
     * @param bool $throwException
     * @return float
     * @throws Exception
     */
    public static function getNumberFromString(string $string, bool $throwException = false): float
    {
        if (empty($string)) {
            return 0;
        }
        $string = mb_strtoupper($string, 'UTF-8');
        $string = trim($string);

        $string = str_replace(' ', '', $string);
        $string = self::removeCurrencyFromString($string);

        $coma = strpos($string, ',');

        $point = strpos($string, '.');
        if ($coma && $point) { //1.000,00 or 1,000.00
            if ($coma < $point) { //1,000.00
                $string = str_replace(',', '', $string);
                return (float)$string;
            }
            //1.000,00
            $s = str_replace(['.', ','], ['', '.'], $string);
            return (float)$s;
        }
        if ($coma && ($point === false)) { //1,000 or //10,00
            $ar = explode(',', $string);
            if (count($ar) > 2) { //1,000,0000
                return (float)str_replace(',', '', $string);
            }

            if (3 !== strlen($ar[1])) { //1,00
                return (float)str_replace(',', '.', $string);
            }
            //1,000 we are really fucked, will treat as dot up to now
            return (float)str_replace(',', '', $string);
        }
        if ($throwException && false === is_numeric($string)) {
            throw new RuntimeException('Not a numeric value');
        }
        return (float)$string;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function removeCurrencyFromString(string $string): string
    {
        $currencies = [];
        foreach (self::$allowedCurrencies as $allowedCurrencyAlternatives) {
            foreach ($allowedCurrencyAlternatives as $currencyAlternative) {
                $currencies[] = $currencyAlternative;
            }
        }
        return str_replace($currencies, '', $string);
    }

    public static function isHTML(string $string): bool
    {
        return ($string != strip_tags($string));
    }

}
