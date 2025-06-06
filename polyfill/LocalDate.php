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
        $calendar     ??= new GregorianCalendar();
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
        $calendar     ??= new GregorianCalendar();
        $daysSinceEpoch = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        return new self($daysSinceEpoch, $calendar);
    }
}
