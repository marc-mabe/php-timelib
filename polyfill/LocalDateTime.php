<?php declare(strict_types=1);

namespace time;

final class LocalDateTime implements Date, Time
{
    /** @var null|array{int, int<1,99>, int<1,31>}  */
    private ?array $ymd = null;

    public int $year {
        get => ($this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->tsSec))[0];
    }

    public int $month {
        get => ($this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->tsSec))[1];
    }

    public int $dayOfMonth {
        get => ($this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->tsSec))[2];
    }

    public int $dayOfYear  {
        get {
            $this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->tsSec);
            return $this->calendar->getDayOfYearByYmd($this->ymd[0], $this->ymd[1], $this->ymd[2]);
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
        get {
            $this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->tsSec);
            return LocalDate::fromYmd($this->ymd[0], $this->ymd[1], $this->ymd[2], calendar: $this->calendar);
        }
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

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Period) {
            $this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->tsSec);

            $ymdHms = $durationOrPeriod->addToYmd(
                $this->ymd[0],
                $this->ymd[1],
                $this->ymd[2],
                $this->hour,
                $this->minute,
                $this->second,
                $this->nanoOfSecond,
                calendar: $this->calendar,
            );
            return self::fromYmd(
                $ymdHms[0],
                $ymdHms[1],
                $ymdHms[2],
                $ymdHms[3],
                $ymdHms[4],
                $ymdHms[5],
                $ymdHms[6],
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            );
        }

        $tuple = $durationOrPeriod->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self($tuple[0], $tuple[1], $this->calendar, $this->weekInfo);
    }

    public function sub(Duration|Period $durationOrPeriod): self
    {
        return $this->add($durationOrPeriod->inverted());
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

    /**
     * @param int<1, 99> $month
     * @param int<1, 31> $dayOfMonth
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999999> $nanoOfSecond
     * @return self
     */
    public static function fromYmd(
        int $year,
        int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYmd($year, $month, $dayOfMonth);
        $ts += $hour * 3600 + $minute * 60 + $second;

        $ldt = new self($ts, $nanoOfSecond, calendar: $calendar, weekInfo: $weekInfo ?? WeekInfo::fromIso());
        $ldt->ymd = [$year, $month, $dayOfMonth];

        return $ldt;
    }

    /**
     * @param int<1, 366> $dayOfYear
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999999> $nanoOfSecond
     */
    public static function fromYd(
        int $year,
        int $dayOfYear,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYd($year, $dayOfYear);
        $ts += $hour * 3600 + $minute * 60 + $second;

        return new self($ts, $nanoOfSecond, calendar: $calendar, weekInfo: $weekInfo ?? WeekInfo::fromIso());
    }

    public static function fromDateTime(Date $date, Time $time): self
    {
        $ts = $date->calendar->getUnixTimestampByYmd($date->year, $date->month, $date->dayOfMonth);
        $ts += $time->hour * 3600 + $time->minute * 60 + $time->second;

        $ldt = new self($ts, $time->nanoOfSecond, $date->calendar, $date->weekInfo);
        $ldt->ymd = [$date->year, $date->month, $date->dayOfMonth];

        return $ldt;
    }
}
