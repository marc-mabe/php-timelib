<?php declare(strict_types=1);

namespace time;

final class LocalDateTime implements Date, Time {
    public int $year {
        get => GregorianCalendar::getYmdByUnixTimestamp($this->tsSec)[0];
    }

    public Month $month {
        get => GregorianCalendar::getYmdByUnixTimestamp($this->tsSec)[1];
    }

    public int $dayOfMonth {
        get => GregorianCalendar::getYmdByUnixTimestamp($this->tsSec)[2];
    }

    public int $dayOfYear  {
        get {
            $date = GregorianCalendar::getYmdByUnixTimestamp($this->tsSec);
            return GregorianCalendar::getDayOfYearByYmd($date[0], $date[1], $date[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => GregorianCalendar::getDayOfWeekByUnixTimestamp($this->tsSec);
    }

    public int $hour {
        get {
            $remainder = $this->tsSec % 86400;
            $remainder += ($remainder < 0) * 86400;
            return \intdiv($remainder, 3600);
        }
    }

    public int $minute  {
        get {
            $remainder = $this->tsSec % 86400;
            $remainder += ($remainder < 0) * 86400;
            $hours = \intdiv($remainder, 3600);
            return \intdiv($remainder - $hours * 3600, 60);
        }
    }

    public int $second  {
        get {
            $remainder = $this->tsSec % 86400;
            $remainder += ($remainder < 0) * 86400;
            return $remainder % 60;
        }
    }

    public int $milliOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000_000);
    }

    public int $microOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000);
    }

    public LocalDate $date {
        get => LocalDate::fromYd($this->year, $this->dayOfYear);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second);
    }

    /**
     * @param int<0, 999999999> $nanoOfSecond
     */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
    ) {}

    public function add(Duration $duration): self
    {
        $tuple = $duration->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self($tuple[0], $tuple[1]);
    }

    public function sub(Duration $duration): self
    {
        return $this->add($duration->inverted());
    }

    public static function fromDateTime(Date $date, Time $time): self
    {
        $ts = GregorianCalendar::getUnixTimestampByYmd($date->year, $date->month, $date->dayOfMonth);
        $ts += $time->hour * 3600 + $time->minute * 60 + $time->second;

        return new self($ts, $time->nanoOfSecond);
    }
}
