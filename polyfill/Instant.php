<?php declare(strict_types=1);

namespace time;

final class Instant implements Instanted, Date, Time, Zoned
{
    public const int SECONDS_PER_DAY = 24 * 3600;

    public readonly Instant $instant;

    public GregorianCalendar $calendar {
        get => $this->calendar ??= new GregorianCalendar();
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
        get => LocalDate::fromYmd($this->ymd[0], $this->ymd[1], $this->ymd[2], calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
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
                $ymdHms[0],
                $ymdHms[1], // @phpstan-ignore argument.type
                $ymdHms[2],
                $ymdHms[3],
                $ymdHms[4],
                $ymdHms[5],
                $ymdHms[6],
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

    /** @return array{int, int<0,999999999>} */
    public function toUnixTimestampTuple(): array {
        return [$this->tsSec, $this->nanoOfSecond];
    }

    public function toZonedDateTime(?Zone $zone = null, ?Calendar $calendar = null): ZonedDateTime
    {
        return ZonedDateTime::fromInstant(
            $this,
            zone: $zone ?? new ZoneOffset(0),
            calendar: $calendar ?? new GregorianCalendar(),
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
                $tsInt > PHP_INT_MAX / 60 || $tsInt < PHP_INT_MIN / 60
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in minutes must be between %f and %f',
                    PHP_INT_MAX / 60,
                    PHP_INT_MIN / 60,
                ));
            } elseif ($unit === TimeUnit::Hour && (
                $tsInt > PHP_INT_MAX / 3600 || $timestamp < PHP_INT_MIN / 3600
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in hours must be between %f and %f',
                    PHP_INT_MIN / 3600,
                    PHP_INT_MAX / 3600,
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
                $timestamp > PHP_INT_MAX / 60 || $timestamp < PHP_INT_MIN / 60
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in minutes must be between %f and %f',
                    PHP_INT_MAX / 60,
                    PHP_INT_MIN / 60,
                ));
            } elseif ($unit === TimeUnit::Hour && (
                $timestamp > PHP_INT_MAX / 3600 || $timestamp < PHP_INT_MIN / 3600
            )) {
                throw new RangeError(\sprintf(
                    'Timestamp in hours must be between %f and %f',
                    PHP_INT_MIN / 3600,
                    PHP_INT_MAX / 3600,
                ));
            }

            $tsInt      = $timestamp;
            $tsFraction = 0.0;
        }

        [$tsSec, $ns] = match ($unit) {
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
            TimeUnit::Minute => [
                $tsInt * 60 + (int)($tsFraction * 60),
                (int)(\fmod($tsFraction * 60, 1) * 1_000_000_000),
            ],
            TimeUnit::Hour => [
                $tsInt * 3600 + (int)($tsFraction * 3600),
                (int)(\fmod($tsFraction * 3600, 1) * 1_000_000_000),
            ],
        };

        // Nanoseconds part must be positive
        if ($ns < 0) {
            $tsSec -= 1;
            $ns = 1_000_000_000 + $ns;
        }
        assert($ns >= 0 && $ns < 1_000_000_000);

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
        $calendar = new GregorianCalendar();

        $ts = $calendar->getDaysSinceUnixEpochByYd($year, $dayOfYear) * self::SECONDS_PER_DAY;
        $ts += $hour * 3600 + $minute * 60 + $second;

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
        $calendar = new GregorianCalendar();

        $ts = $calendar->getDaysSinceUnixEpochByYmd($year, $month, $dayOfMonth) * self::SECONDS_PER_DAY;
        $ts += $hour * 3600 + $minute * 60 + $second;

        return new self($ts, $nanoOfSecond);
    }

    public static function fromDateTime(
        Date $date,
        ?Time $time = null,
        ?Zone $zone = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        return ZonedDateTime::fromDateTime(
            $date,
            $time,
            zone: $zone,
            disambiguation: $disambiguation,
        )->instant;
    }
}
