<?php

namespace time;

use DateInterval;

final class Duration {
    public function __construct(
        public readonly bool $isNegative = false,
        public readonly int $years = 0,
        public readonly int $months = 0,
        public readonly int $days = 0,
        public readonly int $hours = 0,
        public readonly int $minutes = 0,
        public readonly int $seconds = 0,
        public readonly int $milliseconds = 0,
        public readonly int $microseconds = 0,
        public readonly int $nanoseconds = 0,
    ) {}

    public function isEmpty(): bool {
        return !$this->hasDate() && !$this->hasTime();
    }

    public function hasDate(): bool {
        return $this->years || $this->months || $this->days;
    }

    public function hasTime(): bool {
        return $this->hours
            || $this->minutes
            || $this->seconds
            || $this->milliseconds
            || $this->microseconds
            || $this->nanoseconds;
    }

    public function diff(Duration $other): self
    {
        // TODO: Handle isNegative
        return new self(
            years: abs($this->years - $other->years),
            months: abs($this->months - $other->months),
            days: abs($this->days - $other->days),
            hours: abs($this->hours - $other->hours),
            minutes: abs($this->minutes - $other->minutes),
            seconds: abs($this->seconds - $other->seconds),
            milliseconds: abs($this->milliseconds - $other->milliseconds),
            microseconds: abs($this->microseconds - $other->microseconds),
            nanoseconds: abs($this->nanoseconds - $other->nanoseconds),
        );
    }

    public function toIso(): string {
        $dateIso = '';
        $timeIso = '';

        if ($this->years) {
            $dateIso .= 'Y' . $this->years;
        }
        if ($this->months) {
            $dateIso .= 'M' . $this->months;
        }
        if ($this->days) {
            $dateIso .= 'D' . $this->days;
        }

        if ($this->hours) {
            $timeIso .= 'H' . $this->hours;
        }
        if ($this->minutes) {
            $timeIso .= 'M' . $this->minutes;
        }
        if ($this->seconds) {
            $timeIso .= 'S' . $this->seconds;
        }

        // TODO: handle time fractions
        $dateTimeIso = $dateIso . ($timeIso !== '' ? 'T' . $timeIso : '');

        // Just "P" is not allowed as empty duration in ISO
        if ($dateTimeIso === '') {
            return 'P0D';
        }

        return $this->isNegative ? '-P' . $dateTimeIso : 'P' . $dateTimeIso;
    }

    public function abs(): self {
        return new self(
            isNegative: false,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function negated(): self {
        return new self(
            isNegative: true,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withYears(int $years): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withMonths(int $months): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withDays(int $days): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withHours(int $hours): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withMinutes(int $minutes): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withSeconds(int $seconds): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withMilliseconds(int $milliseconds): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withMicroseconds(int $microseconds): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    public function withNanoseconds(int $nanoseconds): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $nanoseconds,
        );
    }

    public function toLegacyInterval(): DateInterval
    {
        return new \DateInterval($this->toIso());
    }

    public static function fromUnit(DateUnit|TimeUnit $unit, int $value): self {
        $isNegative = $value < 0;
        $abs        = abs($value);

        return match ($unit) {
            DateUnit::Year => new self($isNegative, years: $abs),
            DateUnit::Month => new self($isNegative, months: $abs),
            DateUnit::Day => new self($isNegative, days: $abs),
            TimeUnit::Hour => new self($isNegative, hours: $abs),
            TimeUnit::Minute => new self($isNegative, minutes: $abs),
            TimeUnit::Second => new self($isNegative, seconds: $abs),
            TimeUnit::Millisecond => new self($isNegative, milliseconds: $abs),
            TimeUnit::Microsecond => new self($isNegative, microseconds: $abs),
            TimeUnit::Nanosecond => new self($isNegative, nanoseconds: $abs),
        };
    }

    public static function fromTime(Time $time): self
    {
        return new self(
            hours: $time->hour,
            minutes: $time->minute,
            seconds: $time->second,
            nanoseconds: $time->nanoOfSecond,
        );
    }

    public static function fromTimeDiff(Time $start, Time $end): self
    {
        return self::fromTime($start)->diff(self::fromTime($end));
    }

    public static function fromDateDiff(Date $start, Date $end): self {}

    public static function fromDateTimeDiff(Date&Time $start, Date&Time $end): self {}

    public static function fromIso(string $isoFormat): self {}

    public static function fromLegacyInterval(DateInterval $interval): self {
        return new self(
            isNegative: $interval->invert,
            years: $interval->y,
            months: $interval->m,
            days: $interval->d,
            hours: $interval->h,
            minutes: $interval->m,
            seconds: $interval->s,
            microseconds: $interval->f,
        );
    }
}
