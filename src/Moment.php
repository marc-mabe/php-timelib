<?php declare(strict_types=1);

namespace time;

final class Moment implements Momented, Date, Time, Zoned
{
    public readonly Moment $moment;

    public int $year {
        get => $this->calendar->getYmdByUnixTimestamp($this->tsSec)[0];
    }

    public Month $month {
        get => $this->calendar->getYmdByUnixTimestamp($this->tsSec)[1];
    }

    public int $dayOfMonth {
        get => $this->calendar->getYmdByUnixTimestamp($this->tsSec)[2];
    }

    public int $dayOfYear {
        get {
            $date = $this->calendar->getYmdByUnixTimestamp($this->tsSec);
            return $this->calendar->getDayOfYearByYmd($date[0], $date[1], $date[2]);
        }
    }

    public DayOfWeek $dayOfWeek {
        get => $this->calendar->getDayOfWeekByUnixTimestamp($this->tsSec);
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
        get => LocalDate::fromYd($this->year, $this->dayOfYear, calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public ZoneOffset $zone {
        get => $this->zone ??= new ZoneOffset(0);
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->weekInfo->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => $this->weekInfo->getYearOfWeek($this);
    }

    /** @param int<0, 999999999> $nanoOfSecond */
    private function __construct(
        private readonly int $tsSec,
        public readonly int $nanoOfSecond,
        public readonly Calendar $calendar,
        public readonly WeekInfo $weekInfo,
    ) {
        $this->moment = $this;
    }

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Period) {
            $bias   = $durationOrPeriod->isNegative ? -1 : 1;
            $year   = $this->year + $durationOrPeriod->years * $bias;
            $month  = $this->month->value + $durationOrPeriod->months * $bias;
            $day    = $this->dayOfMonth + $durationOrPeriod->days * $bias + $durationOrPeriod->weeks * 7 * $bias;
            $hour   = $this->hour + $durationOrPeriod->hours * $bias;
            $minute = $this->minute + $durationOrPeriod->minutes * $bias;
            $second = $this->second + $durationOrPeriod->seconds * $bias;
            $ns     = $this->nanoOfSecond
                + $durationOrPeriod->milliseconds * 1_000_000 * $bias
                + $durationOrPeriod->microseconds * 1_000 * $bias
                + $durationOrPeriod->nanoseconds * $bias;

            $year  += \intdiv($month - 1, 12);
            $month = ($month - 1) % 12;
            if ($month < 0) {
                $year--;
                $month += 12;
            }
            $month += 1;

            if ($day >= 1) {
                while ($day > ($daysInMonth = $this->calendar->getDaysInMonth($year, $month)))  {
                    $day   -= $daysInMonth;
                    $month++;
                    if ($month > 12) {
                        $month = 1;
                        $year++;
                    }
                }
            } else {
                do {
                    $month--;
                    if ($month < 1) {
                        $month = 12;
                        $year--;
                    }

                    $daysInMonth = $this->calendar->getDaysInMonth($year, $month);
                    $day += $daysInMonth;
                } while ($day < 1);
            }

            $second += \intdiv($ns, 1_000_000_000);
            $ns     = $ns % 1_000_000_000;
            if ($ns < 0) {
                $second--;
                $ns += 1_000_000_000;
            }

            $minute += \intdiv($second, 60);
            $second = $second % 60;
            if ($second < 0) {
                $minute--;
                $second += 60;
            }

            $hour += \intdiv($minute, 60);
            $minute = $minute % 60;
            if ($minute < 0) {
                $hour--;
                $minute += 60;
            }

            $day += \intdiv($hour, 24);
            $hour = $hour % 24;
            if ($hour < 0) {
                $day--;
                $hour += 24;
            }

            if ($day >= 1) {
                $daysInMonth = $this->calendar->getDaysInMonth($year, $month);
                if ($day > $daysInMonth) {
                    $day -= $daysInMonth;
                    $month++;
                    if ($month > 12) {
                        $month = 1;
                        $year++;
                    }
                }
            } else {
                $month--;
                if ($month < 1) {
                    $month = 12;
                    $year--;
                }

                $daysInMonth = $this->calendar->getDaysInMonth($year, $month);
                $day += $daysInMonth;
            }


            return self::fromYmd(
                $year,
                $month,
                $day, /** @phpstan-ignore argument.type */
                $hour,
                $minute,
                $second,
                $ns,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            );
        }

        $tuple = $durationOrPeriod->addToUnixTimestampTuple([$this->tsSec, $this->nanoOfSecond]);
        return new self(
            $tuple[0],
            $tuple[1],
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
        );
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
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
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
        );
    }

    /** @param int<0, 999> $milliOfSecond */
    public function withMilliOfSecond(int $milliOfSecond): self
    {
        return new self(
            $this->tsSec,
            $milliOfSecond * 1_000_000,
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
        );
    }

    /** @param int<0, 999999> $microOfSecond */
    public function withMicroOfSecond(int $microOfSecond): self
    {
        return new self(
            $this->tsSec,
            $microOfSecond * 1_000,
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
        );
    }

    /** @param int<0, 999999999> $nanoOfSecond */
    public function withNanoOfSecond(int $nanoOfSecond): self
    {
        return new self(
            $this->tsSec,
            $nanoOfSecond,
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
        );
    }

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->tsSec, $this->nanoOfSecond, $calendar, $this->weekInfo);
    }

    public function withWeekInfo(WeekInfo $weekInfo): self
    {
        return $this->weekInfo === $weekInfo
            ? $this
            : new self($this->tsSec, $this->nanoOfSecond, $this->calendar, $weekInfo);
    }

    public function truncatedTo(DateUnit|TimeUnit $unit): self
    {
        return match ($unit) {
            DateUnit::Year => self::fromYd(
                $this->year,
                1,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            DateUnit::Month => self::fromYmd(
                $this->year,
                $this->month,
                1,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            DateUnit::Day => self::fromYd(
                $this->year,
                $this->dayOfYear,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            TimeUnit::Hour => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            TimeUnit::Minute => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            TimeUnit::Second => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            TimeUnit::Millisecond => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000_000) * 1_000_000, // @phpstan-ignore argument.type
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
            ),
            TimeUnit::Microsecond => self::fromYd(
                $this->year,
                $this->dayOfYear,
                $this->hour,
                $this->minute,
                $this->second,
                \intdiv($this->nanoOfSecond, 1_000) * 1_000, // @phpstan-ignore argument.type
                calendar: $this->calendar,
                weekInfo: $this->weekInfo,
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

    public function toZonedDateTime(Zone $zone): ZonedDateTime
    {
        return ZonedDateTime::fromUnixTimestampTuple($this->toUnixTimestampTuple(), $this->calendar, $this->weekInfo)
            ->withZoneSameMoment($zone);
    }

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
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

        return new self(
            $tsSecInt,
            $ns,
            calendar: $calendar ?? GregorianCalendar::getInstance(),
            weekInfo: $weekInfo ?? WeekInfo::fromIso(),
        );
    }

    /** @param array{int, int<0, 999999999>} $timestampTuple */
    public static function fromUnixTimestampTuple(
        array $timestampTuple,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return new self(
            $timestampTuple[0],
            $timestampTuple[1],
            calendar: $calendar ?? GregorianCalendar::getInstance(),
            weekInfo: $weekInfo ?? WeekInfo::fromIso(),
        );
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
        ?WeekInfo $weekInfo = null,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYd($year, $dayOfYear);
        $ts += $hour * 3600 + $minute * 60 + $second;

        return new self($ts, $nanoOfSecond, $calendar, $weekInfo ?? WeekInfo::fromIso());
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
        ?WeekInfo $weekInfo = null,
    ): self {
        $calendar ??= GregorianCalendar::getInstance();

        $ts = $calendar->getUnixTimestampByYmd($year, $month, $dayOfMonth);
        $ts += $hour * 3600 + $minute * 60 + $second;

        return new self($ts, $nanoOfSecond, $calendar, $weekInfo ?? WeekInfo::fromIso());
    }

    public static function fromDateTime(
        Date $date,
        ?Time $time = null,
        ?Zone $zone = null,
        Disambiguation $disambiguation = Disambiguation::REJECT,
    ): self {
        if ($zone === null) {
            return self::fromYd(
                $date->year,
                $date->dayOfYear,
                $time->hour ?? 0,
                $time->minute ?? 0,
                $time->second ?? 0,
                $time->nanoOfSecond ?? 0,
                calendar: $date->calendar,
                weekInfo: $date->weekInfo,
            );
        }

        return ZonedDateTime::fromDateTime($zone, $date, $time, disambiguation: $disambiguation)->moment;
    }
}
