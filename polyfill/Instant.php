<?php declare(strict_types=1);

namespace time;

final class Instant implements Instanted, Date, Time, Zoned
{
    public const int SECONDS_PER_MINUTE = 60;
    public const int SECONDS_PER_HOUR = 3600;
    public const int SECONDS_PER_DAY = 24 * self::SECONDS_PER_HOUR;

    public const int NANOS_PER_SECOND = 1_000_000_000;
    public const int MICROS_PER_SECOND = 1_000_000;
    public const int MILLIS_PER_SECOND = 1_000;

    public readonly Instant $instant;

    public IsoCalendar $calendar {
        get => IsoCalendar::getInstance();
    }

    /** @var array{int, int<1,12>, int<1,31>}  */
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

    /** @var int<1,12> */
    public int $month {
        get => $this->ymd[1];
    }

    public int $dayOfMonth {
        get => $this->ymd[2];
    }

    public int $dayOfYear {
        get => $this->calendar->getDayOfYearByYmd($this->ymd[0], $this->ymd[1], $this->ymd[2]);
    }

    /** @var int<1,7> */
    public int $dayOfWeek {
        get {
            $days = \intdiv($this->tsSec, self::SECONDS_PER_DAY);
            $days -= (int)(($this->tsSec % self::SECONDS_PER_DAY) < 0);

            return $this->calendar->getDayOfWeekByDaysSinceUnixEpoch($days);
        }
    }

    public int $hour {
        get {
            $remainder = $this->tsSec % self::SECONDS_PER_DAY;
            $remainder += ($remainder < 0) * self::SECONDS_PER_DAY;
            return \intdiv($remainder, self::SECONDS_PER_HOUR);
        }
    }

    public int $minute  {
        get {
            $remainder = $this->tsSec % self::SECONDS_PER_DAY;
            $remainder += ($remainder < 0) * self::SECONDS_PER_DAY;
            $hours = \intdiv($remainder, self::SECONDS_PER_HOUR);
            return \intdiv($remainder - $hours * self::SECONDS_PER_HOUR, self::SECONDS_PER_MINUTE);
        }
    }

    public int $second  {
        get {
            $remainder = $this->tsSec % self::SECONDS_PER_DAY;
            $remainder += ($remainder < 0) * self::SECONDS_PER_DAY;
            return $remainder % self::SECONDS_PER_MINUTE;
        }
    }

