<?php

namespace dt;

use DateTimeImmutable;

final class Moment implements Date, Time {
    private ?\DateTimeImmutable $_legacy = null;
    private \DateTimeImmutable $legacy {
        get => $this->_legacy
            ??= \DateTimeImmutable::createFromTimestamp($this->timestamp)->setMicrosecond($this->microOfSecond);
    }

    public int $year {
        get => (int)$this->legacy->format('Y');
    }

    public Month $month {
        get => Month::from((int)$this->legacy->format('m'));
    }

    public int $dayOfMonth {
        get => (int)$this->legacy->format('d');
    }

    public int $dayOfYear  {
        get => (int)$this->legacy->format('z');
    }

    public int $hour {
        get =>\intdiv($this->timestamp % (60 * 60 * 24), 60 * 60)
            + ($this->timestamp < 0 ? 23 : 0);
    }

    public int $minute  {
        get => \intdiv($this->timestamp % (60 * 60), 60)
            + ($this->timestamp < 0 ? 59 : 0);
    }

    public int $second  {
        get => $this->timestamp % 60
            + ($this->timestamp < 0 ? 60 : 0);
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
        public readonly int $timestamp,
        public readonly int $nanoOfSecond,
    ) {}

    public function add(Duration $duration): self
    {
        $durationNoFractions = $duration
            ->withMilliseconds(0)
            ->withMicroseconds(0)
            ->withNanoseconds(0);

        $s = DateTimeImmutable::createFromTimestamp($this->timestamp)
            ->add($durationNoFractions->toLegacyInterval())
            ->getTimestamp();

        $ns = $duration->isNegative
            ? $this->nanoOfSecond
                + $duration->nanoseconds
                + ($duration->microseconds * 1_000)
                + ($duration->milliseconds * 1_000_000)
            : $this->nanoOfSecond
                - $duration->nanoseconds
                - ($duration->microseconds * 1_000)
                - ($duration->milliseconds * 1_000_000);

        if ($ns >= 1_000_000_000) {
            $s += \intdiv($ns, 1_000_000_000);
            $ns = $ns % 1_000_000_000;
        } elseif ($ns < 0) {
            // TODO
        }

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
        return new self($this->timestamp, $milliOfSecond * 1_000_000);
    }

    public function withMicroOfSecond(int $microOfSecond): self
    {
        return new self($this->timestamp, $microOfSecond * 1_000);
    }

    public function withNanoOfSecond(int $nanoOfSecond): self
    {
        return new self($this->timestamp, $nanoOfSecond);
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
     * TODO: Detect Integer Overflow
     *
     * @return int|float
     */
    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $asFloat = false): int|float {
        return match ($unit) {
            TimeUnit::Hour        => $asFloat
                ? ($this->timestamp / 3_600) + ($this->nanoOfSecond / 1_000_000_000 / 3_600)
                : (int)($this->timestamp / 3_600),
            TimeUnit::Minute      => $asFloat
                ? ($this->timestamp / 60) + ($this->nanoOfSecond / 1_000_000_000 / 60)
                : (int)($this->timestamp / 60),
            TimeUnit::Second      => $asFloat
                ? (float)($this->timestamp + ($this->nanoOfSecond / 1_000_000_000))
                : $this->timestamp,
            TimeUnit::Millisecond => $asFloat
                ? (float)(($this->timestamp * 1_000) + ($this->nanoOfSecond / 1_000_000))
                : (int)(($this->timestamp * 1_000) + ($this->nanoOfSecond / 1_000_000)),
            TimeUnit::Microsecond => $asFloat
                ? (float)(($this->timestamp * 1_000_000) + ($this->nanoOfSecond / 1_000))
                : (int)(($this->timestamp * 1_000_000) + ($this->nanoOfSecond / 1_000)),
            TimeUnit::Nanosecond  => $asFloat
                ? (float)(($this->timestamp * 1_000_000_000) + $this->nanoOfSecond)
                : (int)(($this->timestamp * 1_000_000_000) + $this->nanoOfSecond),
        };
    }

    /** @return array{int, int<0, 999999999>} */
    public function toUnixTimestampTuple(): array {
        return [$this->timestamp, $this->nanoOfSecond];
    }

    public function toZonedDateTime(ZoneOffset $zoneOffset): ZonedDateTime
    {
        return ZonedDateTime::fromUnixTimestampTuple($this->toUnixTimestampTuple())->moveToZone($zoneOffset);
    }

    public static function fromNow(Clock $clock = new Clock()): self
    {
        return $clock->takeMoment();
    }

    public static function fromUnixTimestamp(int|float $timestamp, TimeUnit $unit = TimeUnit::Second): self
    {
        if (\is_float($timestamp)) {
            if (!\is_finite($timestamp)) {
                throw new \ValueError('Timestamp must be a finite number.');
            }

            $tsInt      = (int)$timestamp;
            $tsFraction = \fmod($timestamp, 1);
        } else {
            $tsInt      = $timestamp;
            $tsFraction = 0.0;
        }

        [$s, $ns] = match ($unit) {
            TimeUnit::Second      => [$tsInt, (int)($tsFraction * 1_000_000_000)],
            TimeUnit::Millisecond => [\intdiv($tsInt, 1_000), (int)($tsFraction * 1_000_000)],
            TimeUnit::Microsecond => [\intdiv($tsInt, 1_000_000), (int)($tsFraction * 1_000)],
            TimeUnit::Nanosecond  => [\intdiv($tsInt, 1_000_000_000), 0],
        };

        return new self($s, $ns);
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self {
        return new self($timestampTuple[0], $timestampTuple[1]);
    }

    public static function fromYd(
        int $year,
        int $dayOfYear,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): self {
        $ts = \mktime($hour, $minute, $second, 0, $dayOfYear, $year);
        return new self($ts, $nanoOfSecond);
    }

    public static function fromYmd(
        int $year,
        Month|int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): self {
        $m  = $month instanceof Month ? $month->value : $month;
        $ts = \mktime($hour, $minute, $second, $m, $dayOfMonth, $year);
        return new self($ts, $nanoOfSecond);
    }

    public static function fromZonedDateTime(Date&Time&Zoned $zonedDateTime): self
    {
        [$s, $ns] = ZonedDateTime::fromZonedDateTime($zonedDateTime)->toUnixTimestampTuple();
        return new self($s, $ns);
    }

    public static function fromDateTime(Date $date, ?Time $time = null, ?ZoneOffset $zoneOffset = null): self
    {
        if ($zoneOffset === null) {
            return self::fromYd(
                $date->year,
                $date->dayOfYear,
                $time->hour ?? 0,
                $time->minute ?? 0,
                $time->second ?? 0,
                $time->nanoOfSecond ?? 0,
            );
        }

        $zonedDateTime = ZonedDateTime::fromDateTime($zoneOffset, $date, $time);
        return self::fromZonedDateTime($zonedDateTime);
    }
}
