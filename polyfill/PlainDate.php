<?php declare(strict_types=1);

namespace time;

final class PlainDate implements Date
{
    /** @var array{int, int<1,99>, int<1,31>}  */
    private array $ymd {
        get {
            return $this->ymd ??= $this->calendar->getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch);
        }
    }

    public int $year {
        get => $this->ymd[0];
    }

    public int $month {
        get => $this->ymd[1];
    }

    public int $dayOfMonth {
        get => $this->ymd[2];
    }

    public int $dayOfYear {
        get => $this->calendar->getDayOfYearByYmd(...$this->ymd);
    }

    public int $dayOfWeek {
        get => $this->calendar->getDayOfWeekByDaysSinceUnixEpoch($this->daysSinceEpoch);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->calendar->getWeekOfYearByYmd($this->year, $this->month, $this->dayOfMonth);
    }

    public int $yearOfWeek {
        get => $this->calendar->getYearOfWeekByYmd($this->year, $this->month, $this->dayOfMonth);
    }

    private function __construct(
        private readonly int $daysSinceEpoch,
        public readonly Calendar $calendar,
    ) {}

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->daysSinceEpoch, $calendar);
    }

    public function add(Period $period): self {
        $ymd = $this->calendar->addPeriodToYmd($period,...$this->ymd);
        return self::fromYmd(
            year: $ymd[0],
            month: $ymd[1],
            dayOfMonth: $ymd[2],
            calendar: $this->calendar,
        );
    }

    public function sub(Period $period): self {
        return $this->add($period->inverted());
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
    ): self {
        $calendar     ??= IsoCalendar::getInstance();
        $daysSinceEpoch = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        return new self($daysSinceEpoch, $calendar);
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public static function fromYd(
        int $year,
        int $dayOfYear,
        ?Calendar $calendar = null,
    ): self {
        $calendar     ??= IsoCalendar::getInstance();
        $daysSinceEpoch = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        return new self($daysSinceEpoch, $calendar);
    }
}
