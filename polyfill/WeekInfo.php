<?php declare(strict_types=1);

namespace time;

final class WeekInfo
{
    /** @var \WeakMap<WeekInfo, null> */
    private static \WeakMap $cache;

    /**
     * @param int<1,7> $minDaysInFirstWeek
     */
    private function __construct(
        public readonly DayOfWeek $firstDayOfWeek,
        public readonly int $minDaysInFirstWeek,
    ) {}

    /**
     * @return int<1,max>
     */
    public function getWeekOfYear(Date $date): int
    {
        $jan1     = LocalDate::fromYmd($date->year, 1, 1, calendar: $date->calendar);
        $jan1Dist = $jan1->dayOfWeek->distance($this->firstDayOfWeek);

        $daysInFirstWeek = ($jan1Dist >= 0 ? $jan1Dist : 7 + $jan1Dist);
        $firstWeekMod    = $daysInFirstWeek < $this->minDaysInFirstWeek
            ? -$daysInFirstWeek
            : 7 - $daysInFirstWeek;
        $weekOfYear = (int)\ceil(($date->dayOfYear + $firstWeekMod) / 7);
        \assert($weekOfYear >= 0);

        // it's the last week of the previous year
        if ($weekOfYear === 0) {
            // TODO: There should be better ways of doing this
            //       especially without knowing the last day of the previous year of this calendar
            $prevDec31 = LocalDate::fromYmd($date->year - 1, 12, 31, calendar: $date->calendar);
            return $this->getWeekOfYear($prevDec31);
        }

        // check if the last days of the year are already part of the first week of the next year
        $daysInYear   = $date->calendar->getDaysInYear($date->year);
        $daysLastWeek = (($daysInYear + $firstWeekMod) % 7) ?: 7;
        if (7 - $daysLastWeek >= $this->minDaysInFirstWeek
            && $weekOfYear === (int)\ceil(($daysInYear + $firstWeekMod) / 7)
        ) {
            $weekOfYear = 1;
        }

        return $weekOfYear;
    }

    public function getYearOfWeek(Date $date): int
    {
        $jan1 = LocalDate::fromYmd($date->year, 1, 1, calendar: $date->calendar);
        $jan1Dist = $jan1->dayOfWeek->distance($this->firstDayOfWeek);

        $daysInFirstWeek = ($jan1Dist >= 0 ? $jan1Dist : 7 + $jan1Dist);
        $firstWeekMod    = $daysInFirstWeek < $this->minDaysInFirstWeek
            ? -$daysInFirstWeek
            : 7 - $daysInFirstWeek;
        $weekOfYear = (int)\ceil(($date->dayOfYear + $firstWeekMod) / 7);
        \assert($weekOfYear >= 0);

        if ($weekOfYear === 0) {
            return $date->year - 1;
        }

        // check if the last days of the year are already part of the first week of the next year
        $daysInYear   = $date->calendar->getDaysInYear($date->year);
        $daysLastWeek = (($daysInYear + $firstWeekMod) % 7) ?: 7;
        if (7 - $daysLastWeek >= $this->minDaysInFirstWeek
            && $weekOfYear === (int)\ceil(($daysInYear + $firstWeekMod) / 7)
        ) {
            return $date->year + 1;
        }

        return $date->year;
    }

    /**
     * @param int<1,7> $minDaysInFirstWeek
     */
    public static function from(DayOfWeek $firstDayOfWeek, int $minDaysInFirstWeek): self
    {
        if (!isset(self::$cache)) {
            /** @var \WeakMap<WeekInfo, null> $cache */
            $cache = new \WeakMap();
            self::$cache = $cache;
        }

        foreach (self::$cache as $weekInfo => $_) {
            if ($weekInfo->firstDayOfWeek === $firstDayOfWeek
                && $weekInfo->minDaysInFirstWeek === $minDaysInFirstWeek
            ) {
                return $weekInfo;
            }
        }

        $weekInfo = new self($firstDayOfWeek, $minDaysInFirstWeek);
        self::$cache->offsetSet($weekInfo, null);
        return $weekInfo;
    }

    public static function fromIso(): self
    {
        return self::from(DayOfWeek::Monday, 4);
    }
}
