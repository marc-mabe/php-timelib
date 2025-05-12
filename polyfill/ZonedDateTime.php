<?php declare(strict_types=1);

namespace time;

final class ZonedDateTime implements Instanted, Date, Time, Zoned
{
    /** @var null|array{int, Month, int<1,31>}  */
    private ?array $ymd = null;

    public int $year {
        get => ($this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->adjustedSec))[0];
    }

    public Month $month {
        get => ($this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->adjustedSec))[1];
    }

    public int $dayOfMonth {
        get => ($this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->adjustedSec))[2];
    }

    public int $dayOfYear {
        get {
            $this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->adjustedSec);
            return $this->calendar->getDayOfYearByYmd($this->ymd[0], $this->ymd[1], $this->ymd[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => $this->calendar->getDayOfWeekByUnixTimestamp($this->adjustedSec);
    }

    public int $hour {
        get {
            $remainder = $this->adjustedSec % 86400;
            $remainder += ($remainder < 0) * 86400;
            return \intdiv($remainder, 3600);
        }
    }

    public int $minute {
        get {
            $remainder = $this->adjustedSec % 86400;
            $remainder += ($remainder < 0) * 86400;
            $hours = \intdiv($remainder, 3600);
            return \intdiv($remainder - $hours * 3600, 60);
        }
    }

    public int $second {
        get {
            $remainder = $this->adjustedSec % 86400;
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

    public readonly int $nanoOfSecond;

    public LocalDateTime $local {
        get => LocalDateTime::fromDateTime($this->date, $this->time);
    }

    public LocalDate $date {
        get => LocalDate::fromYd($this->year, $this->dayOfYear, calendar: $this->calendar, weekInfo:  $this->weekInfo);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->weekInfo->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => $this->weekInfo->getYearOfWeek($this);
    }

    public readonly ZoneOffset $offset;

    private readonly int $adjustedSec;

    private function __construct(
        public readonly Instant $instant,
        public readonly Zone $zone,
        public readonly Calendar $calendar,
        public readonly WeekInfo $weekInfo,
    ) {
        [$s, $this->nanoOfSecond] = $instant->toUnixTimestampTuple();
        $this->offset             = $zone->getOffsetAt($instant);
        $this->adjustedSec        = $s + $this->offset->totalSeconds;
    }

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Duration) {
            return new self(
                $this->instant->add($durationOrPeriod),
                $this->zone,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            );
        }

        $this->ymd ??= $this->calendar->getYmdByUnixTimestamp($this->adjustedSec);
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
            $this->zone,
            $ymdHms[0],
            $ymdHms[1],
            $ymdHms[2],
            $ymdHms[3],
            $ymdHms[4],
            $ymdHms[5],
            $ymdHms[6],
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
            disambiguation: Disambiguation::COMPATIBLE,
        );
    }

    public function sub(Duration|Period $durationOrPeriod): self
    {
        return $this->add($durationOrPeriod->inverted());
    }

    public function withZoneSameInstant(Zone $zone): self
    {
        return new self(
            $this->instant,
            $zone,
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
        );
    }

    public function withZoneSameLocal(Zone $zone): self
    {
        return self::fromDateTime($this, $this, zone: $zone);
    }

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar ? $this : new self(
            $this->instant,
            zone: $this->zone,
            calendar: $calendar,
            weekInfo: $this->weekInfo,
        );
    }

    public function withWeekInfo(WeekInfo $weekInfo): self
    {
        return $this->weekInfo === $weekInfo ? $this : new self(
            $this->instant,
            zone: $this->zone,
            calendar: $this->calendar,
            weekInfo: $weekInfo,
        );
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

    public static function fromInstant(
        Instant $instant,
        ?Zone $zone = null,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return new self(
            $instant,
            zone: $zone ?? $instant->zone,
            calendar: $calendar ?? $instant->calendar,
            weekInfo: $weekInfo ?? $instant->weekInfo,
        );
    }

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return self::fromInstant(
            Instant::fromUnixTimestamp($timestamp, $unit),
            calendar: $calendar,
            weekInfo: $weekInfo,
        );
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(
        array $timestampTuple,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return self::fromInstant(
            Instant::fromUnixTimestampTuple($timestampTuple),
            calendar: $calendar,
            weekInfo: $weekInfo,
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

        return self::fromInstant(
            Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond]),
            zone: $zone,
            calendar: $calendar,
            weekInfo: $weekInfo,
        );
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

        return self::fromInstant(
            Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond]),
            zone: $zone,
            calendar: $calendar,
            weekInfo: $weekInfo,
        );
    }

    public static function fromDateTime(
        Date $date,
        ?Time $time = null,
        ?Zone $zone = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        $localDays = $date->calendar->getDaysSinceUnixEpochByYmd($date->year, $date->month, $date->dayOfMonth);
        $localTs   = $localDays * 60 * 60 * 24;
        $localTs  += $time ? $time->hour * 3600 + $time->minute * 60 + $time->second : 0;

        $zone   ??= new ZoneOffset(0);
        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;
        $ns     = $time ? $time->nanoOfSecond : 0;

        return self::fromInstant(
            Instant::fromUnixTimestampTuple([$ts, $ns]),
            zone: $zone,
            calendar: $date->calendar,
            weekInfo: $date->weekInfo,
        );
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
