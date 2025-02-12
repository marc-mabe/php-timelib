<?php

namespace time;

final class GregorianCalendar
{
    private const array DAYS_IN_MONTH_COMMON = [0,  31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];
    private const array DAYS_IN_MONTH_LEAP = [0,  31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];

    private function __construct() {}

    public static function isLeapYear(int $year): bool
    {
        return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
    }

    public static function getDaysInYear(int $year): int
    {
        return self::isLeapYear($year) ? 366 : 365;
    }

    public static function getDaysInMonth(int $year, Month|int $month): int
    {
        $monthIdx = $month instanceof Month ? $month->value - 1 : $month - 1;
        return self::isLeapYear($year)
            ? self::DAYS_IN_MONTH_LEAP[$monthIdx]
            : self::DAYS_IN_MONTH_COMMON[$monthIdx];
    }
}
