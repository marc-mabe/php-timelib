<?php declare(strict_types=1);

namespace time;

final class JulianCalendar implements Calendar
{
    private const int DAYS_PER_5_MONTHS = 153;
    private const int DAYS_PER_4_YEARS = 1461;

    private const int DAYS_PER_YEAR_COMMON = 365;
    private const int DAYS_PER_YEAR_LEAP   = 366;

    private const array DAYS_IN_MONTH_COMMON = [0, 31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];
    private const array DAYS_IN_MONTH_LEAP   = [0, 31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31];

    private const array DAYS_OF_YEAR_BY_MONTH_COMMON = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365];
    private const array DAYS_OF_YEAR_BY_MONTH_LEAP   = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366];

    private const int JDN_OFFSET = 32083;

    /**
     * @param int<1,7> $firstDayOfWeekIso  Defines the first day of the week using ISO numbering system
     *                                     (1: Mon, ... 7: Sun).
     * @param int<1,7> $minDaysInFirstWeek Defined the minimum number of days of the first week of the year.
     */
    public function __construct(
        public readonly int $firstDayOfWeekIso = 1,
        public readonly int $minDaysInFirstWeek = 4,
    ) {}

    public function isLeapYear(int $year): bool
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Julian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;
        return $year % 4 === 0;
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
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Julian calendar');
        }

        return 12;
    }

    /** @param int<1,12> $month */
    public function getMonthName(int $year, int $month): string
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Julian calendar');
        }

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
    public function getMonthAbbreviation(int $year, int $month): string
    {
        return \substr($this->getMonthName($year, $month), 0, 3);
    }

    /**
     * @return array{int, int<1,12>, int<1,31>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array
    {
        $isoCal = IsoCalendar::getInstance();
        $isoYmd = $isoCal->getYmdByDaysSinceUnixEpoch($days);
        $jdn    = $isoCal->getJdnByYmd(...$isoYmd);
        return $this->getYmdByJdn($jdn);
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $jdn    = $this->getJdnByYmd($year, $month, $dayOfMonth);
        $isoCal = IsoCalendar::getInstance();
        $isoYmd = $isoCal->getYmdByJdn($jdn);
        return $isoCal->getDaysSinceUnixEpochByYmd(...$isoYmd);
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int
    {
        $month       = 1;
        $dayOfMonth  = $dayOfYear;
        $daysInMonth = $this->getDaysInMonth($year, $month);
        while ($daysInMonth < $dayOfMonth) {
            $dayOfMonth -= $daysInMonth;
            $month++;
            \assert($month <= 12);
            $daysInMonth = $this->getDaysInMonth($year, $month);
        }
        \assert($dayOfMonth >= 1);

        $jdn    = $this->getJdnByYmd($year, $month, $dayOfMonth);
        $isoCal = IsoCalendar::getInstance();
        $isoYmd = $isoCal->getYmdByJdn($jdn);
        return $isoCal->getDaysSinceUnixEpochByYmd(...$isoYmd);
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
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Julian calendar');
        }

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
        $firstWeekMod    = $daysInFirstWeek < $this->minDaysInFirstWeek
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
        if (7 - $daysLastWeek >= $this->minDaysInFirstWeek
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
        $firstWeekMod    = $daysInFirstWeek < $this->minDaysInFirstWeek
            ? -$daysInFirstWeek
            : 7 - $daysInFirstWeek;
        $woy = (int)\ceil(($this->getDayOfYearByYmd($year, $month, $dayOfMonth) + $firstWeekMod) / 7);
        \assert($woy >= 0);

        // it's the last week of the previous year
        if ($woy === 0) {
            return $year === 1 ? -1 : $year - 1;
        }

        // check if the last days of the year are already part of the first week of the next year
        $daysInYear   = $this->getDaysInYear($year);
        $daysLastWeek = (($daysInYear + $firstWeekMod) % 7) ?: 7;
        if (7 - $daysLastWeek >= $this->minDaysInFirstWeek
            && $woy === (int)\ceil(($daysInYear + $firstWeekMod) / 7)
        ) {
            return $year === -1 ? 1 : $year + 1;
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

    /** @return int<1,7> */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int
    {
        // TODO: Double check if this is correct for Julian calendar
        // 1970-01-01 is a Wednesday
        $iso = $days % 7;                   // -6 (Thu) - 0 (Wed) - 6 (Tue)
        $iso = $iso < 0 ? $iso + 7 : $iso;  //  0 (Wed) - 6 (Tue)
        $iso = $iso - 4;                    // -4 (Wed) - 2 (Tue)
        $iso = $iso <= 0 ? $iso + 7 : $iso; //  1 (Mon) - 7 (Sun)

        $dow = $iso - $this->firstDayOfWeekIso;
        return $dow <= 0 ? $dow + 7 : $dow;
    }

    /**
     * Get the name of the given day-of-week.
     *
     * @param int<1,7> $dayOfWeek
     * @return non-empty-string
     */
    public function getDayOfWeekName(int $dayOfWeek): string
    {
        $iso = $dayOfWeek + $this->firstDayOfWeekIso;
        $iso = $iso > 7 ? $iso - 7 : $iso;

        return match ($iso) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        };
    }

    /**
     * Get the abbreviation of the given day-of-week.
     *
     * @param int<1,7> $dayOfWeek
     * @return non-empty-string
     */
    public function getDayOfWeekAbbreviation(int $dayOfWeek): string
    {
        return \substr($this->getDayOfWeekName($dayOfWeek), 0, 3);
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
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Julian calendar');
        }

        $year   = $year < 0 ? $year + 1 : $year;
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

        $year = $year <= 0 ? $year - 1 : $year;
        return [$year, $month, $day];
    }

    /** @return array{int, int<1,12>, int<1,31>} */
    public function getYmdByJdn(int|float $julianDay): array
    {
        if ($julianDay > \intdiv(PHP_INT_MAX - self::JDN_OFFSET * 4 + 1, 4)
            || $julianDay <= -self::JDN_OFFSET
        ) {
            throw new RangeError(\sprintf(
                'The julian day number must be between %s and %s',
                -self::JDN_OFFSET + 1,
                \intdiv(PHP_INT_MAX - self::JDN_OFFSET * 4 + 1, 4)
            ));
        }

        $temp = $julianDay * 4 + (self::JDN_OFFSET * 4 - 1);
        \assert(\is_int($temp));

        // Calculate the year and day-of-year (1 <= dayOfYear <= 366)
        $year = \intdiv($temp, self::DAYS_PER_4_YEARS);
        $doy  = \intdiv($temp % self::DAYS_PER_4_YEARS, 4) + 1;

        // Calculate the month and day of month
        $temp  = $doy * 5 - 3;
        $month = \intdiv($temp, self::DAYS_PER_5_MONTHS);
        $dom   = \intdiv($temp % self::DAYS_PER_5_MONTHS, 5) + 1;

        // Convert to the normal beginning of the year
        if ($month < 10) {
            $month += 3;
        } else {
            $year += 1;
            $month -= 9;
        }

        // Adjust the year
        $year -= 4800;
        $year = $year <= 0 ? $year - 1 : $year;

        \assert($month >= 1 && $month <= 12);
        \assert($dom >= 1 && $dom <= 31);
        return [$year, $month, $dom];
    }

    /** @param int<1,12> $month */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Julian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;

        // Adjust the year
        $year = $year + 4800;

        // Adjust the start of the year
        if ($month > 2) {
            $month = $month - 3;
        } else {
            $month = $month + 9;
            $year--;
        }

        return \intdiv($year * self::DAYS_PER_4_YEARS, 4)
            + \intdiv($month * self::DAYS_PER_5_MONTHS + 2, 5)
            + $dayOfMonth
            - self::JDN_OFFSET;
    }
}
