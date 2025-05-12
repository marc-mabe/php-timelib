<?php declare(strict_types=1);

namespace time;

final class LocalDateTime implements Date, Time
{
    public int $year {
        get => $this->calendar->getYmdByUnixTimestamp($this->tsSec)[0];
    }

    public Month $month {
        get => $this->calendar->getYmdByUnixTimestamp($this->tsSec)[1];
    }

    public int $dayOfMonth {
        get => $this->calendar->getYmdByUnixTimestamp($this->tsSec)[2];
    }

    public int $dayOfYear  {
        get {
            $date = $this->calendar->getYmdByUnixTimestamp($this->tsSec);
            return $this->calendar->getDayOfYearByYmd($date[0], $date[1], $date[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => $this->calendar->getDayOfWeekByUnixTimestamp($this->tsSec);
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
        get => LocalDate::fromYd($this->year, $this->dayOfYear, calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->weekInfo->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => $this->weekInfo->getYearOfWeek($this);
    }

    /**
     * @param int<0, 999999999> $nanoOfSecond
     */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
        public readonly Calendar $calendar,
        public readonly WeekInfo $weekInfo,
    ) {}

    public function add(Duration $duration): self
    {
        $tuple = $duration->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self($tuple[0], $tuple[1], $this->calendar, $this->weekInfo);
    }

    public function sub(Duration $duration): self
    {
        return $this->add($duration->inverted());
    }

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->tsSec, $this->nanoOfSecond, $calendar, $this->weekInfo);
    }

    public function withWeekInfo(WeekInfo $weekInfo): self
    {
        return $this->weekInfo === $weekInfo
            ? $this
            : new self($this->tsSec, $this->nanoOfSecond, $this->calendar, $weekInfo);
    }

    public static function fromDateTime(Date $date, Time $time): self
    {
        $ts = $date->calendar->getUnixTimestampByYmd($date->year, $date->month, $date->dayOfMonth);
        $ts += $time->hour * 3600 + $time->minute * 60 + $time->second;

        return new self($ts, $time->nanoOfSecond, $date->calendar, $date->weekInfo);
    }
}
