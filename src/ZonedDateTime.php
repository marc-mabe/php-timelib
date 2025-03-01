<?php declare(strict_types=1);

namespace time;

final class ZonedDateTime implements Date, Time, Zoned
{
    public int $year {
        get => $this->adjusted->year;
    }

    public Month $month {
        get => $this->adjusted->month;
    }

    public int $dayOfMonth {
        get => $this->adjusted->dayOfMonth;
    }

    public int $dayOfYear {
        get => $this->adjusted->dayOfYear;
    }

    public DayOfWeek $dayOfWeek {
        get => $this->adjusted->dayOfWeek;
    }

    public int $hour {
        get => $this->adjusted->hour;
    }

    public int $minute {
        get => $this->adjusted->minute;
    }

    public int $second {
        get => $this->adjusted->second;
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

    public readonly ZoneOffset $offset;

    private readonly Moment $adjusted;

    private function __construct(
        private readonly Moment $moment,
        public readonly Zone $zone,
    ) {
        // Use legacy to lookup timezone db
        $legacy = \DateTimeImmutable::createFromTimestamp($moment->toUnixTimestampTuple()[0])
            ->setTimezone(new \DateTimeZone($zone->identifier));
        $offsetDuration = new Duration(seconds: $legacy->getOffset());

        $this->adjusted = $this->moment->add($offsetDuration);
        $this->offset = ZoneOffset::fromDuration($offsetDuration);
    }

    public function add(Duration $duration): self
    {
        return new self($this->moment->add($duration), $this->zone);
    }

    public function sub(Duration $duration): self
    {
        return new self($this->moment->sub($duration), $this->zone);
    }

    public function withZoneSameMoment(Zone $zone): self
    {
        return new self($this->moment, $zone);
    }

    public function withZoneSameLocal(Zone $zone): self
    {
        return self::fromDateTime($zone, $this->date, $this->time);
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

    /** @return array{int, int<0, 999999999>} */
    public function toUnixTimestampTuple(): array
    {
        return $this->moment->toUnixTimestampTuple();
    }

    public static function fromUnixTimestamp(int|float $timestamp, TimeUnit $unit = TimeUnit::Second): self
    {
        return new self(Moment::fromUnixTimestamp($timestamp, $unit), Zone::fromIdentifier('+00:00'));
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self
    {
        return new self(Moment::fromUnixTimestampTuple($timestampTuple), Zone::fromIdentifier('+00:00'));
    }

    /**
     * @param Month|int<1, 12> $month
     * @param int<1, 31> $dayOfMonth
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999999> $nanoOfSecond
     */
    public static function fromYmd(
        Zone $zone,
        int $year,
        Month|int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): self {
        $n = $month instanceof Month ? $month->value : $month;
        $i = \str_pad((string)$minute, 2, '0', STR_PAD_LEFT);
        $s = \str_pad((string)$second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-n-j G:i:s',
            "{$year}-{$n}-{$dayOfMonth} {$hour}:{$i}:{$s}",
            new \DateTimeZone($zone->identifier),
        );
        assert($legacy !== false);

        return new self(Moment::fromUnixTimestampTuple([$legacy->getTimestamp(), $nanoOfSecond]), $zone);
    }

    /**
     * @param int<1, 366> $dayOfYear
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999999> $nanoOfSecond
     */
    public static function fromYd(
        Zone $zone,
        int $year,
        int $dayOfYear,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
    ): self {
        $z = $dayOfYear -1;
        $i = \str_pad((string)$minute, 2, '0', STR_PAD_LEFT);
        $s = \str_pad((string)$second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$year}-{$z} {$hour}:{$i}:{$s}",
            new \DateTimeZone($zone->identifier),
        );
        assert($legacy !== false);

        return new self(Moment::fromUnixTimestampTuple([$legacy->getTimestamp(), $nanoOfSecond]), $zone);
    }

    public static function fromZonedDateTime(Date&Time&Zoned $zonedDateTime): self
    {
        if ($zonedDateTime instanceof self) {
            return $zonedDateTime;
        }

        return self::fromYd(
            $zonedDateTime->zone,
            $zonedDateTime->year,
            $zonedDateTime->dayOfYear,
            $zonedDateTime->hour,
            $zonedDateTime->minute,
            $zonedDateTime->second,
            $zonedDateTime->nanoOfSecond,
        );
    }

    public static function fromDateTime(Zone $zone, Date $date, ?Time $time = null): self
    {
        $time ??= LocalTime::fromHms(0, 0, 0);

        $z = $date->dayOfYear - 1;
        $i = \str_pad((string)$time->minute, 2, '0', STR_PAD_LEFT);
        $s = \str_pad((string)$time->second, 2, '0', STR_PAD_LEFT);

        $legacy = \DateTimeImmutable::createFromFormat(
            'Y-z G:i:s',
            "{$date->year}-{$z} {$time->hour}:{$i}:{$s}",
            new \DateTimeZone($zone->identifier),
        );
        assert($legacy !== false);

        return new self(Moment::fromUnixTimestampTuple([$legacy->getTimestamp(), $time->nanoOfSecond]), $zone);
    }
}
