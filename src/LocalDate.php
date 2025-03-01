<?php declare(strict_types=1);

namespace time;

final class LocalDate implements Date
{
    public int $year {
        get => GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[0];
    }

    public Month $month {
        get => GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[1];
    }

    public int $dayOfMonth {
        get => GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch)[2];
    }

    public int $dayOfYear {
        get {
            $date = GregorianCalendar::getYmdByDaysSinceUnixEpoch($this->daysSinceEpoch);
            return GregorianCalendar::getDayOfYearByYmd($date[0], $date[1], $date[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => GregorianCalendar::getDayOfWeekByDaysSinceUnixEpoch($this->daysSinceEpoch);
    }

    private function __construct(
        private readonly int $daysSinceEpoch
    ) {}

    /**
     * @param Month|int<1,12> $month
     * @param int<1,31> $dayOfMonth
     */
    public static function fromYmd(int $year, Month|int $month, int $dayOfMonth): self
    {
        $daysSinceEpoch = GregorianCalendar::getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        return new self($daysSinceEpoch);
    }

    /**
     * @param int<1,366> $dayOfYear
     */
    public static function fromYd(int $year, int $dayOfYear): self
    {
        $daysSinceEpoch = GregorianCalendar::getDaysSinceUnixEpochByYd($year, $dayOfYear);
        return new self($daysSinceEpoch);
    }
}
