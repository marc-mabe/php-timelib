<?php declare(strict_types=1);

namespace time;

final class GregorianCalendar implements Calendar
{
    /**
     * @param int<1,7> $firstDayOfIsoWeek  Defines the first day of the week using ISO numbering system
     *                                     (1: Mon, ... 7: Sun).
     * @param int<1,7> $minDaysInFirstWeek Defined the minimum number of days of the first week of the year.
     */
    public function __construct(
        public readonly int $firstDayOfIsoWeek  = 1,
        public readonly int $minDaysInFirstWeek = 4,
    ) {}

    public function isLeapYear(int $year): bool
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        return IsoCalendar::getInstance()->isLeapYear($year < 0 ? $year + 1 : $year);
    }

    /**
     * @return int<365,366>
     */
    public function getDaysInYear(int $year): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        return IsoCalendar::getInstance()->getDaysInYear($year < 0 ? $year + 1 : $year);
    }

    /**
     * @param int<1,12> $month
     * @return int<28,31>
     */
    public function getDaysInMonth(int $year, int $month): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        return IsoCalendar::getInstance()->getDaysInMonth($year < 0 ? $year + 1 : $year, $month);
    }

    /** @return int<1,12> */
    public function getMonthsInYear(int $year): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        return 12;
    }

    /** @param int<1,12> $month */
    public function getMonthName(int $year, int $month): string
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        return IsoCalendar::getInstance()->getMonthName($year < 0 ? $year + 1 : $year, $month);
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
        $ymd = IsoCalendar::getInstance()->getYmdByDaysSinceUnixEpoch($days);
        $ymd[0] = $ymd[0] <= 0 ? $ymd[0] - 1 : $ymd[0];

        return $ymd;
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;

        return IsoCalendar::getInstance()->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;

        return IsoCalendar::getInstance()->getDaysSinceUnixEpochByYd($year, $dayOfYear);
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,366>
     */
    public function getDayOfYearByYmd(int $year, int $month, int $dayOfMonth): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;

        return IsoCalendar::getInstance()->getDayOfYearByYmd($year, $month, $dayOfMonth);
    }

    /**
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     * @return int<7,7>
     */
    public function getDaysInWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
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
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

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
     * @param int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getYearOfWeekByYmd(int $year, int $month, int $dayOfMonth): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

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
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;

        $iso = IsoCalendar::getInstance()->getDayOfWeekByYmd($year, $month, $dayOfMonth);
        $dow = $iso - ($this->firstDayOfIsoWeek - 1);

        return $dow <= 0 ? $dow + 7 : $dow;
    }

    /** @return int<1,7> */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int
    {
        $iso = IsoCalendar::getInstance()->getDayOfWeekByDaysSinceUnixEpoch($days);
        $dow = $iso - ($this->firstDayOfIsoWeek - 1);

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
        $iso = $dayOfWeek + ($this->firstDayOfIsoWeek - 1);
        $iso = $iso > 7 ? $iso - 7 : $iso;

        return IsoCalendar::getInstance()->getDayOfWeekName($iso);
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
     * @return array{int, int<1,12>, int<1,31>, int<0,23>, int<0,59>, int<0,59>, int<0,999999999>}
     */
    public function addPeriodToYmd(
        Period $period,
        int $year,
        int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): array {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        if ($year < 0) {
            $year += 1;
            $bc = true;
        } else {
            $bc = false;
        }

        $ymdHis = IsoCalendar::getInstance()->addPeriodToYmd(
            $period,
            $year,
            $month,
            $dayOfMonth,
            $hour,
            $minute,
            $second,
            $nanoOfSecond
        );

        if ($bc) {
            $ymdHis[0] -= 1;
        }

        if ($ymdHis[0] === 0) {
            $ymdHis[0] -= 1;
        }

        return $ymdHis;
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

    /** @param int<1,12> $month */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int
    {
        if ($year === 0) {
            throw new InvalidValueException('Year zero does not exist in the gregorian calendar');
        }

        $year = $year < 0 ? $year + 1 : $year;
        return IsoCalendar::getInstance()->getJdnByYmd($year, $month, $dayOfMonth);
    }
}
