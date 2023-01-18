<?php

namespace Bejao\Shared\Framework;

final class TemplateHelper
{
    /**
     * @param string $template
     * @param array<string,string|int|float> $variables
     * @return string
     */
    public static function processTemplate(string $template, array $variables): string
    {
        $result = $template;
        foreach ($variables as $variable => $value) {
            $result = str_replace('{{' . $variable . '}}', (string)$value, $result);
        }
        $result = self::parseText($result);
        return str_replace(['((', '))'], ['[', ']'], $result);
    }

    private static function parseText(string $text): string
    {
        preg_match_all('/(?<=\[).+?(?=\])/', $text, $matches);

        foreach ($matches['0'] as $match) {


            if (strpos($match, '|')) {
                $text = self::randomValues($match, $text);
                continue;
            }

            if (strpos($match, '==')) {
                $text = self::ifThenElse($match, $text);
                continue;
            }
            if (strpos($match, '>') !== false) {
                $text = self::firstNonEmptyValue($match, $text);
            }


        }
        return $text;
    }

    /**
     * @param string $match
     * @param string $text
     * @return string
     */
    private static function randomValues(string $match, string $text): string
    {
        $values = explode('|', $match);
        $values = array_filter($values, static function ($item) {
            if (str_contains($item, '#ERROR#')) {
                return false;
            }
            if (trim($item) === '') {
                return false;
            }
            return true;
        });
        $text = str_replace('[' . $match . ']', $values[array_rand($values)], $text);
        return $text;
    }

    /**
     * @param string $match
     * @param string $text
     * @return string
     */
    private static function firstNonEmptyValue(string $match, string $text): string
    {
        $values = explode('>', $match);
        $values = array_filter($values, static function ($item) {
            if (str_contains($item, '#ERROR#')) {
                return false;
            }
            if (trim($item) === '') {
                return false;
            }
            return true;
        });
        $firstItem = current($values);
        $text = str_replace('[' . $match . ']', $firstItem ?: '#ERROR#', $text);
        return $text;
    }

    /**
     * @param string $match
     * @param string $text
     * @return string
     */
    private static function ifThenElse(string $match, string $text): string
    {
        [$comparison, $then, $else] = explode('>', $match);

        [$value1, $value2] = explode('==', $comparison);

        $result = $else;
        if ($value1 === $value2) {
            $result = $then;
        }

        $text = str_replace('[' . $match . ']', $result, $text);
        return $text;
    }

}
