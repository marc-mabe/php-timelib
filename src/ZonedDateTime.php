<?php

namespace time;

final class ZonedDateTime implements Date, Time, Zoned
{
    private ?\DateTimeImmutable $_legacySec = null;
    private \DateTimeImmutable $legacySec {
        get => $this->_legacySec ?? \DateTimeImmutable::createFromTimestamp($this->toUnixTimestampTuple()[0])
            ->setTimezone($this->zoneOffset->toLegacyTz());
    }

    public int $year {
        get => (int)$this->legacySec->format('Y');
    }

    public Month $month {
        get => Month::from((int)$this->legacySec->format('n'));
    }

    public int $dayOfMonth {
        get => (int)$this->legacySec->format('j');
    }

    public int $dayOfYear {
        get => (int)$this->legacySec->format('z');
    }

    public DayOfWeek $dayOfWeek {
        get => DayOfWeek::from((int)$this->legacySec->format('N'));
    }

    public int $hour {
        get => (int)$this->legacySec->format('G');
    }

    public int $minute {
        get => (int)$this->legacySec->format('i');
    }

    public int $second {
        get => (int)$this->legacySec->format('s');
    }

    public int $milliOfSecond {
        get => $this->moment->milliOfSecond;
    }

    public int $microOfSecond {
        get => $this->moment->microOfSecond;
    }

    public int $nanoOfSecond {
        get => $this->moment->nanoOfSecond;
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

    public Duration $offset {
        get {
            $seconds    = $this->legacySec->getOffset();
            $isNegative = $seconds < 0;
            $secondsAbs = \abs($seconds);
            $hour       = \intdiv($secondsAbs, 3600);
            $minute     = \intdiv($secondsAbs, 60) % 60;
            $second     = $secondsAbs % 60;
            return new Duration(isNegative: $isNegative, hours: $hour, minutes: $minute, seconds: $second);
        }
    }

    private function __construct(
        private readonly Moment $moment,
        public readonly ZoneOffset $zoneOffset,
    ) {}

    public function add(Duration $duration): self
    {
        $s  = $this->legacySec->add($duration->toLegacyInterval())->getTimestamp();
        $ns = $this->nanoOfSecond; // TODO: handle fraction of a second
        return new self(Moment::fromUnixTimestampTuple([$s, $ns]), $this->zoneOffset);
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

    public function moveToZone(ZoneOffset $zoneOffset): self
    {
        return new self($this->moment, $zoneOffset);
    }

    public function withZone(ZoneOffset $zoneOffset): self
    {
        return self::fromDateTime($zoneOffset, $this->date, $this->time);
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public function toMoment(): Moment
    {
        return $this->moment;
    }

    /**
     * Convert to current unix timestamp in defined unit
     *
     * @return int|float
     */
    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->moment->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0,999999999>} */
    public function toUnixTimestampTuple(): array
    {
        return $this->moment->toUnixTimestampTuple();
    }

    public static function fromNow(ZoneOffset $zoneOffset, Clock $clock = new Clock()): self
    {
        return $clock->takeZonedDateTime($zoneOffset);
    }

    public static function fromUnixTimestamp(int|float $timestamp, TimeUnit $unit = TimeUnit::Second): self
    {
        return new self(Moment::fromUnixTimestamp($timestamp, $unit), ZoneOffset::fromIdentifier('+00:00'));
    }

    /** @param array{int, int<0,999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self
    {
        return new self(Moment::fromUnixTimestampTuple($timestampTuple), ZoneOffset::fromIdentifier('+00:00'));
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
        $n = $month instanceof Month ? $month->value : $month;
        $i = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-n-j G:i:s',
            "{$year}-{$n}-{$dayOfMonth} {$hour}:{$i}:{$s}",
            $zoneOffset->toLegacyTz(),
        );

        return new self(Moment::fromUnixTimestampTuple([$legacy->getTimestamp(), $nanoOfSecond]), $zoneOffset);
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
        $z = $dayOfYear -1;
        $i = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$year}-{$z} {$hour}:{$i}:{$s}",
            $zoneOffset->toLegacyTz(),
        );

        return new self(Moment::fromUnixTimestampTuple([$legacy->getTimestamp(), $nanoOfSecond]), $zoneOffset);
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

        $z = $date->dayOfYear - 1;
        $i = str_pad($time->minute, 2, '0', STR_PAD_LEFT);
        $s = str_pad($time->second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$date->year}-{$z} {$time->hour}:{$i}:{$s}",
            $zoneOffset->toLegacyTz(),
        );

        return new self(Moment::fromUnixTimestampTuple([$legacy->getTimestamp(), $time->nanoOfSecond]), $zoneOffset);
    }
}
