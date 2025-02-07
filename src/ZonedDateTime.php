<?php

namespace dt;

final class ZonedDateTime implements Date, Time, Zoned {
    public int $year { get => (int)$this->legacySec->format('Y'); }
    public Month $month { get => Month::from((int)$this->legacySec->format('m')); }
    public int $dayOfMonth { get => (int)$this->legacySec->format('j'); }
    public int $dayOfYear  { get => (int)$this->legacySec->format('z'); }
    public int $hour { get => (int)$this->legacySec->format('H'); }
    public int $minute  { get => (int)$this->legacySec->format('i'); }
    public int $second  { get => (int)$this->legacySec->format('s'); }
    public int $milliOfSecond { get => (int)($this->nanoOfSecond / 1_000_000); }
    public int $microOfSecond { get => (int)($this->nanoOfSecond / 1_000); }
    public LocalDateTime $local { get => LocalDateTime::fromDateTime($this->date, $this->time); }
    public LocalDate $date { get => LocalDate::fromYd($this->year, $this->dayOfYear); }
    public LocalTime $time { get => LocalTime::fromHms($this->hour, $this->minute, $this->second); }
    public Duration $offset {
        get {
            $seconds = $this->legacySec->getOffset();
            return $seconds < 0 ? new Duration(isNegative: true, seconds: $seconds) : new Duration(seconds: $seconds);
        }
    }

    private \DateTimeImmutable $legacySec {
        get => \DateTimeImmutable::createFromTimestamp($this->tsSecInt);
    }

    private function __construct(
        public readonly ZoneOffset $zoneOffset,
        private readonly int $tsSecInt,
        public readonly int $nanoOfSecond,
    ) {}

    public function add(Duration $duration): self
    {
        // TODO: Fix fraction of a second
        $legacy = $this->legacySec->add($duration->toLegacyInterval());
        return new self($this->zoneOffset, $legacy->getTimestamp(), $this->nanoOfSecond);
    }

    public function sub(Duration $duration): self {
        return $duration->isNegative
            ? $this->add($duration->abs())
            : $this->add($duration->negated());
    }

    public function truncatedTo(DateUnit|TimeUnit $unit): self {
        // TODO
    }

    // TODO: Default rounding mode should match \round()
    public function roundedTo(DateUnit|TimeUnit $unit, \RoundingMode $mode = \RoundingMode::HalfAwayFromZero): self {
        // TODO
    }

    public function moveToZone(ZoneOffset $zone): self
    {
        return new self($zone, $this->tsSecInt, $this->nanoOfSecond);
    }

    public function withZone(ZoneOffset $zone): self
    {
        return self::fromDateTime($zone, $this->date, $this->time);
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public function toMoment(): Moment
    {
        return Moment::fromUnixTimestampTuple($this->toUnixTimestampTuple());
    }

    /**
     * Convert to current unix timestamp in defined unit
     *
     * @return int|float
     */
    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->toMoment()->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0,999999999>} */
    public function toUnixTimestampTuple(): array
    {
        return [$this->legacySec->getTimestamp(), $this->nanoOfSecond];
    }

    public static function fromNow(ZoneOffset $zoneOffset, Clock $clock = new Clock()): self
    {
        return $clock->takeZonedDateTime($zoneOffset);
    }

    public static function fromUnixTimestamp(int|float $timestamp, TimeUnit $unit = TimeUnit::Second): self
    {
        [$s, $ns] = Moment::fromUnixTimestamp($timestamp, $unit)->toUnixTimestampTuple();
        return new self(ZoneOffset::fromIdentifier('UTC'), $s, $ns);
    }

    /** @param array{int, int<0,999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self
    {
        [$s, $ns] = $timestampTuple;
        return new self(ZoneOffset::fromIdentifier('UTC'), $s, $ns);
    }

    public static function fromYmd(
        ZoneOffset $zoneOffset,
        int $year,
        Month|int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): self {
        $M = $month instanceof Month ? $month->value : $month;
        $m = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-n-j G:i:s',
            "{$year}-{$M}-{$dayOfMonth} {$hour}:{$m}:{$s}",
            $zoneOffset->toLegacyTz(),
        );

        return self::fromUnixTimestampTuple([$legacy->getTimestamp(), $nanoOfSecond])->withZone($zoneOffset);
    }

    public static function fromYd(
        ZoneOffset $zoneOffset,
        int $year,
        int $dayOfYear,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): self {
        $m = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$year}-{$dayOfYear} {$hour}:{$m}:{$s}",
            $zoneOffset->toLegacyTz(),
        );

        return self::fromUnixTimestampTuple([$legacy->getTimestamp(), $nanoOfSecond])->withZone($zoneOffset);
    }

    public static function fromZonedDateTime(Date&Time&Zoned $zonedDateTime): self
    {
        if ($zonedDateTime instanceof self) {
            return $zonedDateTime;
        }

        return self::fromYd(
            $zonedDateTime->zoneOffset,
            $zonedDateTime->year,
            $zonedDateTime->dayOfYear,
            $zonedDateTime->hour,
            $zonedDateTime->minute,
            $zonedDateTime->second,
            $zonedDateTime->nanoOfSecond,
        );
    }

    public static function fromDateTime(ZoneOffset $zoneOffset, Date $date, ?Time $time = null): self
    {
        $time ??= LocalTime::fromHms(0, 0, 0);
        $m = str_pad($time->minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($time->second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$date->year}-{$date->dayOfYear} {$time->hour}:{$m}:{$s}",
            $zoneOffset->toLegacyTz(),
        );

        return new self($zoneOffset, $legacy->getTimestamp(), $time->nanoOfSecond);
    }
}
