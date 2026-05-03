<?php declare(strict_types=1);

namespace time;

final class HebrewCalendar implements Calendar
{
    private const int MODIFIED_JULIAN_DAY_OFFSET = 2400001;
    private const int UNIX_EPOCH_JDN = 2440588;
    private const int HEBREW_EPOCH_JDN = 347998;
    private const int DAYS_BETWEEN_UNIX_AND_HEBREW_EPOCH = self::UNIX_EPOCH_JDN - self::HEBREW_EPOCH_JDN;

    private const array MONTH_LABEL_WIDE_COMMON = [
        1 => 'Tishrei', 2 => 'Cheshvan', 3 => 'Kislev', 4 => 'Tevet', 5 => 'Shevat',
        6 => 'Adar', 7 => 'Nisan', 8 => 'Iyar', 9 => 'Sivan', 10 => 'Tammuz', 11 => 'Av', 12 => 'Elul',
    ];
    private const array MONTH_LABEL_WIDE_LEAP = [
        1 => 'Tishrei', 2 => 'Cheshvan', 3 => 'Kislev', 4 => 'Tevet', 5 => 'Shevat',
        6 => 'Adar I', 7 => 'Adar II', 8 => 'Nisan', 9 => 'Iyar', 10 => 'Sivan',
        11 => 'Tammuz', 12 => 'Av', 13 => 'Elul',
    ];
    private const array MONTH_LABEL_ABBREVIATION_COMMON = [
        1 => 'Tish', 2 => 'Ches', 3 => 'Kis', 4 => 'Tev', 5 => 'She', 6 => 'Ad', 7 => 'Nis',
        8 => 'Iy', 9 => 'Siv', 10 => 'Tam', 11 => 'Av', 12 => 'El',
    ];
    private const array MONTH_LABEL_ABBREVIATION_LEAP = [
        1 => 'Tish', 2 => 'Ches', 3 => 'Kis', 4 => 'Tev', 5 => 'She', 6 => 'Adi', 7 => 'Aii',
        8 => 'Nis', 9 => 'Iy', 10 => 'Siv', 11 => 'Tam', 12 => 'Av', 13 => 'El',
    ];
    private const array MONTH_LABEL_NARROW_COMMON = [
        1 => 'T', 2 => 'C', 3 => 'K', 4 => 'T', 5 => 'S', 6 => 'A', 7 => 'N', 8 => 'I',
        9 => 'S', 10 => 'T', 11 => 'A', 12 => 'E',
    ];
    private const array MONTH_LABEL_NARROW_LEAP = [
        1 => 'T', 2 => 'C', 3 => 'K', 4 => 'T', 5 => 'S', 6 => 'A', 7 => 'A', 8 => 'N', 9 => 'I',
        10 => 'S', 11 => 'T', 12 => 'A', 13 => 'E',
    ];