    public int $milliOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000_000);
    }

    public int $microOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000);
    }

    public PlainDateTime $local {
        get => PlainDateTime::fromDateTime($this->date, $this->time);
    }

    public PlainDate $date {
        get => PlainDate::fromYmd($this->ymd[0], $this->ymd[1], $this->ymd[2], calendar: $this->calendar);
    }

    public PlainTime $time {
        get => PlainTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public ZoneOffset $zone {
        get => $this->zone ??= new ZoneOffset(0);
    }

    /** @var int<1,53> */
    public int $weekOfYear {
        get => $this->calendar->getWeekOfYearByYmd(...$this->ymd);
    }

    public int $yearOfWeek {
        get => $this->calendar->getYearOfWeekByYmd(...$this->ymd);
    }

    /** @param int<0,999999999> $nanoOfSecond */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
    ) {
        $this->instant = $this;
    }

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Period) {
            $ymd = $this->calendar->addPeriodToYmd($durationOrPeriod, ...$this->ymd);

            return self::fromYmd(
                year: $ymd[0],
                month: $ymd[1],
                dayOfMonth: $ymd[2],
                hour: $this->hour,
                minute: $this->minute,
                second: $this->second,
                nanoOfSecond: $this->nanoOfSecond,
            );
        }

        $tuple = $durationOrPeriod->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self($tuple[0], $tuple[1]);
    }

    public function sub(Duration|Period $durationOrPeriod): self
    {
        return $this->add($durationOrPeriod->inverted());
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
        );
    }

    /** @param int<1,12> $month */
    public function withMonth(int $month): self
    {
        return self::fromYmd(
            $this->year,
            $month,
            $this->dayOfMonth,
            $this->hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
        );
    }

    /** @param int<1,31> $dayOfMonth */
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
        );
    }

    /** @param int<1,366> $dayOfYear */
    public function withDayOfYear(int $dayOfYear): self
    {
        return self::fromYd(
            $this->year,
            $dayOfYear,
            $this->hour,
            $this->minute,
            $this->second,
            $this->nanoOfSecond,
        );
    }

    /** @param int<0,23> $hour */
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
        );
    }

    /** @param int<0,59> $minute */
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
        );
    }

    /** @param int<0,59> $second */
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
        );
    }

    /** @param int<0,999> $milliOfSecond */
    public function withMilliOfSecond(int $milliOfSecond): self
    {
        return new self($this->tsSec, $milliOfSecond * 1_000_000);
    }

    /** @param int<0,999999> $microOfSecond */
    public function withMicroOfSecond(int $microOfSecond): self
    {
        return new self($this->tsSec, $microOfSecond * 1_000);
    }

    /** @param int<0,999999999> $nanoOfSecond */
    public function withNanoOfSecond(int $nanoOfSecond): self
    {
        return new self($this->tsSec, $nanoOfSecond);
    }

    public function truncatedTo(DateUnit|TimeUnit $unit): self
    {
        return match ($unit) {
            DateUnit::Year => self::fromYmd($this->year, 1, 1),
            DateUnit::Month => self::fromYmd($this->year, $this->month, 1),
            DateUnit::Day => self::fromYmd($this->year, $this->month, $this->dayOfMonth),
            TimeUnit::Hour => self::fromYmd($this->year, $this->month, $this->dayOfMonth, $this->hour),
            TimeUnit::Minute => self::fromYmd($this->year, $this->month, $this->dayOfMonth, $this->hour, $this->minute),
            TimeUnit::Second => self::fromYmd($this->year, $this->month, $this->dayOfMonth, $this->hour, $this->minute, $this->second),
            TimeUnit::Millisecond => self::fromYmd(
                $this->year,
                $this->month,
                $this->dayOfMonth,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000_000) * 1_000_000, // @phpstan-ignore argument.type
            ),
            TimeUnit::Microsecond => self::fromYmd(
                $this->year,
                $this->month,
                $this->dayOfMonth,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000) * 1_000, // @phpstan-ignore argument.type
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
                TimeUnit::Hour        => ($this->tsSec / self::SECONDS_PER_HOUR) + ($this->nanoOfSecond / self::NANOS_PER_SECOND / self::SECONDS_PER_HOUR),
                TimeUnit::Minute      => ($this->tsSec / self::SECONDS_PER_MINUTE) + ($this->nanoOfSecond / self::NANOS_PER_SECOND / self::SECONDS_PER_MINUTE),
                TimeUnit::Second      => ($this->tsSec + ($this->nanoOfSecond / self::NANOS_PER_SECOND)),
                TimeUnit::Millisecond => ($this->tsSec * self::MILLIS_PER_SECOND) + ($this->nanoOfSecond / 1_000_000),
                TimeUnit::Microsecond => ($this->tsSec * self::MICROS_PER_SECOND) + ($this->nanoOfSecond / 1_000),
                TimeUnit::Nanosecond  => ($this->tsSec * self::NANOS_PER_SECOND) + $this->nanoOfSecond,
            };
        }

        return match ($unit) {
            TimeUnit::Hour        => \intdiv($this->tsSec, self::SECONDS_PER_HOUR),
            TimeUnit::Minute      => \intdiv($this->tsSec, self::SECONDS_PER_MINUTE),
            TimeUnit::Second      => $this->tsSec,
            TimeUnit::Millisecond => ($this->tsSec * self::MILLIS_PER_SECOND) + \intdiv($this->nanoOfSecond, 1_000_000),
            TimeUnit::Microsecond => ($this->tsSec * self::MICROS_PER_SECOND) + \intdiv($this->nanoOfSecond, 1_000),
            TimeUnit::Nanosecond  => ($this->tsSec * self::NANOS_PER_SECOND) + $this->nanoOfSecond,
        };
    }

    /** @return array{int, int<0,999999999>} */
    public function toUnixTimestampTuple(): array {
        return [$this->tsSec, $this->nanoOfSecond];
    }

    public function toZonedDateTime(Zone $zone = new ZoneOffset(0), ?Calendar $calendar = null): ZonedDateTime
    {
        return ZonedDateTime::fromInstant(
            $this,
            zone: $zone,
            calendar: $calendar ?? IsoCalendar::getInstance(),
        );
    }

    /** @throws RangeError */
    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
    ): self {
        if (\is_float($timestamp)) {
            if (!\is_finite($timestamp)) {
                throw new RangeError('Timestamp must be a finite number.');
            }

            $tsFraction = \fmod($timestamp, 1);
            $tsInt      = $timestamp - $tsFraction;

            if ($unit === TimeUnit::Minute && (
                $tsInt > PHP_INT_MAX / self::SECONDS_PER_MINUTE || $tsInt < PHP_INT_MIN / self::SECONDS_PER_MINUTE
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in minutes must be between %f and %f',
                    PHP_INT_MAX / self::SECONDS_PER_MINUTE,
                    PHP_INT_MIN / self::SECONDS_PER_MINUTE,
                ));
            } elseif ($unit === TimeUnit::Hour && (
                $tsInt > PHP_INT_MAX / self::SECONDS_PER_HOUR || $tsInt < PHP_INT_MIN / self::SECONDS_PER_HOUR
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in hours must be between %f and %f',
                    PHP_INT_MIN / self::SECONDS_PER_HOUR,
                    PHP_INT_MAX / self::SECONDS_PER_HOUR,
                ));
            } elseif (
                (PHP_INT_SIZE === 8 && ($tsInt >= (float)PHP_INT_MAX || $tsInt < (float)PHP_INT_MIN))
                || (PHP_INT_SIZE === 4 && ($tsInt > (float)PHP_INT_MAX || $tsInt < (float)PHP_INT_MIN))
            ) {
                throw new RangeError(
                    'Timestamp must be between ' . PHP_INT_MIN . ' and ' . PHP_INT_MAX . '.999999999'
                );
            }

            $tsInt = (int)$tsInt;

        } else {
            if ($unit === TimeUnit::Minute && (
                $timestamp > PHP_INT_MAX / self::SECONDS_PER_MINUTE || $timestamp < PHP_INT_MIN / self::SECONDS_PER_MINUTE
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in minutes must be between %f and %f',
                    PHP_INT_MAX / self::SECONDS_PER_MINUTE,
                    PHP_INT_MIN / self::SECONDS_PER_MINUTE,
                ));
            } elseif ($unit === TimeUnit::Hour && (
                $timestamp > PHP_INT_MAX / self::SECONDS_PER_HOUR || $timestamp < PHP_INT_MIN / self::SECONDS_PER_HOUR
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in hours must be between %f and %f',
                    PHP_INT_MIN / self::SECONDS_PER_HOUR,
                    PHP_INT_MAX / self::SECONDS_PER_HOUR,
                ));
            }

            $tsInt      = $timestamp;
            $tsFraction = 0.0;
        }

        [$tsSec, $ns] = match ($unit) {
            TimeUnit::Second      => [$tsInt, (int)($tsFraction * self::NANOS_PER_SECOND)],
            TimeUnit::Millisecond => [
                \intdiv($tsInt, 1_000),
                ($tsInt % 1_000 * 1_000_000) - (int)($tsFraction * self::NANOS_PER_SECOND),
            ],
            TimeUnit::Microsecond => [
                \intdiv($tsInt, 1_000_000),
                ($tsInt % 1_000_000 * 1_000) - (int)($tsFraction * self::NANOS_PER_SECOND),
            ],
            TimeUnit::Nanosecond  => [
                \intdiv($tsInt, self::NANOS_PER_SECOND),
                $tsInt % self::NANOS_PER_SECOND,
            ],
            TimeUnit::Minute => [
                $tsInt * self::SECONDS_PER_MINUTE + (int)($tsFraction * self::SECONDS_PER_MINUTE),
                (int)(\fmod($tsFraction * self::SECONDS_PER_MINUTE, 1) * self::NANOS_PER_SECOND),
            ],
            TimeUnit::Hour => [
                $tsInt * self::SECONDS_PER_HOUR + (int)($tsFraction * self::SECONDS_PER_HOUR),
                (int)(\fmod($tsFraction * self::SECONDS_PER_HOUR, 1) * self::NANOS_PER_SECOND),
            ],
        };

        // Nanoseconds part must be positive
        if ($ns < 0) {
            $tsSec -= 1;
            $ns = self::NANOS_PER_SECOND + $ns;
        }
        assert($ns >= 0 && $ns < self::NANOS_PER_SECOND);

        return new self($tsSec, $ns);
    }

    /** @param array{int, int<0,999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self
    {
        return new self($timestampTuple[0], $timestampTuple[1]);
    }

    /**
     * @param int<1,366> $dayOfYear
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999999> $nanoOfSecond
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
        $calendar = IsoCalendar::getInstance();

        $days = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        $secs = $hour * self::SECONDS_PER_HOUR + $minute * self::SECONDS_PER_MINUTE + $second;

        if ($days > \intdiv(PHP_INT_MAX, self::SECONDS_PER_DAY)
            || $days * self::SECONDS_PER_DAY > PHP_INT_MAX - $secs
            || $days < \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
            || ($days === \intdiv(PHP_INT_MIN, self::SECONDS_PER_DAY) - (int)(PHP_INT_MIN % self::SECONDS_PER_DAY !== 0) // @phpstan-ignore notIdentical.alwaysTrue
                && $secs < self::SECONDS_PER_DAY + PHP_INT_MIN % self::SECONDS_PER_DAY
            )
        ) {
            $fmt = new DateTimeFormatter('Y-z H:i:sf');
            $sf  = $second + $nanoOfSecond / self::NANOS_PER_SECOND;
            throw new RangeError(sprintf(
                "An Instant must be between %s and %s, %s given",
                $fmt->format(self::min()),
                $fmt->format(self::max()),
                "{$year}-{$dayOfYear} {$hour}:{$minute}:{$sf}",
            ));
        }

        $ts = $days < 0
            ? ($days + 1) * self::SECONDS_PER_DAY + $secs - self::SECONDS_PER_DAY
            : $days * self::SECONDS_PER_DAY + $secs;

        return new self($ts, $nanoOfSecond);
    }

    /**
     * @param int<1,12> $month
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
    ): self {
        $calendar = IsoCalendar::getInstance();

        $days = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth);
        $secs = $hour * self::SECONDS_PER_HOUR + $minute * self::SECONDS_PER_MINUTE + $second;

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
                "An Instant must be between %s and %s, %s given",
                $fmt->format(self::min()),
                $fmt->format(self::max()),
                "{$year}-{$month}-{$dayOfMonth} {$hour}:{$minute}:{$sf}",
            ));
        }

        $ts = $days < 0
            ? ($days + 1) * self::SECONDS_PER_DAY + $secs - self::SECONDS_PER_DAY
            : $days * self::SECONDS_PER_DAY + $secs;

        return new self($ts, $nanoOfSecond);
    }

    public static function fromDateTime(
        Date $date,
        ?Time $time = null,
        Zone $zone = new ZoneOffset(0),
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        return ZonedDateTime::fromDateTime(
            $date,
            $time,
            zone: $zone,
            disambiguation: $disambiguation,
        )->instant;
    }

    public static function min(): self
    {
        return new self(PHP_INT_MIN, 0);
    }

    public static function max(): self
    {
        return new self(PHP_INT_MAX, 999_999_999);
    }
}
