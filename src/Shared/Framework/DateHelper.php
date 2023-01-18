<?php

namespace Bejao\Shared\Framework;

final class DateHelper
{
    public const DAY = 24 * 60 * 60;
    public const YEAR = 24 * 60 * 60 * 365;

    public static function getDurationWeeksAndDays(int $seconds): string
    {
        if ($seconds < 0 || $seconds > self::YEAR * 10) {
            return '';
        }
        if ($seconds < self::DAY) {
            return '1D';
        }
        $weeks = $seconds / (self::DAY * 7);
        $rest = $weeks - floor($weeks);
        $days = $rest * 7;
        return (floor($weeks) ? floor($weeks) . 'S' : '') . ($days ? (floor($days) . 'D') : '');
    }

    public static function getDurationDaysAndHours(int $seconds): string
    {
        if ($seconds < 0 || $seconds > self::YEAR * 10) {
            return '';
        }
        if ($seconds < self::DAY) {
            return round(($seconds / (60 * 60)), 1) . ' hours';
        }
        if ($seconds < self::DAY * 4) {
            $days = floor($seconds / self::DAY);
            $hours = round(($seconds - $days * self::DAY)/(60*60), 1);
            return $days . ' days ' . $hours . ' hours';
        }
        return (floor($seconds / self::DAY)) . ' days';
    }
}
