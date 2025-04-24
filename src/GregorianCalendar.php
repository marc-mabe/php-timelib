<?php declare(strict_types=1);

namespace time;

final class GregorianCalendar implements Calendar
{
    private const int DAYS_PER_YEAR_COMMON = 365;
    private const int DAYS_PER_YEAR_LEAP   = 366;

    private const array DAYS_IN_MONTH_COMMON = [31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];
    private const array DAYS_IN_MONTH_LEAP   = [31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];

    private const array DAYS_OF_YEAR_BY_MONTH_COMMON = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365];
    private const array DAYS_OF_YEAR_BY_MONTH_LEAP   = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366];

    private const int SECONDS_PER_DAY = 24 * 3600;

    private const int HINNANT_YEARS_PER_ERA = 400;

    /** 400 years * 365 days + 97 leap days */
    private const int HINNANT_DAYS_PER_ERA = 146097;

    /**
     * Number of days between Hinnant epoch (0000-03-01) and unix epoch (1970-01-01)
     */
    private const int HINNANT_EPOCH_SHIFT = 719468;

    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function isLeapYear(int $year): bool
    {
        return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
    }

    /**
     * @return int<365,366>
     */
    public function getDaysInYear(int $year): int
    {
        return $this->isLeapYear($year)
            ? self::DAYS_PER_YEAR_LEAP
            : self::DAYS_PER_YEAR_COMMON;
    }

    /**
     * @param Month|int<1,12> $month
     * @return int<28,31>
     */
    public function getDaysInMonth(int $year, Month|int $month): int
    {
        $monthIdx = ($month instanceof Month ? $month->value : $month) - 1;
        return $this->isLeapYear($year)
            ? self::DAYS_IN_MONTH_LEAP[$monthIdx]
            : self::DAYS_IN_MONTH_COMMON[$monthIdx];
    }

    /**
     * @return array{int, Month, int<1,31>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array
    {
        $days += self::HINNANT_EPOCH_SHIFT;
        $era = \intdiv($days >= 0 ? $days : $days - self::HINNANT_DAYS_PER_ERA + 1, self::HINNANT_DAYS_PER_ERA);
        $dayOfEra = $days - $era * self::HINNANT_DAYS_PER_ERA;

        $yearOfEra = \intdiv(
            $dayOfEra - \intdiv($dayOfEra, 1460) + \intdiv($dayOfEra, 36524) - \intdiv($dayOfEra, 146096),
            self::DAYS_PER_YEAR_COMMON
        );

        $year = $yearOfEra + $era * self::HINNANT_YEARS_PER_ERA;
        $dayOfYear = $dayOfEra - (
            self::DAYS_PER_YEAR_COMMON * $yearOfEra
            + \intdiv($yearOfEra, 4)
            - \intdiv($yearOfEra, 100)
        );
        $monthPortion = \intdiv(5 * $dayOfYear + 2, 153);
        $day = $dayOfYear - \intdiv(153 * $monthPortion + 2, 5) + 1;
        $month = $monthPortion + ($monthPortion < 10 ? 3 : -9);
        $year += (int)($month <= 2);

        /** @phpstan-ignore return.type */
        return [$year, Month::from($month), $day];
    }

    /**
     * @return array{int, Month, int<1,31>}
     */
    public function getYmdByUnixTimestamp(int $ts): array
    {
        $days = \intdiv($ts, self::SECONDS_PER_DAY);
        $days -= (int)(($ts % self::SECONDS_PER_DAY) < 0);

        return $this->getYmdByDaysSinceUnixEpoch($days);
    }

    /**
     * @param Month|int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getDaysSinceUnixEpochByYmd(int $year, Month|int $month, int $dayOfMonth): int
    {
        $month = $month instanceof Month ? $month->value : $month;

        // adjust leap days to the end of the year and month between 0 and 11
        if ($month <= 2) {
            $year -= 1;
            $month += 9;
        } else {
            $month -= 3;
        }

        $era = \intdiv($year >= 0 ? $year : $year - (self::HINNANT_YEARS_PER_ERA - 1), self::HINNANT_YEARS_PER_ERA);
        $yoe = $year - $era * self::HINNANT_YEARS_PER_ERA; // [0, 399]
        $doy = \intdiv(153 * $month + 2, 5) + $dayOfMonth - 1;   // [0, 365]
        $doe = $yoe * self::DAYS_PER_YEAR_COMMON + \intdiv($yoe, 4) - \intdiv($yoe, 100) + $doy; // [0, 146096]
        return $era * self::HINNANT_DAYS_PER_ERA + $doe - self::HINNANT_EPOCH_SHIFT;
    }

    /**
     * @param Month|int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getUnixTimestampByYmd(int $year, Month|int $month, int $dayOfMonth): int
    {
        return $this->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth) * self::SECONDS_PER_DAY;
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int
    {
        $daysOfYearByMonth = self::isLeapYear($year)
            ? self::DAYS_OF_YEAR_BY_MONTH_LEAP
            : self::DAYS_OF_YEAR_BY_MONTH_COMMON;

        $dayOfMonth = 0;
        for ($month = \intdiv($dayOfYear, 31) + 1; $month <= 12; $month++) {
            if ($daysOfYearByMonth[$month] >= $dayOfYear) {
                $dayOfMonth = $dayOfYear - $daysOfYearByMonth[$month - 1];
                break;
            }
        }

        assert($month > 0 && $month <= 12);
        assert($dayOfMonth > 0 && $dayOfMonth <= 31);

        return $this->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public function getUnixTimestampByYd(int $year, int $dayOfYear): int
    {
        return $this->getDaysSinceUnixEpochByYd($year, $dayOfYear) * self::SECONDS_PER_DAY;
    }

    /**
     * @param Month|int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,366>
     */
    public function getDayOfYearByYmd(int $year, Month|int $month, int $dayOfMonth): int
    {
        $month = $month instanceof Month ? $month->value : $month;
        return ($this->isLeapYear($year)
            ? self::DAYS_OF_YEAR_BY_MONTH_LEAP[$month - 1]
            : self::DAYS_OF_YEAR_BY_MONTH_COMMON[$month - 1]
        ) + $dayOfMonth;
    }

    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): DayOfWeek
    {
        // 1970-01-01 is a Thursday
        $dow = $days % 7;                   // -6 (Fri) - 0 (Thu) - 6 (Wed)
        $dow = $dow < 0 ? $dow + 7 : $dow;  //  0 (Thu) - 6 (Wed)
        $dow = $dow - 3;                    // -3 (Thu) - 3 (Wed)
        $dow = $dow <= 0 ? $dow + 7 : $dow; //  1 (Mon) - 7 (Sun)

        return DayOfWeek::from($dow);
    }

    public function getDayOfWeekByUnixTimestamp(int $ts): DayOfWeek
    {
        $days = \intdiv($ts, self::SECONDS_PER_DAY);
        $days -= (int)(($ts % self::SECONDS_PER_DAY) < 0);

        return $this->getDayOfWeekByDaysSinceUnixEpoch($days);
    }
}
