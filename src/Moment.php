<?php

namespace time;

final class Moment implements Date, Time {
    private ?\DateTimeImmutable $_legacySec = null;
    private \DateTimeImmutable $legacySec {
        get => $this->_legacySec ??= \DateTimeImmutable::createFromTimestamp($this->tsSec);
    }

    public int $year {
        get => (int)$this->legacySec->format('Y');
    }

    public Month $month {
        get => Month::from((int)$this->legacySec->format('m'));
    }

    public int $dayOfMonth {
        get => (int)$this->legacySec->format('j');
    }

    public int $dayOfYear  {
        get => ((int)$this->legacySec->format('z') + 1);
    }

    public DayOfWeek $dayOfWeek {
        get => DayOfWeek::from((int)$this->legacySec->format('N'));
    }

    public int $hour {
        get {
            $s = $this->tsSec % (60 * 60 * 24);
            $h = \intdiv($s, 60 * 60);
            return ($s % 60) < 0 ? $h + 23 : $h;
        }
    }

    public int $minute  {
        get {
            $s = $this->tsSec % (60 * 60);
            $m = \intdiv($s, 60);
            return ($s % 60) < 0 ? $m + 59 : $m;
        }
    }

    public int $second  {
        get {
            $s = $this->tsSec % 60;
            return $s < 0 ? $s + 60 : $s;
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
        get => LocalDate::fromYd($this->year, $this->dayOfYear);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
    ) {}

    public function add(Duration $duration): self
    {
        $ns = $this->nanoOfSecond + $duration->nanoOfSeconds;
        $s  = $this->tsSec + $duration->seconds + \intdiv($ns, 1_000_000_000);
        $ns = $ns % 1_000_000_000;

        return new self($s, $ns);
    }

    public function sub(Duration $duration): self
    {
        return $this->add($duration->isNegative ? $duration->abs() : $duration->negated());
    }

    public function withYear(int $year): self
    {
        return self::fromYmd($year, $this->month, $this->dayOfMonth, $this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public function withMonth(Month|int $month): self
    {
        return self::fromYmd($this->year, $month, $this->dayOfMonth, $this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public function withDayOfMonth(int $dayOfMonth): self
    {
        return self::fromYmd($this->year, $this->month, $dayOfMonth, $this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public function withDayOfYear(int $dayOfYear): self
    {
        return self::fromYd($this->year, $dayOfYear, $this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public function withHour(int $hour): self
    {
        return self::fromYmd($this->year, $this->month, $this->dayOfMonth, $hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public function withMinute(int $minute): self
    {
        return self::fromYmd($this->year, $this->month, $this->dayOfMonth, $this->hour, $minute, $this->second, $this->nanoOfSecond);
    }

    public function withSecond(int $second): self
    {
        return self::fromYmd($this->year, $this->month, $this->dayOfMonth, $this->hour, $this->minute, $second, $this->nanoOfSecond);
    }

    public function withMilliOfSecond(int $milliOfSecond): self
    {
        return new self($this->tsSec, $milliOfSecond * 1_000_000);
    }

    public function withMicroOfSecond(int $microOfSecond): self
    {
        return new self($this->tsSec, $microOfSecond * 1_000);
    }

    public function withNanoOfSecond(int $nanoOfSecond): self
    {
        return new self($this->tsSec, $nanoOfSecond);
    }

    public function truncatedTo(DateUnit|TimeUnit $unit): self {
        return match ($unit) {
            DateUnit::Year => self::fromYd($this->year, 1),
            DateUnit::Month => self::fromYmd($this->year, $this->month, 1),
            DateUnit::Day => self::fromYd($this->year, $this->dayOfYear),
            TimeUnit::Hour => self::fromYd($this->year, $this->dayOfYear, $this->hour),
            TimeUnit::Minute => self::fromYd($this->year, $this->dayOfYear, $this->hour, $this->minute),
            TimeUnit::Second => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second
            ),
            TimeUnit::Millisecond => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000_000) * 1_000_000
            ),
            TimeUnit::Microsecond => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000) * 1_000
            ),
            TimeUnit::Nanosecond => $this,
        };
    }

    // TODO: Default rounding mode should match \round()
    public function roundedTo(DateUnit|TimeUnit $unit, \RoundingMode $mode = \RoundingMode::HalfAwayFromZero): self {
        // TODO
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    /**
     * Convert to current unix timestamp in defined unit
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
            TimeUnit::Hour        => ($this->tsSec < 0 && $this->nanoOfSecond ? $this->tsSec + 1 : $this->tsSec) / 3_600,
            TimeUnit::Minute      => ($this->tsSec < 0 && $this->nanoOfSecond ? $this->tsSec + 1 : $this->tsSec) / 60,
            TimeUnit::Second      => $this->tsSec < 0 && $this->nanoOfSecond ? $this->tsSec + 1 : $this->tsSec,
            TimeUnit::Millisecond => ($this->tsSec * 1_000) + ($this->nanoOfSecond / 1_000_000),
            TimeUnit::Microsecond => ($this->tsSec * 1_000_000) + ($this->nanoOfSecond / 1_000),
            TimeUnit::Nanosecond  => ($this->tsSec * 1_000_000_000) + $this->nanoOfSecond,
        };
    }

    /** @return array{int, int<0, 999999999>} */
    public function toUnixTimestampTuple(): array {
        return [$this->tsSec, $this->nanoOfSecond];
    }

    public function toZonedDateTime(Zone $zone): ZonedDateTime
    {
        return ZonedDateTime::fromUnixTimestampTuple($this->toUnixTimestampTuple())->moveToZone($zone);
    }

    public static function fromNow(Clock $clock = new WallClock()): self
    {
        return $clock->takeMoment();
    }

    public static function fromUnixTimestamp(int|float $timestamp, TimeUnit $unit = TimeUnit::Second): self
    {
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
        };

        return new self($tsSecInt, $ns);
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self {
        return new self($timestampTuple[0], $timestampTuple[1]);
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
    ): self {
        $z = $dayOfYear - 1;
        $i = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);
        $ts = \DateTime::createFromFormat(
            'Y-z G:i:s',
            "{$year}-{$z} {$hour}:{$i}:{$s}"
        )->getTimestamp();

        return new self($ts, $nanoOfSecond);
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
    ): self {
        $n = $month instanceof Month ? $month->value : $month;
        $i = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);
        $ts = \DateTime::createFromFormat(
            'Y-n-j G:i:s',
            "{$year}-{$n}-{$dayOfMonth} {$hour}:{$i}:{$s}"
        )->getTimestamp();

        return new self($ts, $nanoOfSecond);
    }

    public static function fromZonedDateTime(Date&Time&Zoned $zonedDateTime): self
    {
        [$s, $ns] = ZonedDateTime::fromZonedDateTime($zonedDateTime)->toUnixTimestampTuple();
        return new self($s, $ns);
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
            );
        }

        $zonedDateTime = ZonedDateTime::fromDateTime($zone, $date, $time);
        return self::fromZonedDateTime($zonedDateTime);
    }
}
