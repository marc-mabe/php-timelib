<?php declare(strict_types=1);

namespace time;

final class GregorianCalendar implements Calendar
{
    private const int DAYS_PER_YEAR_COMMON = 365;
    private const int DAYS_PER_YEAR_LEAP   = 366;

    private const array DAYS_IN_MONTH_COMMON = [0, 31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];
    private const array DAYS_IN_MONTH_LEAP   = [0, 31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];

    private const array DAYS_OF_YEAR_BY_MONTH_COMMON = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365];
    private const array DAYS_OF_YEAR_BY_MONTH_LEAP   = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366];

    private const int SECONDS_PER_DAY = 24 * 3600;

    private const int HINNANT_YEARS_PER_ERA = 400;

    private const int HINNANT_LEAP_YEARS_PER_ERA = 97;

    private const int HINNANT_DAYS_PER_ERA = self::HINNANT_YEARS_PER_ERA * self::DAYS_PER_YEAR_COMMON
        + self::HINNANT_LEAP_YEARS_PER_ERA;

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
     * @param int<1,12> $month
     * @return int<28,31>
     */
    public function getDaysInMonth(int $year, int $month): int
    {
        return $this->isLeapYear($year)
            ? self::DAYS_IN_MONTH_LEAP[$month]
            : self::DAYS_IN_MONTH_COMMON[$month];
    }

    /** @return int<1,12> */
    public function getMonthsInYear(int $year): int
    {
        return 12;
    }

    /** @param int<1,12> $month */
    public function getNameOfMonth(int $year, int $month): string
    {
        return match ($month) {
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        };
    }

    /** @param int<1,12> $month */
    public function getAbbreviationOfMonth(int $year, int $month): string
    {
        return \substr($this->getNameOfMonth($year, $month), 0, 3);
    }

    /**
     * @return array{int, int<1,12>, int<1,31>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array
    {
        $days += self::HINNANT_EPOCH_SHIFT;
        $era = \intdiv($days >= 0 ? $days : $days - self::HINNANT_DAYS_PER_ERA + 1, self::HINNANT_DAYS_PER_ERA);
        $dayOfEra = $days - $era * self::HINNANT_DAYS_PER_ERA;
        \assert($dayOfEra >= 0 && $dayOfEra < self::HINNANT_DAYS_PER_ERA);

        $yearOfEra = \intdiv(
            $dayOfEra - \intdiv($dayOfEra, 1460) + \intdiv($dayOfEra, 36524) - \intdiv($dayOfEra, 146096),
            self::DAYS_PER_YEAR_COMMON
        );
        \assert($yearOfEra >= 0 && $yearOfEra < self::HINNANT_YEARS_PER_ERA);

        $year = $yearOfEra + $era * self::HINNANT_YEARS_PER_ERA;
        $dayOfYear = $dayOfEra - (
            self::DAYS_PER_YEAR_COMMON * $yearOfEra
            + \intdiv($yearOfEra, 4)
            - \intdiv($yearOfEra, 100)
        );
        \assert($dayOfYear >= 0 && $dayOfYear < self::DAYS_PER_YEAR_LEAP);

        $monthPortion = \intdiv(5 * $dayOfYear + 2, 153);
        \assert($monthPortion >= 0 && $monthPortion <= 11);

        $day = $dayOfYear - \intdiv(153 * $monthPortion + 2, 5) + 1;
        \assert($day >= 1 && $day <= 31);

        $month = $monthPortion + ($monthPortion < 10 ? 3 : -9);
        \assert($month >= 1 && $month <= 12);

        $year += (int)($month <= 2);

        return [$year, $month, $day];
    }

    /**
     * @return array{int, int<1,12>, int<1,31>}
     */
    public function getYmdByUnixTimestamp(int $ts): array
    {
        $days = \intdiv($ts, self::SECONDS_PER_DAY);
        $days -= (int)(($ts % self::SECONDS_PER_DAY) < 0);

        return $this->getYmdByDaysSinceUnixEpoch($days);
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int
    {
        // adjust leap days to the end of the year and month between 0 and 11
        if ($month <= 2) {
            $year -= 1;
            $month += 9;
        } else {
            $month -= 3;
        }

        $era = \intdiv($year >= 0 ? $year : $year - self::HINNANT_YEARS_PER_ERA + 1, self::HINNANT_YEARS_PER_ERA);
        $yoe = $year - $era * self::HINNANT_YEARS_PER_ERA;
        \assert($yoe >= 0 && $yoe < self::HINNANT_YEARS_PER_ERA);

        $doy = \intdiv(153 * $month + 2, 5) + $dayOfMonth - 1;
        \assert($doy >= 0 && $doy < 366);

        $doe = $yoe * self::DAYS_PER_YEAR_COMMON + \intdiv($yoe, 4) - \intdiv($yoe, 100) + $doy;
        \assert($doe >= 0 && $doe < self::HINNANT_DAYS_PER_ERA);

        return $era * self::HINNANT_DAYS_PER_ERA + $doe - self::HINNANT_EPOCH_SHIFT;
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getUnixTimestampByYmd(int $year, int $month, int $dayOfMonth): int
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

        \assert($month > 0 && $month <= 12);
        \assert($dayOfMonth > 0 && $dayOfMonth <= 31);

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
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,366>
     */
    public function getDayOfYearByYmd(int $year, int $month, int $dayOfMonth): int
    {
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

    /**
     * @param int $year
     * @param int $month
     * @param int $dayOfMonth
     * @return array{int, int<1,12>, int<1,31>}
     */
    public function normalize(int $year, int $month, int $dayOfMonth): array
    {
        $year += \intdiv($month - 1, 12);
        $month = ($month - 1) % 12;
        if ($month < 0) {
            $year--;
            $month += 12;
        }
        $month += 1;

        if ($dayOfMonth >= 1) {
            while ($dayOfMonth > ($daysInMonth = $this->getDaysInMonth($year, $month)))  {
                $dayOfMonth -= $daysInMonth;
                $month++;
                if ($month > 12) {
                    $month = 1;
                    $year++;
                }
            }
        } else {
            do {
                $month--;
                if ($month < 1) {
                    $year--;
                    $month = 12;
                }

                $daysInMonth = $this->getDaysInMonth($year, $month);
                $dayOfMonth += $daysInMonth;
            } while ($dayOfMonth < 1);
        }
        \assert($dayOfMonth >= 1 && $dayOfMonth <= 31); // @phpstan-ignore smallerOrEqual.alwaysTrue

        return [$year, $month, $dayOfMonth];
    }
}
