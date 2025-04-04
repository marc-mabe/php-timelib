<?php declare(strict_types=1);

namespace time;

final class Moment implements Date, Time
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

    public int $dayOfYear {
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

    public LocalDateTime $local {
        get => LocalDateTime::fromDateTime($this->date, $this->time);
    }

    public LocalDate $date {
        get => LocalDate::fromYd($this->year, $this->dayOfYear, calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    /** @param int<0, 999999999> $nanoOfSecond */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
        public readonly Calendar $calendar,
    ) {}

    public function add(Duration $duration): self
    {
        $tuple = $duration->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self($tuple[0], $tuple[1], $this->calendar);
    }

    public function sub(Duration $duration): self
    {
        return $this->add($duration->inverted());
    }

    public function withYear(int $year): self
    {
        return self::fromYmd(
            $year,
            $this->month,
            $this->dayOfMonth,
            $this->hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param Month|int<1, 12> $month */
    public function withMonth(Month|int $month): self
    {
        return self::fromYmd(
            $this->year,
            $month,
            $this->dayOfMonth,
            $this->hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param int<1, 31> $dayOfMonth */
    public function withDayOfMonth(int $dayOfMonth): self
    {
        return self::fromYmd(
            $this->year,
            $this->month,
            $dayOfMonth,
            $this->hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param int<1, 366> $dayOfYear */
    public function withDayOfYear(int $dayOfYear): self
    {
        return self::fromYd(
            $this->year,
            $dayOfYear,
            $this->hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param int<0, 23> $hour */
    public function withHour(int $hour): self
    {
        return self::fromYmd(
            $this->year,
            $this->month,
            $this->dayOfMonth,
            $hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param int<0, 59> $minute */
    public function withMinute(int $minute): self
    {
        return self::fromYmd(
            $this->year,
            $this->month,
            $this->dayOfMonth,
            $this->hour,
            $minute,
            $this->second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param int<0, 59> $second */
    public function withSecond(int $second): self
    {
        return self::fromYmd(
            $this->year,
            $this->month,
            $this->dayOfMonth,
            $this->hour,
            $this->minute,
            $second,
            $this->nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /** @param int<0, 999> $milliOfSecond */
    public function withMilliOfSecond(int $milliOfSecond): self
    {
        return new self($this->tsSec, $milliOfSecond * 1_000_000, $this->calendar);
    }

    /** @param int<0, 999999> $microOfSecond */
    public function withMicroOfSecond(int $microOfSecond): self
    {
        return new self($this->tsSec, $microOfSecond * 1_000, $this->calendar);
    }

    /** @param int<0, 999999999> $nanoOfSecond */
    public function withNanoOfSecond(int $nanoOfSecond): self
    {
        return new self($this->tsSec, $nanoOfSecond, $this->calendar);
    }

    public function truncatedTo(DateUnit|TimeUnit $unit): self
    {
        return match ($unit) {
            DateUnit::Year => self::fromYd($this->year, 1, calendar: $this->calendar),
            DateUnit::Month => self::fromYmd($this->year, $this->month, 1, calendar: $this->calendar),
            DateUnit::Day => self::fromYd($this->year, $this->dayOfYear, calendar: $this->calendar),
            TimeUnit::Hour => self::fromYd($this->year, $this->dayOfYear, $this->hour, calendar: $this->calendar),
            TimeUnit::Minute => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                calendar: $this->calendar,
            ),
            TimeUnit::Second => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                calendar: $this->calendar,
            ),
            TimeUnit::Millisecond => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000_000) * 1_000_000, // @phpstan-ignore argument.type
                calendar: $this->calendar,
            ),
            TimeUnit::Microsecond => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000) * 1_000, // @phpstan-ignore argument.type
                calendar: $this->calendar,
            ),
            TimeUnit::Nanosecond => $this,
        };
    }

    /**
     * Convert to unix timestamp in the defined unit.
     *
     * In case the fractions should not be included the resulting timestamp will be rounded down.
     *
     * @return int|float
     */
    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        if ($fractions) {
            return match ($unit) {
                TimeUnit::Hour        => ($this->tsSec / 3_600) + ($this->nanoOfSecond / 1_000_000_000 / 3_600),
                TimeUnit::Minute      => ($this->tsSec / 60) + ($this->nanoOfSecond / 1_000_000_000 / 60),
                TimeUnit::Second      => ($this->tsSec + ($this->nanoOfSecond / 1_000_000_000)),
                TimeUnit::Millisecond => ($this->tsSec * 1_000) + ($this->nanoOfSecond / 1_000_000),
                TimeUnit::Microsecond => ($this->tsSec * 1_000_000) + ($this->nanoOfSecond / 1_000),
                TimeUnit::Nanosecond  => ($this->tsSec * 1_000_000_000) + $this->nanoOfSecond,
            };
        }

        return match ($unit) {
            TimeUnit::Hour        => \intdiv($this->tsSec, 3_600),
            TimeUnit::Minute      => \intdiv($this->tsSec, 60),
            TimeUnit::Second      => $this->tsSec,
            TimeUnit::Millisecond => ($this->tsSec * 1_000) + \intdiv($this->nanoOfSecond, 1_000_000),
            TimeUnit::Microsecond => ($this->tsSec * 1_000_000) + \intdiv($this->nanoOfSecond, 1_000),
            TimeUnit::Nanosecond  => ($this->tsSec * 1_000_000_000) + $this->nanoOfSecond,
        };
    }

    /** @return array{int, int<0, 999999999>} */
    public function toUnixTimestampTuple(): array {
        return [$this->tsSec, $this->nanoOfSecond];
    }

    public function toZonedDateTime(Zone $zone): ZonedDateTime
    {
        return ZonedDateTime::fromUnixTimestampTuple($this->toUnixTimestampTuple(), $this->calendar)
            ->withZoneSameMoment($zone);
    }

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
        ?Calendar $calendar = null,
    ): self {
        if (\is_float($timestamp)) {
            if (!\is_finite($timestamp)) {
                throw new \ValueError('Timestamp must be a finite number.');
            }

            $tsFraction = \fmod($timestamp, 1);
            $tsInt      = $timestamp - $tsFraction;

            if ($tsInt > PHP_INT_MAX || $timestamp < PHP_INT_MIN) {
                throw new \ValueError(
                    'Timestamp must be within ' . PHP_INT_MIN . ' and ' . PHP_INT_MAX . '.999999999.'
                );
            }

            $tsInt = (int)$tsInt;
            if ($tsFraction < 0.0) {
                $tsInt -= 1;
                $tsFraction = 1.0 + $tsFraction;
            }

        } else {
            $tsInt      = $timestamp;
            $tsFraction = 0.0;
        }

        [$tsSecInt, $ns] = match ($unit) {
            TimeUnit::Second      => [$tsInt, (int)($tsFraction * 1_000_000_000)],
            TimeUnit::Millisecond => [
                \intdiv($tsInt, 1_000),
                ($tsInt % 1_000 * 1_000_000) - (int)($tsFraction * 1_000_000_000),
            ],
            TimeUnit::Microsecond => [
                \intdiv($tsInt, 1_000_000),
                ($tsInt % 1_000_000 * 1_000) - (int)($tsFraction * 1_000_000_000),
            ],
            TimeUnit::Nanosecond  => [
                \intdiv($tsInt, 1_000_000_000),
                $tsInt % 1_000_000_000,
            ],
            TimeUnit::Minute => [(int)($timestamp * 60), (int)($timestamp * 60 / 1_000_000_000)],
            TimeUnit::Hour => [(int)($timestamp * 3600), (int)($timestamp * 3600 / 1_000_000_000)],
        };
        assert($ns >= 0 && $ns <1_000_000_000);

        return new self($tsSecInt, $ns, $calendar ?? GregorianCalendar::getInstance());
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple, ?Calendar $calendar = null): self
    {
        return new self($timestampTuple[0], $timestampTuple[1], $calendar ?? GregorianCalendar::getInstance());
    }

    /**
     * @param int<1, 366> $dayOfYear
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999999> $nanoOfSecond
     * @return self
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
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYd($year, $dayOfYear);
        $ts += $hour * 3600 + $minute * 60 + $second;

        return new self($ts, $nanoOfSecond, $calendar);
    }

    /**
     * @param Month|int<1, 12> $month
     * @param int<1, 31> $dayOfMonth
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999999> $nanoOfSecond
     * @return self
     */
    public static function fromYmd(
        int $year,
        Month|int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
        ?Calendar $calendar = null,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYmd($year, $month, $dayOfMonth);
        $ts += $hour * 3600 + $minute * 60 + $second;

        return new self($ts, $nanoOfSecond, $calendar);
    }

    public static function fromZonedDateTime(Date&Time&Zoned $zonedDateTime): self
    {
        if (!$zonedDateTime instanceof ZonedDateTime) {
            $zonedDateTime = ZonedDateTime::fromZonedDateTime($zonedDateTime);
        }

        [$s, $ns] = $zonedDateTime->toUnixTimestampTuple();
        return new self($s, $ns, $zonedDateTime->calendar);
    }

    public static function fromDateTime(Date $date, ?Time $time = null, ?Zone $zone = null): self
    {
        if ($zone === null) {
            return self::fromYd(
                $date->year,
                $date->dayOfYear,
                $time->hour ?? 0,
                $time->minute ?? 0,
                $time->second ?? 0,
                $time->nanoOfSecond ?? 0,
                calendar: $date->calendar,
            );
        }

        $zonedDateTime = ZonedDateTime::fromDateTime($zone, $date, $time);
        return self::fromZonedDateTime($zonedDateTime);
    }
}
