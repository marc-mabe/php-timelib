<?php declare(strict_types=1);

namespace time;

final class ZonedDateTime implements Momented, Date, Time, Zoned
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
        get => LocalDate::fromYd($this->year, $this->dayOfYear, calendar: $this->calendar);
    }

    public LocalTime $time {
        get => LocalTime::fromHms($this->hour, $this->minute, $this->second, $this->nanoOfSecond);
    }

    public WeekInfo $weekInfo {
        get => $this->moment->weekInfo;
    }

    /** @var int<1,max> */
    public int $weekOfYear {
        get => $this->weekInfo->getWeekOfYear($this);
    }

    public int $yearOfWeek {
        get => $this->weekInfo->getYearOfWeek($this);
    }

    public readonly ZoneOffset $offset;

    private readonly Moment $adjusted;

    private function __construct(
        public readonly Moment $moment,
        public readonly Zone $zone,
    ) {
        $offset         = $zone->getOffsetAt($moment);
        $this->adjusted = $this->moment->add($offset->toDuration());
        $this->offset   = $offset;
    }

    public function add(Duration|Period $durationOrPeriod): self
    {
        if ($durationOrPeriod instanceof Duration) {
            return new self($this->moment->add($durationOrPeriod), $this->zone);
        }

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
            $this->zone,
            $year,
            $month,
            $day, /** @phpstan-ignore argument.type */
            $hour,
            $minute,
            $second,
            $ns,
            calendar: $this->calendar,
            weekInfo: $this->weekInfo,
            disambiguation: Disambiguation::COMPATIBLE,
        );
    }

    public function sub(Duration|Period $durationOrPeriod): self
    {
        return $this->add($durationOrPeriod->inverted());
    }

    public function withZoneSameMoment(Zone $zone): self
    {
        return new self($this->moment, $zone);
    }

    public function withZoneSameLocal(Zone $zone): self
    {
        return self::fromDateTime($zone, $this->date, $this->time);
    }

    public function withCalendar(Calendar $calendar): self
    {
        return $this->calendar === $calendar
            ? $this
            : new self($this->moment->withCalendar($calendar), $this->zone);
    }

    public function withWeekInfo(WeekInfo $weekInfo): self
    {
        return $this->weekInfo === $weekInfo
            ? $this
            : new self($this->moment->withWeekInfo($weekInfo), $this->zone);
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

    public static function fromUnixTimestamp(
        int|float $timestamp,
        TimeUnit $unit = TimeUnit::Second,
        ?Calendar $calendar = null,
        ?WeekInfo $weekInfo = null,
    ): self {
        return new self(
            Moment::fromUnixTimestamp($timestamp, $unit, $calendar, $weekInfo),
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
            Moment::fromUnixTimestampTuple($timestampTuple, $calendar, $weekInfo),
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
        return new self(Moment::fromUnixTimestampTuple([$ts, $nanoOfSecond], $calendar, $weekInfo), $zone);
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
        return new self(Moment::fromUnixTimestampTuple([$ts, $nanoOfSecond], $calendar, $weekInfo), $zone);
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
        return new self(Moment::fromUnixTimestampTuple([$ts, $ns], $date->calendar, $date->weekInfo), $zone);
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
        $maxTran = $zone->info->getTransitionAt(Moment::fromUnixTimestampTuple([$maxTs, 0]));
        \assert($maxTran !== null);
        $maxStart = $maxTran->moment->toUnixTimestampTuple()[0] + $maxTran->offset->totalSeconds;

        $minTs   = $localTs + ZoneOffset::TOTAL_SECONDS_MIN;
        $minTran = $zone->info->getTransitionAt(Moment::fromUnixTimestampTuple([$minTs, 0]));
        \assert($minTran !== null);
        $minStart = $minTran->moment->toUnixTimestampTuple()[0] + $minTran->offset->totalSeconds;
        $minEnd   = $maxTran->moment->toUnixTimestampTuple()[0] + $minTran->offset->totalSeconds;

        if ($minStart <= $localTs && $minEnd > $localTs) {
            if ($maxStart <= $localTs) {
                return match ($disambiguation) {
                    Disambiguation::EARLIER    => $minTran->offset,
                    Disambiguation::LATER      => $maxTran->offset,
                    Disambiguation::COMPATIBLE => $minTran->offset,
                    Disambiguation::REJECT => throw new \RuntimeException(sprintf(
                        "Ambiguous date-time '%s' for zone '%s'",
                        new DateTimeFormatter('Y-m-d H:i:s')->format(Moment::fromUnixTimestampTuple([$localTs, 0])),
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
                    new DateTimeFormatter('Y-m-d H:i:s')->format(Moment::fromUnixTimestampTuple([$localTs, 0])),
                    $zone->identifier
                )),
            };
        }

        return $maxTran->offset;
    }
}
