<?php declare(strict_types=1);

namespace time;

final class IsoCalendar implements Calendar
{
    private const int DAYS_PER_YEAR_COMMON = 365;
    private const int DAYS_PER_YEAR_LEAP   = 366;

    private const array DAYS_IN_MONTH_COMMON = [0, 31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];
    private const array DAYS_IN_MONTH_LEAP   = [0, 31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];

    private const array DAYS_OF_YEAR_BY_MONTH_COMMON = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365];
    private const array DAYS_OF_YEAR_BY_MONTH_LEAP   = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366];

    /**
     * English civil month names shared by ISO, Gregorian, and Julian calendars in this library.
     *
     * @var array<int<1,12>, non-empty-string>
     */
    private const array MONTH_LABEL_WIDE = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    /** @var array<int<1,12>, non-empty-string> */
    private const array MONTH_LABEL_ABBREVIATION = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
    ];

    /** @var array<int<1,12>, non-empty-string> */
    private const array MONTH_LABEL_NARROW = [
        1 => 'J', 2 => 'F', 3 => 'M', 4 => 'A', 5 => 'M', 6 => 'J',
        7 => 'J', 8 => 'A', 9 => 'S', 10 => 'O', 11 => 'N', 12 => 'D',
    ];

    /** ISO 8601 weekday: 1 = Monday … 7 = Sunday. @var array<int<1,7>, non-empty-string> */
    private const array DAY_OF_WEEK_LABEL_WIDE = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];

    /** @var array<int<1,7>, non-empty-string> */
    private const array DAY_OF_WEEK_LABEL_ABBREVIATION = [
        1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun',
    ];

    /** @var array<int<1,7>, non-empty-string> */
    private const array DAY_OF_WEEK_LABEL_SHORT = [
        1 => 'Mo', 2 => 'Tu', 3 => 'We', 4 => 'Th', 5 => 'Fr', 6 => 'Sa', 7 => 'Su',
    ];

    /** @var array<int<1,7>, non-empty-string> */
    private const array DAY_OF_WEEK_LABEL_NARROW = [
        1 => 'M', 2 => 'T', 3 => 'W', 4 => 'T', 5 => 'F', 6 => 'S', 7 => 'S',
    ];

    private const int HINNANT_YEARS_PER_ERA = 400;

    private const int HINNANT_LEAP_YEARS_PER_ERA = 97;

    private const int HINNANT_DAYS_PER_ERA = self::HINNANT_YEARS_PER_ERA * self::DAYS_PER_YEAR_COMMON
        + self::HINNANT_LEAP_YEARS_PER_ERA;

    /**
     * Number of days between Hinnant epoch (0000-03-01) and unix epoch (1970-01-01)
     */
    private const int HINNANT_EPOCH_SHIFT = 719468;

    private const int MIN_DAYS_IN_FIRST_WEEK = 4;

    private const int JDN_OFFSET = 32045;

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

    public function hasYearZero(): bool
    {
        return true;
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

    public function getMonthName(int $year, int $month): string
    {
        $this->assertMonthInYear($year, $month);

        return self::MONTH_LABEL_WIDE[$month];
    }

    public function getMonthAbbreviation(int $year, int $month): string
    {
        $this->assertMonthInYear($year, $month);

        return self::MONTH_LABEL_ABBREVIATION[$month];
    }

    public function getMonthNarrow(int $year, int $month): string
    {
        $this->assertMonthInYear($year, $month);

        return self::MONTH_LABEL_NARROW[$month];
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

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<7,7>
     */
    public function getDaysInWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        return 7;
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,53>
     */
    public function getWeekOfYearByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $firstDate = [$year, 1, 1];
        $firstDow  = $this->getDayOfWeekByYmd(...$firstDate);

        $daysInFirstWeek = 7 - ($firstDow - 1);
        $firstWeekMod    = $daysInFirstWeek < self::MIN_DAYS_IN_FIRST_WEEK
            ? -$daysInFirstWeek
            : 7 - $daysInFirstWeek;
        $woy = (int)\ceil(($this->getDayOfYearByYmd($year, $month, $dayOfMonth) + $firstWeekMod) / 7);
        \assert($woy >= 0);

        // it's the last week of the previous year
        if ($woy === 0) {
            return $this->getWeekOfYearByYmd($year - 1, 12, 31);
        }

        // check if the last days of the year are already part of the first week of the next year
        $daysInYear   = $this->getDaysInYear($year);
        $daysLastWeek = (($daysInYear + $firstWeekMod) % 7) ?: 7;
        if (7 - $daysLastWeek >= self::MIN_DAYS_IN_FIRST_WEEK
            && $woy === (int)\ceil(($daysInYear + $firstWeekMod) / 7)
        ) {
            $woy = 1;
        }

        \assert($woy <= 53);
        return $woy;
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getYearOfWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $firstDate = [$year, 1, 1];
        $firstDow  = $this->getDayOfWeekByYmd(...$firstDate);

        $daysInFirstWeek = 7 - ($firstDow - 1);
        $firstWeekMod    = $daysInFirstWeek < self::MIN_DAYS_IN_FIRST_WEEK
            ? -$daysInFirstWeek
            : 7 - $daysInFirstWeek;
        $woy = (int)\ceil(($this->getDayOfYearByYmd($year, $month, $dayOfMonth) + $firstWeekMod) / 7);
        \assert($woy >= 0);

        // it's the last week of the previous year
        if ($woy === 0) {
            return $year - 1;
        }

        // check if the last days of the year are already part of the first week of the next year
        $daysInYear   = $this->getDaysInYear($year);
        $daysLastWeek = (($daysInYear + $firstWeekMod) % 7) ?: 7;
        if (7 - $daysLastWeek >= self::MIN_DAYS_IN_FIRST_WEEK
            && $woy === (int)\ceil(($daysInYear + $firstWeekMod) / 7)
        ) {
            return $year + 1;
        }

        return $year;
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,7>
     */
    public function getDayOfWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $daysSinceEpoch = $this->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        return $this->getDayOfWeekByDaysSinceUnixEpoch($daysSinceEpoch);
    }

    /** @return int<1,7> 1: Mon, ... 7: Sun */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int
    {
        // 1970-01-01 is a Thursday
        $dow = $days % 7;                   // -6 (Fri) - 0 (Thu) - 6 (Wed)
        $dow = $dow < 0 ? $dow + 7 : $dow;  //  0 (Thu) - 6 (Wed)
        $dow = $dow - 3;                    // -3 (Thu) - 3 (Wed)
        $dow = $dow <= 0 ? $dow + 7 : $dow; //  1 (Mon) - 7 (Sun)

        return $dow;
    }

    public function getDayOfWeekName(int $dayOfWeek): string
    {
        $this->assertIsoDayOfWeek($dayOfWeek);

        return self::DAY_OF_WEEK_LABEL_WIDE[$dayOfWeek];
    }

    public function getDayOfWeekAbbreviation(int $dayOfWeek): string
    {
        $this->assertIsoDayOfWeek($dayOfWeek);

        return self::DAY_OF_WEEK_LABEL_ABBREVIATION[$dayOfWeek];
    }

    public function getDayOfWeekShort(int $dayOfWeek): string
    {
        $this->assertIsoDayOfWeek($dayOfWeek);

        return self::DAY_OF_WEEK_LABEL_SHORT[$dayOfWeek];
    }

    public function getDayOfWeekNarrow(int $dayOfWeek): string
    {
        $this->assertIsoDayOfWeek($dayOfWeek);

        return self::DAY_OF_WEEK_LABEL_NARROW[$dayOfWeek];
    }

    /**
     * @param int $referenceYear Unused for ISO; required by {@see Calendar}.
     *
     * @return array<int<1,12>, non-empty-string>
     */
    public function getMonthNameMap(int $referenceYear): array
    {
        return self::MONTH_LABEL_WIDE;
    }

    /**
     * @param int $referenceYear Unused for ISO; required by {@see Calendar}.
     *
     * @return array<int<1,12>, non-empty-string>
     */
    public function getMonthAbbreviationMap(int $referenceYear): array
    {
        return self::MONTH_LABEL_ABBREVIATION;
    }

    /**
     * @param int $referenceYear Unused for ISO; required by {@see Calendar}.
     *
     * @return array<int<1,12>, non-empty-string>
     */
    public function getMonthNarrowMap(int $referenceYear): array
    {
        return self::MONTH_LABEL_NARROW;
    }

    /** @return array<int<1,7>, non-empty-string> */
    public function getDayOfWeekAbbreviationMap(): array
    {
        return self::DAY_OF_WEEK_LABEL_ABBREVIATION;
    }

    /** @return array<int<1,7>, non-empty-string> */
    public function getDayOfWeekNameMap(): array
    {
        return self::DAY_OF_WEEK_LABEL_WIDE;
    }

    /** @return array<int<1,7>, non-empty-string> */
    public function getDayOfWeekNarrowMap(): array
    {
        return self::DAY_OF_WEEK_LABEL_NARROW;
    }

    /** @return array<int<1,7>, non-empty-string> */
    public function getDayOfWeekShortMap(): array
    {
        return self::DAY_OF_WEEK_LABEL_SHORT;
    }

    private function assertMonthInYear(int $year, int $month): void
    {
        $max = $this->getMonthsInYear($year);
        if ($month < 1 || $month > $max) {
            throw new InvalidValueException("Month must be within 1 and {$max}, {$month} given.");
        }
    }

    private function assertIsoDayOfWeek(int $dayOfWeek): void
    {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            throw new InvalidValueException("Day of week must be within 1 and 7, {$dayOfWeek} given.");
        }
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return array{int, int<1,12>, int<1,31>}
     */
    public function addPeriodToYmd(
        Period $period,
        int $year,
        int $month,
        int $dayOfMonth,
    ): array {
        $bias   = $period->isNegative ? -1 : 1;
        $year   = $year + $period->years * $bias;
        $month  = $month + $period->months * $bias;
        $day    = $dayOfMonth
            + $period->days * $bias
            + $period->weeks * 7 * $bias;

        $year += \intdiv($month - 1, 12);
        $month = ($month - 1) % 12;
        if ($month < 0) {
            $year--;
            $month += 12;
        }
        $month += 1;

        if ($day >= 1) {
            while ($day > ($daysInMonth = $this->getDaysInMonth($year, $month)))  {
                $day -= $daysInMonth;
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
                $day += $daysInMonth;
            } while ($day < 1);
        }
        \assert($day >= 1 && $day <= 31); // @phpstan-ignore smallerOrEqual.alwaysTrue

        return [$year, $month, $day];
    }

    /** @return array{int, int<1,12>, int<1,31>} */
    public function getYmdByJdn(int|float $julianDay): array
    {
        $daysPer5Month = 153;
        $daysPer4Years = 1461;

        if ($julianDay > \intdiv(PHP_INT_MAX - 4 * self::JDN_OFFSET, 4)
            || $julianDay <= -self::JDN_OFFSET
        ) {
            throw new RangeError(\sprintf(
                'Julian day number must be between %s and %s',
                -self::JDN_OFFSET + 1,
                \intdiv(PHP_INT_MAX - 4 * self::JDN_OFFSET, 4)
            ));
        }

        $julianDay = (int)$julianDay;
        $temp      = ($julianDay + self::JDN_OFFSET) * 4 - 1;
        $century   = \intdiv($temp, self::HINNANT_DAYS_PER_ERA);

        // Calculate the year and day of year (1 <= dayOfYear <= 366)
        $temp = \intdiv($temp % self::HINNANT_DAYS_PER_ERA, 4) * 4 + 3;
        $year = ($century * 100) + \intdiv($temp, $daysPer4Years) - 4800;
        $dayOfYear = \intdiv(($temp % $daysPer4Years), 4) + 1;

        /* Calculate the month and day of month. */
        $temp  = $dayOfYear * 5 - 3;
        $month = \intdiv($temp, $daysPer5Month);
        $dom   = \intdiv(($temp % $daysPer5Month), 5) + 1;

        // Convert to the normal beginning of the year
        if ($month < 10) {
            $month += 3;
        } else {
            $year += 1;
            $month -= 9;
        }

        \assert($month >= 1 && $month <= 12);
        \assert($dom >= 1 && $dom <= 31);
        return [$year, $month, $dom];
    }

    /** @param int<1,12> $month */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $daysPer5Month = 153;
        $daysPer4Years = 1461;

        // Adjust the year
        $year = $year + 4800;

        // Adjust the start of the year
        if ($month > 2) {
            $month = $month - 3;
        } else {
            $month = $month + 9;
            $year--;
        }

        return \intdiv((\intdiv($year, 100) * self::HINNANT_DAYS_PER_ERA), 4)
            + \intdiv((($year % 100) * $daysPer4Years), 4)
            + \intdiv(($month * $daysPer5Month + 2), 5)
            + $dayOfMonth
            - self::JDN_OFFSET;
    }
}
