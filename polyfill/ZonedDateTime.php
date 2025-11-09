<?php declare(strict_types=1);

namespace time;

final class ZonedDateTime implements Instanted, Date, Time, Zoned
{
    private const int SECONDS_PER_DAY = 24 * 3600;
    private const int MAX_OFFSET = 18 * 3600;

    /** @var array{int, int<1,99>, int<1,31>}  */
    private array $ymd {
        get {
            if (!isset($this->ymd)) {
                $days = \intdiv($this->adjustedSec, self::SECONDS_PER_DAY);
                $days -= (int)(($this->adjustedSec % self::SECONDS_PER_DAY) < 0);
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

    public int $dayOfYear {
        get => $this->calendar->getDayOfYearByYmd($this->ymd[0], $this->ymd[1], $this->ymd[2]);
    }

    public int $dayOfWeek {
        get {
            $days = \intdiv($this->adjustedSec, self::SECONDS_PER_DAY);
            $days -= (int)(($this->adjustedSec % self::SECONDS_PER_DAY) < 0);

            return $this->calendar->getDayOfWeekByDaysSinceUnixEpoch($days);
        }
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
        get => LocalDate::fromYmd($this->ymd[0], $this->ymd[1], $this->ymd[2], calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->calendar->getWeekOfYearByYmd(...$this->ymd);
    }

    public int $yearOfWeek {
        get => $this->calendar->getYearOfWeekByYmd(...$this->ymd);
    }

    public readonly ZoneOffset $offset;

    private readonly int $adjustedSec;

    private function __construct(
        public readonly Instant $instant,
        public readonly Zone $zone,
        public readonly Calendar $calendar,
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
            );
        }

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
            zone: $this->zone,
            calendar: $this->calendar,
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

    /** @return array{int, int<0,999999999>} */
    public function toUnixTimestampTuple(): array
    {
        return $this->instant->toUnixTimestampTuple();
    }

    public static function fromInstant(
        Instant $instant,
        Zone $zone = new ZoneOffset(0),
        ?Calendar $calendar = null,
    ): self {
        return new self(
            $instant,
            zone: $zone,
            calendar: $calendar ?? $instant->calendar,
        );
    }

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
        ?Calendar $calendar = null,
    ): self {
        return self::fromInstant(
            Instant::fromUnixTimestamp($timestamp, $unit),
            calendar: $calendar,
        );
    }

    /** @param array{int, int<0,999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(
        array $timestampTuple,
        ?Calendar $calendar = null,
    ): self {
        return self::fromInstant(
            Instant::fromUnixTimestampTuple($timestampTuple),
            calendar: $calendar,
        );
    }

    /**
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999999> $nanoOfSecond
     *
     * @throws AmbiguousValueException
     * @throws InvalidValueException
     */
    public static function fromYmd(
        int $year,
        int $month,
        int $dayOfMonth,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
        Zone $zone = new ZoneOffset(0),
        ?Calendar $calendar = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        $calendar ??= IsoCalendar::getInstance();

        $localDays = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        $localSecs = $hour * 3600 + $minute * 60 + $second;

        if ($localDays > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $localDays * self::SECONDS_PER_DAY > PHP_INT_MAX - self::MAX_OFFSET - $localSecs
            || $localDays < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($localDays === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $localSecs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-m-d H:i:sfP');
            $sf  = $second + $nanoOfSecond / 1_000_000_000;
            throw new RangeError(sprintf(
                "A ZonedDateTime of the %s must be between %s and %s, %s given",
                $calendar::class,
                $fmt->format(self::min(zone: $zone, calendar: $calendar)),
                $fmt->format(self::max(zone: $zone, calendar: $calendar)),
                "{$year}-{$month}-{$dayOfMonth} {$hour}:{$minute}:{$sf} {$zone->identifier}",
            ));
        }

        $localTs = $localDays < 0
            ? ($localDays + 1) * self::SECONDS_PER_DAY + $localSecs - self::SECONDS_PER_DAY
            : $localDays * self::SECONDS_PER_DAY + $localSecs;

        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;

        $zdt = self::fromInstant(
            Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond]),
            zone: $zone,
            calendar: $calendar,
        );
        $zdt->ymd = [$year, $month, $dayOfMonth];

        return $zdt;
    }

    /**
     * @param int<1,366> $dayOfYear
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999999> $nanoOfSecond
     *
     * @throws AmbiguousValueException
     * @throws InvalidValueException
     */
    public static function fromYd(
        int $year,
        int $dayOfYear,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $nanoOfSecond = 0,
        Zone $zone = new ZoneOffset(0),
        ?Calendar $calendar = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        $calendar ??= IsoCalendar::getInstance();

        $localDays = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        $localSecs = $hour * 3600 + $minute * 60 + $second;

        if ($localDays > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $localDays * self::SECONDS_PER_DAY > PHP_INT_MAX - self::MAX_OFFSET - $localSecs
            || $localDays < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($localDays === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $localSecs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-z H:i:sfP');
            $sf  = $second + $nanoOfSecond / 1_000_000_000;
            throw new RangeError(sprintf(
                "A ZonedDateTime of the %s must be between %s and %s, %s given",
                $calendar::class,
                $fmt->format(self::min(zone: $zone, calendar: $calendar)),
                $fmt->format(self::max(zone: $zone, calendar: $calendar)),
                "{$year}-{$dayOfYear} {$hour}:{$minute}:{$sf} {$zone->identifier}",
            ));
        }

        $localTs = $localDays < 0
            ? ($localDays + 1) * self::SECONDS_PER_DAY + $localSecs - self::SECONDS_PER_DAY
            : $localDays * self::SECONDS_PER_DAY + $localSecs;

        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;

        return self::fromInstant(
            Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond]),
            zone: $zone,
            calendar: $calendar,
        );
    }

