<?php declare(strict_types=1);

namespace time;

final class GregorianCalendar implements Calendar
{
    private const int MODIFIED_JULIAN_DAY_OFFSET = 2400001;

    /**
     * @param int<1,7> $minDaysInFirstWeek Defined the minimum number of days of the first week of the year.
     */
    public function __construct(
        public readonly IsoDayOfWeek $firstDayOfWeek = IsoDayOfWeek::Monday,
        public readonly int $minDaysInFirstWeek = 4,
    ) {}

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function isLeapYear(int $year): bool
    {
        $this->assertYear($year);

        return IsoCalendar::getInstance()->isLeapYear($year < 0 ? $year + 1 : $year);
    }

    /**
     * @return int<365,366>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function getDaysInYear(int $year): int
    {
        $this->assertYear($year);

        return IsoCalendar::getInstance()->getDaysInYear($year < 0 ? $year + 1 : $year);
    }

    public function hasYearZero(): false
    {
        return false;
    }

    public function getDayBoundary(): DayBoundary
    {
        return DayBoundary::Midnight;
    }

    /**
     * @return int<28,31>
     * @throws InvalidValueException
     * @phpstan-assert int<1,12> $month
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function getDaysInMonth(int $year, int $month): int
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        return IsoCalendar::getInstance()->getDaysInMonth($year < 0 ? $year + 1 : $year, $month);
    }

    /**
     * @return int<12,12>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function getMonthsInYear(int $year): int
    {
        $this->assertYear($year);

        return 12;
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getMonthName(int $year, int $month): string
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        return IsoCalendar::getInstance()->getMonthName($year < 0 ? $year + 1 : $year, $month);
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getMonthAbbreviation(int $year, int $month): string
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        return IsoCalendar::getInstance()->getMonthAbbreviation($year < 0 ? $year + 1 : $year, $month);
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getMonthNarrow(int $year, int $month): string
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        return IsoCalendar::getInstance()->getMonthNarrow($year < 0 ? $year + 1 : $year, $month);
    }

    /**
     * @return array{int, int<1,12>, int<1,31>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array
    {
        $ymd = IsoCalendar::getInstance()->getYmdByDaysSinceUnixEpoch($days);
        $ymd[0] = $ymd[0] <= 0 ? $ymd[0] - 1 : $ymd[0];

        return $ymd;
    }

    /**
     * @param int<1,31> $dayOfMonth
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertYear( $year);
        $this->assertMonthInYear($month);

        $year = $year < 0 ? $year + 1 : $year;

        return IsoCalendar::getInstance()->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @param int<1,max> $dayOfYear
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,366> $dayOfYear
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int
    {
        $daysInYear = $this->getDaysInYear($year);
        if ($dayOfYear > $daysInYear) {
            throw new InvalidValueException(sprintf(
                'Day of year must be between 1 and %d for Gregorian year %d, %d given.',
                $daysInYear,
                $year,
                $dayOfYear,
            ));
        }

        $year = $year < 0 ? $year + 1 : $year;
        return IsoCalendar::getInstance()->getDaysSinceUnixEpochByYd($year, $dayOfYear);
    }

    /**
     * @param int<1,31> $dayOfMonth
     * @return int<1,366>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getDayOfYearByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        $year = $year < 0 ? $year + 1 : $year;

        return IsoCalendar::getInstance()->getDayOfYearByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @param int<1,31> $dayOfMonth
     * @return int<7,7>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getDaysInWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        return 7;
    }

    /**
     * @param int<1,31> $dayOfMonth
     * @return int<1,53>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
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
            return $this->getWeekOfYearByYmd(
                $year === 1 ? -1 : $year - 1,
                 12, 
                 31,
            );
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
     * @param int<1,31> $dayOfMonth
     * @return int<0,max>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
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
     * @param int<1,31> $dayOfMonth
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
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
     * @param int<1,31> $dayOfMonth
     * @return int<1,7>
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getDayOfWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        $year = $year < 0 ? $year + 1 : $year;

        $iso = IsoCalendar::getInstance()->getDayOfWeekByYmd($year, $month, $dayOfMonth);
        $dow = $iso - ($this->firstDayOfWeek->value - 1);

        return $dow <= 0 ? $dow + 7 : $dow;
    }

    /** @return int<1,7> */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekByDaysSinceUnixEpoch($days);
        $dow = $iso - ($this->firstDayOfWeek->value - 1);