    private const array DAY_OF_WEEK_LABEL_WIDE = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];
    private const array DAY_OF_WEEK_LABEL_ABBREVIATION = [
        1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun',
    ];
    private const array DAY_OF_WEEK_LABEL_SHORT = [
        1 => 'Mo', 2 => 'Tu', 3 => 'We', 4 => 'Th', 5 => 'Fr', 6 => 'Sa', 7 => 'Su',
    ];
    private const array DAY_OF_WEEK_LABEL_NARROW = [
        1 => 'M', 2 => 'T', 3 => 'W', 4 => 'T', 5 => 'F', 6 => 'S', 7 => 'S',
    ];

    /**
     * @param int<1,7> $minDaysInFirstWeek Defines the minimum number of days of the first week of the year.
     */
    public function __construct(
        public readonly IsoDayOfWeek $firstDayOfWeek = IsoDayOfWeek::Sunday,
        public readonly int $minDaysInFirstWeek = 1,
    ) {}

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<1,max> $year
     */
    public function isLeapYear(int $year): bool
    {
        $this->assertYear($year);

        return (($year * 7 + 1) % 19) < 7;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @return int<353,355>|int<383,385>
     * @throws InvalidValueException
     */
    public function getDaysInYear(int $year): int
    {
        $this->assertYear($year);

        $daysInYear = $this->getDaysSinceTishrei1($year + 1) - $this->getDaysSinceTishrei1($year);

        \assert($this->isLeapYear($year)
            ? $daysInYear >= 383 && $daysInYear <= 385
            : $daysInYear >= 353 && $daysInYear <= 355
        );

        /** @var int<353,355>|int<383,385> $daysInYear */
        return $daysInYear;
    }

    public function hasYearZero(): false
    {
        return false;
    }

    public function getDayBoundary(): DayBoundary
    {
        return DayBoundary::Sunset;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @return int<29,30>
     * @throws InvalidValueException
     */
    public function getDaysInMonth(int $year, int $month): int
    {
        $this->assertMonthInYear($year, $month);

        return match ($month) {
            1 => 30,
            2 => $this->getDaysInYear($year) % 10 === 5 ? 30 : 29,
            3 => $this->getDaysInYear($year) % 10 === 3 ? 29 : 30,
            4 => 29,
            5 => 30,
            6 => $this->isLeapYear($year) ? 30 : 29,
            7 => $this->isLeapYear($year) ? 29 : 30,
            8 => $this->isLeapYear($year) ? 30 : 29,
            9 => $this->isLeapYear($year) ? 29 : 30,
            10 => $this->isLeapYear($year) ? 30 : 29,
            11 => $this->isLeapYear($year) ? 29 : 30,
            12 => $this->isLeapYear($year) ? 30 : 29,
            13 => 29,
        };
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @return int<12,13>
     * @throws InvalidValueException
     */
    public function getMonthsInYear(int $year): int
    {
        $this->assertYear($year);

        return $this->isLeapYear($year) ? 13 : 12;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @throws InvalidValueException
     */
    public function getMonthName(int $year, int $month): string
    {
        $this->assertMonthInYear($year, $month);

        return $this->isLeapYear($year)
            ? self::MONTH_LABEL_WIDE_LEAP[$month]
            : self::MONTH_LABEL_WIDE_COMMON[$month];
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @throws InvalidValueException
     */
    public function getMonthAbbreviation(int $year, int $month): string
    {
        $this->assertMonthInYear($year, $month);

        return $this->isLeapYear($year)
            ? self::MONTH_LABEL_ABBREVIATION_LEAP[$month]
            : self::MONTH_LABEL_ABBREVIATION_COMMON[$month];
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @throws InvalidValueException
     */
    public function getMonthNarrow(int $year, int $month): string
    {
        $this->assertMonthInYear($year, $month);

        return $this->isLeapYear($year)
            ? self::MONTH_LABEL_NARROW_LEAP[$month]
            : self::MONTH_LABEL_NARROW_COMMON[$month];
    }

    /**
     * @return array{int<1,max>,int<1,13>,int<1,30>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array
    {
        $daysSinceHebrewEpoch = $days + self::DAYS_BETWEEN_UNIX_AND_HEBREW_EPOCH;
        if ($daysSinceHebrewEpoch < 0) {
            throw new RangeError('Days must be on or after 1 Tishrei AM 1 for the Hebrew calendar');
        }

        $year = \intdiv($daysSinceHebrewEpoch, 366) + 1;
        if ($year < 1) {
            $year = 1;
        }

        $yearStart = $this->getDaysSinceTishrei1($year);
        while ($yearStart > $daysSinceHebrewEpoch) {
            $year--;
            $yearStart = $this->getDaysSinceTishrei1($year);
        }

        $nextYearStart = $this->getDaysSinceTishrei1($year + 1);
        while ($nextYearStart <= $daysSinceHebrewEpoch) {
            $year++;
            $nextYearStart = $this->getDaysSinceTishrei1($year + 1);
        }

        /** @var int<1,13> $month */
        $month = 1;
        $dayOfMonth = $daysSinceHebrewEpoch - $this->getDaysSinceTishrei1($year) + 1;
        while ($dayOfMonth > ($daysInMonth = $this->getDaysInMonth($year, $month))) {
            $dayOfMonth -= $daysInMonth;
            $month++;
            \assert($month <= 13);
        }

        \assert($dayOfMonth > 0);

        return [$year, $month, $dayOfMonth];
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $daysSinceHebrewEpoch = $this->getDaysSinceHebrewEpochByYmd($year, $month, $dayOfMonth);

        return $daysSinceHebrewEpoch - self::DAYS_BETWEEN_UNIX_AND_HEBREW_EPOCH;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,385> $dayOfYear
     * @throws InvalidValueException
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int
    {
        $daysInYear = $this->getDaysInYear($year);
        if ($dayOfYear > $daysInYear) {
            throw new InvalidValueException(
                "Day of year must be between 1 and {$daysInYear} for Hebrew year {$year}, {$dayOfYear} given",
            );
        }

        /** @var int<1,13> $month */
        $month = 1;
        $dayOfMonth = $dayOfYear;
        while ($dayOfMonth > ($daysInMonth = $this->getDaysInMonth($year, $month))) {
            $dayOfMonth -= $daysInMonth;
            $month++;
            \assert($month <= 13);
        }

        \assert($dayOfMonth > 0);

        return $this->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getDayOfYearByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertDayOfMonth($year, $month, $dayOfMonth);

        $dayOfYear = $dayOfMonth;
        for ($i = 1; $i < $month; $i++) {
            $dayOfYear += $this->getDaysInMonth($year, $i);
        }

        return $dayOfYear;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getDaysInWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertDayOfMonth($year, $month, $dayOfMonth);

        return 7;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
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

        if ($woy === 0) {
            if ($year === 1) {
                return 1;
            }

            $prevYear = $year - 1;
            $month = $this->getMonthsInYear($prevYear);

            return $this->getWeekOfYearByYmd(
                $prevYear,
                $month,
                $this->getDaysInMonth($prevYear, $month),
            );
        }

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
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getWeekOfMonthByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $firstDow = $this->getDayOfWeekByYmd($year, $month, 1);
        $daysInWeek = $this->getDaysInWeekByYmd($year, $month, $dayOfMonth);
        $firstDowIso = $this->localDayOfWeekToIso($firstDow);
        $firstWeekStartDay = (($this->firstDayOfWeek->value - $firstDowIso + $daysInWeek) % $daysInWeek) + 1;
        $daysBeforeFirstWeek = $firstWeekStartDay - 1;

        $weekOfMonth = match (true) {
            $this->minDaysInFirstWeek === 1 && $firstWeekStartDay === 1 => intdiv($dayOfMonth - 1, $daysInWeek) + 1,
            $this->minDaysInFirstWeek === 1 && $dayOfMonth < $firstWeekStartDay => 1,
            $this->minDaysInFirstWeek === 1 => intdiv($dayOfMonth - $firstWeekStartDay, $daysInWeek) + 2,
            $firstWeekStartDay === 1 => intdiv($dayOfMonth - 1, $daysInWeek) + 1,
            default => $dayOfMonth <= $daysBeforeFirstWeek ? 0 : intdiv($dayOfMonth - $firstWeekStartDay, $daysInWeek) + 1,
        };

        \assert($weekOfMonth >= 0);
        return $weekOfMonth;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
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

        if ($woy === 0) {
            return $year === 1 ? 1 : $year - 1;
        }

        $daysInYear   = $this->getDaysInYear($year);
        $daysLastWeek = (($daysInYear + $firstWeekMod) % 7) ?: 7;
        if (7 - $daysLastWeek >= $this->minDaysInFirstWeek
            && $woy === (int)\ceil(($daysInYear + $firstWeekMod) / 7)
        ) {
            return $year + 1;
        }

        return $year;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getDayOfWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertDayOfMonth($year, $month, $dayOfMonth);

        $daysSinceEpoch = $this->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        return $this->getDayOfWeekByDaysSinceUnixEpoch($daysSinceEpoch);
    }

    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekByDaysSinceUnixEpoch($days);
        $dow = $iso - ($this->firstDayOfWeek->value - 1);

        return $dow <= 0 ? $dow + 7 : $dow;
    }

    public function getDayOfWeekName(int $dayOfWeek): string
    {
        return self::DAY_OF_WEEK_LABEL_WIDE[$this->localDayOfWeekToIso($dayOfWeek)];
    }

    public function getDayOfWeekAbbreviation(int $dayOfWeek): string
    {
        return self::DAY_OF_WEEK_LABEL_ABBREVIATION[$this->localDayOfWeekToIso($dayOfWeek)];
    }

    public function getDayOfWeekShort(int $dayOfWeek): string
    {
        return self::DAY_OF_WEEK_LABEL_SHORT[$this->localDayOfWeekToIso($dayOfWeek)];
    }

    public function getDayOfWeekNarrow(int $dayOfWeek): string
    {
        return self::DAY_OF_WEEK_LABEL_NARROW[$this->localDayOfWeekToIso($dayOfWeek)];
    }

    public function getYmdByJdn(int|float $julianDay): array
    {
        return $this->getYmdByDaysSinceUnixEpoch((int)$julianDay - self::UNIX_EPOCH_JDN);
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int
    {
        return $this->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth) + self::UNIX_EPOCH_JDN;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    public function getMjdByYmd(int $year, int $month, int $dayOfMonth): float
    {
        return $this->getJdnByYmd($year, $month, $dayOfMonth) - self::MODIFIED_JULIAN_DAY_OFFSET;
    }

    public function getYmdByMjd(int|float $modifiedJulianDay): array
    {
        return $this->getYmdByJdn($modifiedJulianDay + self::MODIFIED_JULIAN_DAY_OFFSET);
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @throws InvalidValueException
     */
    public function getMonthNameMap(int $year): array
    {
        $months = [];
        $monthsInYear = $this->getMonthsInYear($year);
        for ($month = 1; $month <= $monthsInYear; $month++) {
            $months[$month] = $this->getMonthName($year, $month);
        }

        return $months;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @throws InvalidValueException
     */
    public function getMonthAbbreviationMap(int $year): array
    {
        $months = [];
        $monthsInYear = $this->getMonthsInYear($year);
        for ($month = 1; $month <= $monthsInYear; $month++) {
            $months[$month] = $this->getMonthAbbreviation($year, $month);
        }

        return $months;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @throws InvalidValueException
     */
    public function getMonthNarrowMap(int $year): array
    {
        $months = [];
        $monthsInYear = $this->getMonthsInYear($year);
        for ($month = 1; $month <= $monthsInYear; $month++) {
            $months[$month] = $this->getMonthNarrow($year, $month);
        }

        return $months;
    }

    /**
     * @return array<int<1,7>, non-empty-string>
     */
    public function getDayOfWeekAbbreviationMap(): array
    {
        $iso = self::DAY_OF_WEEK_LABEL_ABBREVIATION;
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    /**
     * @return array<int<1,7>, non-empty-string>
     */
    public function getDayOfWeekNameMap(): array
    {
        $iso = self::DAY_OF_WEEK_LABEL_WIDE;
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    /**
     * @return array<int<1,7>, non-empty-string>
     */
    public function getDayOfWeekNarrowMap(): array
    {
        $iso = self::DAY_OF_WEEK_LABEL_NARROW;
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    /**
     * @return array<int<1,7>, non-empty-string>
     */
    public function getDayOfWeekShortMap(): array
    {
        $iso = self::DAY_OF_WEEK_LABEL_SHORT;
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @return array{int<1,max>, int<1,13>, int<1,30>}
     * @throws InvalidValueException
     */
    public function addPeriodToYmd(
        Period $period,
        int $year,
        int $month,
        int $dayOfMonth,
    ): array {
        $this->assertDayOfMonth($year, $month, $dayOfMonth);

        $bias = $period->isNegative ? -1 : 1;
        $year = $year + $period->years * $bias;
        if ($year < 1) {
            throw new RangeError(
                "Resulting Hebrew year must be within the supported range, got {$year}",
            );
        }

        $month += $period->months * $bias;
        $monthsInYear = $this->getMonthsInYear($year);
        while ($month > $monthsInYear) {
            $month -= $monthsInYear;
            $year++;
            $monthsInYear = $this->getMonthsInYear($year);
        }
        while ($month < 1) {
            $year--;
            if ($year < 1) {
                throw new RangeError(
                    "Resulting Hebrew year must be within the supported range, got {$year}",
                );
            }
            $monthsInYear = $this->getMonthsInYear($year);
            $month += $monthsInYear;
        }

        $dayOfMonth = $dayOfMonth + $period->days * $bias + $period->weeks * 7 * $bias;
        $monthsInYear = $this->getMonthsInYear($year);
        if ($dayOfMonth >= 1) {
            while ($dayOfMonth > ($daysInMonth = $this->getDaysInMonth($year, $month))) {
                $dayOfMonth -= $daysInMonth;
                $month++;
                if ($month > $monthsInYear) {
                    $month = 1;
                    $year++;
                    $monthsInYear = $this->getMonthsInYear($year);
                }
            }

            \assert($dayOfMonth > 0);
        } else {
            do {
                $month--;
                if ($month < 1) {
                    $year--;
                    if ($year < 1) {
                        throw new RangeError(
                            "Resulting Hebrew year must be within the supported range, got {$year}",
                        );
                    }
                    $monthsInYear = $this->getMonthsInYear($year);
                    $month = $monthsInYear;
                }

                $dayOfMonth += $this->getDaysInMonth($year, $month);
            } while ($dayOfMonth < 1);
        }

        return [$year, $month, $dayOfMonth];
    }

    /**
     * @param int<1,max> $dayOfWeek
     * @return int<1,7>
     * @throws InvalidValueException
     */
    private function localDayOfWeekToIso(int $dayOfWeek): int
    {
        $this->assertDayOfWeek($dayOfWeek);

        $iso = $dayOfWeek + ($this->firstDayOfWeek->value - 1);
        $iso = $iso > 7 ? $iso - 7 : $iso;
        /** @var int<1,7> $iso */
        return $iso;
    }

    /**
     * @param int<1,99> $month
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    private function getDaysSinceHebrewEpochByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertDayOfMonth($year, $month, $dayOfMonth);

        $days = $this->getDaysSinceTishrei1($year);
        for ($i = 1; $i < $month; $i++) {
            $days += $this->getDaysInMonth($year, $i);
        }

        return $days + $dayOfMonth - 1;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @throws InvalidValueException
     */
    private function getDaysSinceTishrei1(int $year): int
    {
        $this->assertYear($year);

        $months = intdiv(235 * $year - 234, 19);
        $parts = 204 + 793 * ($months % 1080);
        $hours = 5 + 12 * $months + 793 * intdiv($months, 1080) + intdiv($parts, 1080);

        $days = 1 + 29 * $months + intdiv($hours, 24);
        $parts = $parts % 1080;
        $hours = $hours % 24;

        if ($hours >= 18) {
            $days++;
        }

        $dayOfWeek = $days % 7;
        if ($dayOfWeek === 0 || $dayOfWeek === 3 || $dayOfWeek === 5) {
            $days++;
        }

        if ($this->isLeapYear($year)) {
            if ($dayOfWeek === 1 && $hours >= 15 && $parts >= 589) {
                $days++;
            }
        } elseif ($dayOfWeek === 2 && $hours >= 9 && $parts >= 204) {
            $days++;
        }

        return $days - 1;
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @throws InvalidValueException
     */
    private function assertYear(int $year): void
    {
        if ($year < 1) {
            throw new InvalidValueException('Hebrew year must be 1 or greater');
        }
    }

    /**
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @throws InvalidValueException
     */
    private function assertMonthInYear(int $year, int $month): void
    {
        $this->assertYear($year);

        $monthsInYear = $this->getMonthsInYear($year);
        if ($month < 1 || $month > $monthsInYear) {
            throw new InvalidValueException(
                "Month must be within 1 and {$monthsInYear} for Hebrew year {$year}, {$month} given"
            );
        }
    }

    /**
     * @param int<1,99> $month
     * @phpstan-assert int<1,max> $year
     * @phpstan-assert int<1,13> $month
     * @phpstan-assert int<1,30> $dayOfMonth
     * @throws InvalidValueException
     */
    private function assertDayOfMonth(int $year, int $month, int $dayOfMonth): void
    {
        $maxDayOfMonth = $this->getDaysInMonth($year, $month);
        if ($dayOfMonth < 1 || $dayOfMonth > $maxDayOfMonth) {
            throw new InvalidValueException(
                "Day of month must be between 1 and {$maxDayOfMonth}} for Hebrew year {$year} and month {$month}, {$dayOfMonth} given",
            );
        }
    }

    /**
     * @param int<1,max> $dayOfWeek
     * @phpstan-assert int<1,7> $dayOfWeek
     * @throws InvalidValueException
     */
    private function assertDayOfWeek(int $dayOfWeek): void
    {
        if ($dayOfWeek > 7) {
            throw new InvalidValueException("Day of week must be between 1 and 7, {$dayOfWeek} given");
        }
    }
}
