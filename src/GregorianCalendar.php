<?php declare(strict_types=1);

namespace time;

final class GregorianCalendar
{
    private const int DAYS_PER_YEAR_COMMON = 365;

    private const array DAYS_IN_MONTH_COMMON = [0,  31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];
    private const array DAYS_IN_MONTH_LEAP = [0,  31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];

    private const int SECONDS_PER_DAY = 24 * 3600;

    private const int HINNANT_YEARS_PER_ERA = 400;

    /** 400 years * 365 days + 97 leap days */
    private const int HINNANT_DAYS_PER_ERA = 146097;

    /**
     * Number of days between Hinnant epoch (0000-03-01) and unix epoch (1970-01-01)
     */
    private const int HINNANT_EPOCH_SHIFT = 719468;

    private function __construct() {}

    public static function isLeapYear(int $year): bool
    {
        return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
    }

    public static function getDaysInYear(int $year): int
    {
        return self::DAYS_PER_YEAR_COMMON + (int)self::isLeapYear($year);
    }

    public static function getDaysInMonth(int $year, Month|int $month): int
    {
        $monthIdx = ($month instanceof Month ? $month->value : $month) - 1;
        return self::isLeapYear($year)
            ? self::DAYS_IN_MONTH_LEAP[$monthIdx]
            : self::DAYS_IN_MONTH_COMMON[$monthIdx];
    }

    /**
     * Calculates the year, month and day of month from the given unix timestamp (in seconds)
     * using the Hinnant algorithm.
     *
     * @return array{int, int<1,12>, int<1,31>}
     */
    public static function getDateByUnixTimestamp(int $ts): array
    {
        // Calculate days since Hinnant epoch
        $days = \intdiv($ts, self::SECONDS_PER_DAY) + self::HINNANT_EPOCH_SHIFT;

        // Adjustment for a negative time portion
        $days += (($ts % self::SECONDS_PER_DAY) < 0) ? -1 : 0;

        $era = \intdiv($days >= 0 ? $days : $days - self::HINNANT_DAYS_PER_ERA + 1, self::HINNANT_DAYS_PER_ERA);
        $day_of_era = $days - $era * self::HINNANT_DAYS_PER_ERA;

        $year_of_era = \intdiv(
            $day_of_era - \intdiv($day_of_era, 1460) + \intdiv($day_of_era, 36524) - \intdiv($day_of_era, 146096),
            self::DAYS_PER_YEAR_COMMON
        );

        $y = $year_of_era + $era * self::HINNANT_YEARS_PER_ERA;
        $day_of_year = $day_of_era - (self::DAYS_PER_YEAR_COMMON * $year_of_era + \intdiv($year_of_era, 4) - \intdiv($year_of_era, 100));
        $month_portion = \intdiv(5 * $day_of_year + 2, 153);
        $d = $day_of_year - \intdiv(153 * $month_portion + 2, 5) + 1;
        $m = $month_portion + ($month_portion < 10 ? 3 : -9);
        $y += (int)($m <= 2);

        return [$y, $m, $d];
    }

    /**
     * Calculates the day of week from the given unix timestamp (in seconds).
     *
     * @return DayOfWeek
     */
    public static function getDowByUnixTimestamp(int $ts): DayOfWeek
    {
        $daysSinceEpoch = \intdiv($ts, self::SECONDS_PER_DAY);
        $daysSinceEpoch += (($ts % self::SECONDS_PER_DAY) < 0) ? -1 : 0;

        // 1970-01-01 is a Thursday
        $dow = $daysSinceEpoch % 7;         // -6 (Fri) - 0 (Thu) - 6 (Wed)
        $dow = $dow < 0 ? $dow + 7 : $dow;  // 0 (Thu) - 6 (Wed)
        $dow = $dow - 3;                    // -3 (Thu) - 3 (Wed)
        $dow = $dow <= 0 ? $dow + 7 : $dow; // 1 (Mon) - 7 (Sun)

        return DayOfWeek::from($dow);
    }
}