        return $dow <= 0 ? $dow + 7 : $dow;
    }

    /**
     * @param int<1,7> $dayOfWeek
     * @throws InvalidValueException
     */
    public function getDayOfWeekName(int $dayOfWeek): string
    {
        return IsoCalendar::getInstance()->getDayOfWeekName($this->localDayOfWeekToIso($dayOfWeek));
    }

    /**
     * @param int<1,7> $dayOfWeek
     * @throws InvalidValueException
     */
    public function getDayOfWeekAbbreviation(int $dayOfWeek): string
    {
        return IsoCalendar::getInstance()->getDayOfWeekAbbreviation($this->localDayOfWeekToIso($dayOfWeek));
    }

    /**
     * @param int<1,7> $dayOfWeek
     * @throws InvalidValueException
     */
    public function getDayOfWeekShort(int $dayOfWeek): string
    {
        return IsoCalendar::getInstance()->getDayOfWeekShort($this->localDayOfWeekToIso($dayOfWeek));
    }

    /**
     * @param int<1,7> $dayOfWeek
     * @throws InvalidValueException
     */
    public function getDayOfWeekNarrow(int $dayOfWeek): string
    {
        return IsoCalendar::getInstance()->getDayOfWeekNarrow($this->localDayOfWeekToIso($dayOfWeek));
    }

    /**
     * @param int<1,7> $dayOfWeek
     * @return int<1,7>
     */
    private function localDayOfWeekToIso(int $dayOfWeek): int
    {
        $iso = $dayOfWeek + ($this->firstDayOfWeek->value - 1);
        $iso = $iso > 7 ? $iso - 7 : $iso;
        /** @var int<1,7> $iso */

        return $iso;
    }

    /**
     * @param int<1,31> $dayOfMonth
     * @return array{int, int<1,12>, int<1,31>}
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function addPeriodToYmd(
        Period $period,
        int $year,
        int $month,
        int $dayOfMonth,
    ): array {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        if ($year < 0) {
            $year += 1;
            $bc = true;
        } else {
            $bc = false;
        }

        $ymd = IsoCalendar::getInstance()->addPeriodToYmd(
            $period,
            $year,
            $month,
            $dayOfMonth,
        );

        if ($bc) {
            $ymd[0] -= 1;
        }

        if ($ymd[0] === 0) {
            $ymd[0] -= 1;
        }

        return $ymd;
    }

    /** @return array{int, int<1,12>, int<1,31>} */
    public function getYmdByJdn(int|float $julianDay): array
    {
        $ymd = IsoCalendar::getInstance()->getYmdByJdn($julianDay);

        if ($ymd[0] <= 0) {
            $ymd[0] -= 1;
        }

        return $ymd;
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
     */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int
    {
        $this->assertYear($year);
        $this->assertMonthInYear($month);

        $year = $year < 0 ? $year + 1 : $year;
        return IsoCalendar::getInstance()->getJdnByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @phpstan-assert int<1,12> $month
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
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function getMonthNameMap(int $year): array
    {
        $this->assertYear($year);

        return IsoCalendar::getInstance()->getMonthNameMap($year < 0 ? $year + 1 : $year);
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function getMonthAbbreviationMap(int $year): array
    {
        $this->assertYear($year);

        return IsoCalendar::getInstance()->getMonthAbbreviationMap($year < 0 ? $year + 1 : $year);
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<min,-1>|int<1,max> $year
     */
    public function getMonthNarrowMap(int $year): array
    {
        $this->assertYear($year);

        return IsoCalendar::getInstance()->getMonthNarrowMap($year < 0 ? $year + 1 : $year);
    }

    public function getDayOfWeekAbbreviationMap(): array
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekAbbreviationMap();
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    public function getDayOfWeekNameMap(): array
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekNameMap();
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    public function getDayOfWeekNarrowMap(): array
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekNarrowMap();
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    public function getDayOfWeekShortMap(): array
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekShortMap();
        $map = [];
        for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
            $map[$dayOfWeek] = $iso[$this->localDayOfWeekToIso($dayOfWeek)];
        }

        return $map;
    }

    /**
     * @phpstan-assert int<min,-1>|int<1,max> $year
     * @throws InvalidValueException
     */
    private function assertYear(int $year): void
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the Gregorian calendar');
        }
    }

    /**
     * @throws InvalidValueException
     * @phpstan-assert int<1,12> $month
     */
    private function assertMonthInYear(int $month): void
    {
        if ($month < 1 || $month > 12) {
            throw new InvalidValueException("Month must be within 1 and 12, {$month} given.");
        }
    }
}
