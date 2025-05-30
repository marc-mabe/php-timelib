<?php declare(strict_types=1);

namespace time;

final class LocalDate implements Date
{
    public int $year {
        get => $this->calendar->getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[0];
    }

    public int $month {
        get => $this->calendar->getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[1];
    }

    public int $dayOfMonth {
        get => $this->calendar->getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[2];
    }

    public int $dayOfYear {
        get {
            $date = $this->calendar->getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch);
            return $this->calendar->getDayOfYearByYmd($date[0], $date[1], $date[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => $this->calendar->getDayOfWeekByDaysSinceUnixEpoch($this->daysSinceEpoch);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->weekInfo->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => $this->weekInfo->getYearOfWeek($this);
    }

    private function __construct(
        private readonly int $daysSinceEpoch,
        public readonly Calendar $calendar,
        public readonly WeekInfo $weekInfo,
    ) {}

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->daysSinceEpoch, $calendar, $this->weekInfo);
    }

    public function withWeekInfo(WeekInfo $weekInfo): self
    {
        return $this->weekInfo === $weekInfo
            ? $this
            : new self($this->daysSinceEpoch, $this->calendar, $weekInfo);
    }

    /**
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     */
    public static function fromYmd(
        int $year,
        int $month,
        int $dayOfMonth,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        $calendar     ??= GregorianCalendar::getInstance();
        $daysSinceEpoch = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        return new self($daysSinceEpoch, $calendar, $weekInfo ?? WeekInfo::fromIso());
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public static function fromYd(
        int $year,
        int $dayOfYear,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        $calendar     ??= GregorianCalendar::getInstance();
        $daysSinceEpoch = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        return new self($daysSinceEpoch, $calendar, $weekInfo ?? WeekInfo::fromIso());
    }
}
