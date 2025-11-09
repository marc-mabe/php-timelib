<?php declare(strict_types=1);

namespace time;

use IntlDateFormatter;

final class LocalDateTime implements Date, Time
{
    public const int SECONDS_PER_DAY = 24 * 3600;

    /** @var array{int, int<1,99>, int<1,31>}  */
    private array $ymd {
        get {
            if (!isset($this->ymd)) {
                $days = \intdiv($this->tsSec, self::SECONDS_PER_DAY);
                $days -= (int)(($this->tsSec % self::SECONDS_PER_DAY) < 0);
                $this->ymd = $this->calendar->getYmdByDaysSinceUnixEpoch($days);
            }

            return $this->ymd;
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

    public int $dayOfYear  {
        get => $this->calendar->getDayOfYearByYmd($this->ymd[0], $this->ymd[1], $this->ymd[2]);
    }

    public int $dayOfWeek {
        get {
            $days = \intdiv($this->tsSec, self::SECONDS_PER_DAY);
            $days -= (int)(($this->tsSec % self::SECONDS_PER_DAY) < 0);

            return $this->calendar->getDayOfWeekByDaysSinceUnixEpoch($days);
        }
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
        get => LocalDate::fromYmd($this->ymd[0], $this->ymd[1], $this->ymd[2], calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->calendar->getWeekOfYearByYmd(...$this->ymd);
    }

    public int $yearOfWeek {
        get => $this->calendar->getYearOfWeekByYmd(...$this->ymd);
    }

    /**
     * @param int<0,999999999> $nanoOfSecond
     */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
        public readonly Calendar $calendar,
    ) {}

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Period) {
            $ymdHms = $this->calendar->addPeriodToYmd(
                $durationOrPeriod,
                $this->ymd[0],
                $this->ymd[1],
                $this->ymd[2],
                $this->hour,
                $this->minute,
                $this->second,
                $this->nanoOfSecond,
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
            );
        }

        $tuple = $durationOrPeriod->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self($tuple[0], $tuple[1], $this->calendar);
    }

    public function sub(Duration|Period $durationOrPeriod): self
    {
        return $this->add($durationOrPeriod->inverted());
    }

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->tsSec, $this->nanoOfSecond, $calendar);
    }

    /**
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999999> $nanoOfSecond
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
    ): self {
        $calendar ??= IsoCalendar::getInstance();

        $days = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        $secs = $hour * 3600 + $minute * 60 + $second;

        if ($days > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $days * self::SECONDS_PER_DAY > PHP_INT_MAX - $secs
            || $days < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($days === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $secs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-m-d H:i:sf');
            $sf  = $second + $nanoOfSecond / 1_000_000_000;
            throw new RangeError(sprintf(
                "A LocalDateTime of the %s must be between %s and %s, %s given",
                $calendar::class,
                $fmt->format(self::min($calendar)),
                $fmt->format(self::max($calendar)),
                "{$year}-{$month}-{$dayOfMonth} {$hour}:{$minute}:{$sf}",
            ));
        }

        $ts = $days < 0
            ? ($days + 1) * self::SECONDS_PER_DAY + $secs - self::SECONDS_PER_DAY
            : $days * self::SECONDS_PER_DAY + $secs;

        $ldt = new self($ts, $nanoOfSecond, calendar: $calendar);
        $ldt->ymd = [$year, $month, $dayOfMonth];

        return $ldt;
    }

    /**
     * @param int<1,366> $dayOfYear
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999999> $nanoOfSecond
     */
    public static function fromYd(
        int $year,
        int $dayOfYear,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
        ?Calendar $calendar = null,
    ): self {
        $calendar ??= IsoCalendar::getInstance();

        $days = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        $secs = $hour * 3600 + $minute * 60 + $second;

        if ($days > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $days * self::SECONDS_PER_DAY > PHP_INT_MAX - $secs
            || $days < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($days === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $secs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-z H:i:sf');
            $sf  = $second + $nanoOfSecond / 1_000_000_000;
            throw new RangeError(sprintf(
                "A LocalDateTime of the %s must be between %s and %s, %s given",
                $calendar::class,
                $fmt->format(self::min($calendar)),
                $fmt->format(self::max($calendar)),
                "{$year}-{$dayOfYear} {$hour}:{$minute}:{$sf}",
            ));
        }

        $ts = $days < 0
            ? ($days + 1) * self::SECONDS_PER_DAY + $secs - self::SECONDS_PER_DAY
            : $days * self::SECONDS_PER_DAY + $secs;

        return new self($ts, $nanoOfSecond, calendar: $calendar);
    }

    public static function fromDateTime(Date $date, Time $time): self
    {
        $days = $date->calendar->getDaysSinceUnixEpochByYmd($date->year, $date->month, $date->dayOfMonth);
        $secs = $time->hour * 3600 + $time->minute * 60 + $time->second;

        if ($days > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $days * self::SECONDS_PER_DAY > PHP_INT_MAX - $secs
            || $days < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($days === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $secs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-m-d H:i:sf');
            throw new RangeError(sprintf(
                "A LocalDateTime of the %s must be between %s and %s, %s %s given",
                $date->calendar::class,
                $fmt->format(self::min($date->calendar)),
                $fmt->format(self::max($date->calendar)),
                new DateTimeFormatter('Y-m-d')->format($date),
                new DateTimeFormatter('H:i:sf')->format($time),
            ));
        }

        $ts = $days < 0
            ? ($days + 1) * self::SECONDS_PER_DAY + $secs - self::SECONDS_PER_DAY
            : $days * self::SECONDS_PER_DAY + $secs;

        $ldt = new self($ts, $time->nanoOfSecond, $date->calendar);
        $ldt->ymd = [$date->year, $date->month, $date->dayOfMonth];

        return $ldt;
    }

    public static function min(?Calendar $calendar = null): self
    {
        return new self(PHP_INT_MIN, 0, $calendar ?? IsoCalendar::getInstance());
    }

    public static function max(?Calendar $calendar = null): self
    {
        return new self(PHP_INT_MAX, 999_999_999, $calendar ?? IsoCalendar::getInstance());
    }
}
