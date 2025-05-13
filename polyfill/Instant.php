<?php declare(strict_types=1);

namespace time;

final class Instant implements Instanted, Date, Time, Zoned
{
    public readonly Instant $instant;

    public Calendar $calendar {
        get => GregorianCalendar::getInstance();
    }

    /** @var null|array{int, Month, int<1,31>}  */
    private ?array $ymd = null;

    public int $year {
        get => ($this->ymd ??= GregorianCalendar::getInstance()->getYmdByUnixTimestamp($this->tsSec))[0];
    }

    public Month $month {
        get => ($this->ymd ??= GregorianCalendar::getInstance()->getYmdByUnixTimestamp($this->tsSec))[1];
    }

    public int $dayOfMonth {
        get => ($this->ymd ??= GregorianCalendar::getInstance()->getYmdByUnixTimestamp($this->tsSec))[2];
    }

    public int $dayOfYear {
        get {
            $calendar = GregorianCalendar::getInstance();
            $this->ymd ??= $calendar->getYmdByUnixTimestamp($this->tsSec);
            return $calendar->getDayOfYearByYmd($this->ymd[0], $this->ymd[1], $this->ymd[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => GregorianCalendar::getInstance()->getDayOfWeekByUnixTimestamp($this->tsSec);
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
        get {
            $calendar = GregorianCalendar::getInstance();
            $this->ymd ??= $calendar->getYmdByUnixTimestamp($this->tsSec);
            return LocalDate::fromYmd($this->ymd[0], $this->ymd[1], $this->ymd[2], calendar: $calendar);
        }
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public ZoneOffset $zone {
        get => $this->zone ??= new ZoneOffset(0);
    }

    public WeekInfo $weekInfo {
        get => WeekInfo::fromIso();
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => WeekInfo::fromIso()->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => WeekInfo::fromIso()->getYearOfWeek($this);
    }

    /** @param int<0, 999999999> $nanoOfSecond */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
    ) {
        $this->instant = $this;
    }

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Period) {
            $calendar = GregorianCalendar::getInstance();
            $this->ymd ??= $calendar->getYmdByUnixTimestamp($this->tsSec);

            $ymdHms = $durationOrPeriod->addToYmd(
                $this->ymd[0],
                $this->ymd[1],
                $this->ymd[2],
                $this->hour,
                $this->minute,
                $this->second,
                $this->nanoOfSecond,
                calendar: $calendar,
            );

            return self::fromYmd(
                $ymdHms[0],
                $ymdHms[1],
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

    /** @param Month|int<1, 12> $month */
    public function withMonth(Month|int $month): self
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

    /** @param int<1, 31> $dayOfMonth */
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

    /** @param int<1, 366> $dayOfYear */
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

    /** @param int<0, 23> $hour */
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

    /** @param int<0, 59> $minute */
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

    /** @param int<0, 59> $second */
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

    /** @param int<0, 999> $milliOfSecond */
    public function withMilliOfSecond(int $milliOfSecond): self
    {
        return new self($this->tsSec, $milliOfSecond * 1_000_000);
    }

    /** @param int<0, 999999> $microOfSecond */
    public function withMicroOfSecond(int $microOfSecond): self
    {
        return new self($this->tsSec, $microOfSecond * 1_000);
    }

    /** @param int<0, 999999999> $nanoOfSecond */
    public function withNanoOfSecond(int $nanoOfSecond): self
    {
        return new self($this->tsSec, $nanoOfSecond);
    }

    public function truncatedTo(DateUnit|TimeUnit $unit): self
    {
        return match ($unit) {
            DateUnit::Year => self::fromYmd($this->year, Month::January, 1),
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

    /** @return array{int, int<0, 999999999>} */
    public function toUnixTimestampTuple(): array {
        return [$this->tsSec, $this->nanoOfSecond];
    }

    public function toZonedDateTime(
        ?Zone $zone = null,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): ZonedDateTime {
        return ZonedDateTime::fromInstant(
            $this,
            zone: $zone ?? new ZoneOffset(0),
            calendar: $calendar ?? GregorianCalendar::getInstance(),
            weekInfo: $weekInfo ?? WeekInfo::fromIso(),
        );
    }

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
    ): self {
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
            TimeUnit::Minute => [(int)($timestamp * 60), (int)($timestamp * 60 / 1_000_000_000)],
            TimeUnit::Hour => [(int)($timestamp * 3600), (int)($timestamp * 3600 / 1_000_000_000)],
        };
        assert($ns >= 0 && $ns <1_000_000_000);

        return new self($tsSecInt, $ns);
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(array $timestampTuple): self
    {
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
        ?Calendar $calendar = null,
    ): self {
        $calendar = $calendar ?? GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYd($year, $dayOfYear);
        $ts += $hour * 3600 + $minute * 60 + $second;

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
        ?Calendar $calendar = null,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYmd($year, $month, $dayOfMonth);
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
