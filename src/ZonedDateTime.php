<?php declare(strict_types=1);

namespace time;

final class ZonedDateTime implements Instanted, Date, Time, Zoned
{
    public Calendar $calendar {
        get => $this->adjusted->calendar;
    }

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
        get => $this->instant->milliOfSecond;
    }

    public int $microOfSecond {
        get => $this->instant->microOfSecond;
    }

    public int $nanoOfSecond {
        get => $this->instant->nanoOfSecond;
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

    public WeekInfo $weekInfo {
        get => $this->instant->weekInfo;
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->weekInfo->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => $this->weekInfo->getYearOfWeek($this);
    }

    public readonly ZoneOffset $offset;

    private readonly Instant $adjusted;

    private function __construct(
        public readonly Instant $instant,
        public readonly Zone $zone,
    ) {
        $offset         = $zone->getOffsetAt($instant);
        $this->adjusted = $this->instant->add($offset->toDuration());
        $this->offset   = $offset;
    }

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Duration) {
            return new self($this->instant->add($durationOrPeriod), $this->zone);
        }

        $dt = $this->adjusted->add($durationOrPeriod);
        return self::fromDateTime($this->zone, $dt, $dt, disambiguation: Disambiguation::COMPATIBLE);
    }

    public function sub(Duration|Period $durationOrPeriod): self
    {
        return $this->add($durationOrPeriod->inverted());
    }

    public function withZoneSameInstant(Zone $zone): self
    {
        return new self($this->instant, $zone);
    }

    public function withZoneSameLocal(Zone $zone): self
    {
        return self::fromDateTime($zone, $this->date, $this->time);
    }

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->instant->withCalendar($calendar), $this->zone);
    }

    public function withWeekInfo(WeekInfo $weekInfo): self
    {
        return $this->weekInfo === $weekInfo
            ? $this
            : new self($this->instant->withWeekInfo($weekInfo), $this->zone);
    }

    /**
     * Convert to current unix timestamp in defined unit
     *
     * @return int|float
     */
    public function toUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->instant->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0, 999999999>} */
    public function toUnixTimestampTuple(): array
    {
        return $this->instant->toUnixTimestampTuple();
    }

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return new self(
            Instant::fromUnixTimestamp($timestamp, $unit, $calendar, $weekInfo),
            new ZoneOffset(totalSeconds: 0),
        );
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(
        array $timestampTuple,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return new self(
            Instant::fromUnixTimestampTuple($timestampTuple, $calendar, $weekInfo),
            new ZoneOffset(totalSeconds: 0),
        );
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
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $localDays = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        $localTs   = $localDays * 60 * 60 * 24;
        $localTs  += $hour * 3600 + $minute * 60 + $second;

        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;
        return new self(Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond], $calendar, $weekInfo), $zone);
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
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $localDays = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        $localTs   = $localDays * 60 * 60 * 24;
        $localTs  += $hour * 3600 + $minute * 60 + $second;

        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;
        return new self(Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond], $calendar, $weekInfo), $zone);
    }

    public static function fromDateTime(
        Zone $zone,
        Date $date,
        ?Time $time = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        $localDays = $date->calendar->getDaysSinceUnixEpochByYmd($date->year, $date->month, $date->dayOfMonth);
        $localTs   = $localDays * 60 * 60 * 24;
        $localTs  += $time ? $time->hour * 3600 + $time->minute * 60 + $time->second : 0;

        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;
        $ns     = $time ? $time->nanoOfSecond : 0;
        return new self(Instant::fromUnixTimestampTuple([$ts, $ns], $date->calendar, $date->weekInfo), $zone);
    }

    private static function findOffsetByLocalTimestamp(
        Zone $zone,
        int $localTs,
        Disambiguation $disambiguation,
    ): ZoneOffset {
        $offset = $zone->fixedOffset;
        if ($offset) {
            return $offset;
        }

        $maxTs   = $localTs + ZoneOffset::TOTAL_SECONDS_MAX;
        $maxTran = $zone->info->getTransitionAt(Instant::fromUnixTimestampTuple([$maxTs, 0]));
        \assert($maxTran !== null);
        $maxStart = $maxTran->instant->toUnixTimestampTuple()[0] + $maxTran->offset->totalSeconds;

        $minTs   = $localTs + ZoneOffset::TOTAL_SECONDS_MIN;
        $minTran = $zone->info->getTransitionAt(Instant::fromUnixTimestampTuple([$minTs, 0]));
        \assert($minTran !== null);
        $minStart = $minTran->instant->toUnixTimestampTuple()[0] + $minTran->offset->totalSeconds;
        $minEnd   = $maxTran->instant->toUnixTimestampTuple()[0] + $minTran->offset->totalSeconds;

        if ($minStart <= $localTs && $minEnd > $localTs) {
            if ($maxStart <= $localTs) {
                return match ($disambiguation) {
                    Disambiguation::EARLIER    => $minTran->offset,
                    Disambiguation::LATER      => $maxTran->offset,
                    Disambiguation::COMPATIBLE => $minTran->offset,
                    Disambiguation::REJECT => throw new \RuntimeException(sprintf(
                        "Ambiguous date-time '%s' for zone '%s'",
                        new DateTimeFormatter('Y-m-d H:i:s')->format(Instant::fromUnixTimestampTuple([$localTs, 0])),
                        $zone->identifier
                    )),
                };
            }

            return $minTran->offset;
        }

        if ($maxStart > $localTs) {
            return match ($disambiguation) {
                Disambiguation::EARLIER    => $maxTran->offset,
                Disambiguation::LATER      => $minTran->offset,
                Disambiguation::COMPATIBLE => $minTran->offset,
                Disambiguation::REJECT => throw new \RuntimeException(sprintf(
                    "Invalid date-time '%s' for zone '%s'",
                    new DateTimeFormatter('Y-m-d H:i:s')->format(Instant::fromUnixTimestampTuple([$localTs, 0])),
                    $zone->identifier
                )),
            };
        }

        return $maxTran->offset;
    }
}