    /**
     * @throws AmbiguousValueException
     * @throws InvalidValueException
     */
    public static function fromDateTime(
        Date $date,
        ?Time $time = null,
        Zone $zone = new ZoneOffset(0),
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        [$hour, $minute, $second, $nanoOfSecond] = $time
            ? [$time->hour, $time->minute, $time->second, $time->nanoOfSecond]
            : [0, 0, 0, 0];

        $localDays = $date->calendar->getDaysSinceUnixEpochByYmd($date->year, $date->month, $date->dayOfMonth);
        $localSecs = $hour * 3600 + $minute * 60 + $second;

        if ($localDays > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $localDays * self::SECONDS_PER_DAY > PHP_INT_MAX - self::MAX_OFFSET - $localSecs
            || $localDays < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($localDays === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $localSecs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-m-d H:i:sfP');
            $sf  = $second + $nanoOfSecond / 1_000_000_000;
            throw new RangeError(sprintf(
                "A ZonedDateTime of the %s must be between %s and %s, %s %s given",
                $date->calendar::class,
                $fmt->format(self::min(zone: $zone, calendar: $date->calendar)),
                $fmt->format(self::max(zone: $zone, calendar: $date->calendar)),
                new DateTimeFormatter('Y-m-d')->format($date),
                "{$hour}:{$minute}:{$sf} {$zone->identifier}",
            ));
        }

        $localTs = $localDays < 0
            ? ($localDays + 1) * self::SECONDS_PER_DAY + $localSecs - self::SECONDS_PER_DAY
            : $localDays * self::SECONDS_PER_DAY + $localSecs;

        $offset = self::findOffsetByLocalTimestamp($zone, $localTs, $disambiguation);
        $ts     = $localTs - $offset->totalSeconds;

        $zdt = self::fromInstant(
            Instant::fromUnixTimestampTuple([$ts, $nanoOfSecond]),
            zone: $zone,
            calendar: $date->calendar,
        );
        $zdt->ymd = [$date->year, $date->month, $date->dayOfMonth];

        return $zdt;
    }

    /**
     * @throws AmbiguousValueException
     * @throws InvalidValueException
     */
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
                    Disambiguation::REJECT     => throw new AmbiguousValueException(sprintf(
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
                Disambiguation::REJECT     => throw new InvalidValueException(sprintf(
                    "Invalid date-time '%s' for zone '%s'",
                    new DateTimeFormatter('Y-m-d H:i:s')->format(Instant::fromUnixTimestampTuple([$localTs, 0])),
                    $zone->identifier
                )),
            };
        }

        return $maxTran->offset;
    }

    public static function min(?Zone $zone = null, ?Calendar $calendar = null): self
    {
        return new self(
            Instant::fromUnixTimestampTuple([PHP_INT_MIN + self::MAX_OFFSET, 0]),
            zone: $zone ?? new ZoneOffset(0),
            calendar: $calendar ?? IsoCalendar::getInstance(),
        );
    }

    public static function max(?Zone $zone = null, ?Calendar $calendar = null): self
    {
        return new self(
            Instant::fromUnixTimestampTuple([PHP_INT_MAX - self::MAX_OFFSET, 999_999_999]),
            zone: $zone ?? new ZoneOffset(0),
            calendar: $calendar ?? IsoCalendar::getInstance(),
        );
    }
}
