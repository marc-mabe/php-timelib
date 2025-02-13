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
            $dateIso .= $this->years . 'Y';
        }
        if ($this->months) {
            $dateIso .= $this->months . 'M';
        }
        if ($this->days) {
            $dateIso .= $this->days . 'D';
        }

        if ($this->hours) {
            $timeIso .= $this->hours . 'H';
        }
        if ($this->minutes) {
            $timeIso .= $this->minutes . 'M';
        }

        $ns = $this->milliseconds * 1_000_000 + $this->microseconds * 1_000 + $this->nanoseconds;
        $s  = $this->seconds + \intdiv($ns, 1_000_000_000);
        $ns = $ns % 1_000_000_000;
        if ($s || $ns) {
            if ($ns < 0) {
                $s -= 1;
                $ns = 1_000_000_000 - $ns;
            }

            $timeIso .= $s;

            if ($ns) {
                $timeIso .= '.' . \rtrim(\str_pad($ns, 9, '0', STR_PAD_LEFT), '0');
            }

            $timeIso .= 'S';
        }

        $dateTimeIso = $dateIso . ($timeIso !== '' ? 'T' . $timeIso : '');

        // An ISO period must have defined at least one value
        if ($dateTimeIso === '') {
            $dateTimeIso = '0D';
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

    /**
     * Tests if this duration is standardized to the given time unit where
     *  - each microsecond as 1000 nanoseconds
     *  - each millisecond as 1000 microseconds
     *  - each second has 1000 milliseconds
     *  - each minute has 60 seconds
     *  - each hour has 60 minutes
     *  - each day has 24 hours
     */
    public function isStandardized(TimeUnit $unit): bool
    {
        $standardized = $this->nanoseconds < 1_000;
        if (!$standardized || $unit === TimeUnit::Nanosecond) {
            return $standardized;
        }

        $standardized = $this->microseconds < 1_000;
        if (!$standardized || $unit === TimeUnit::Microsecond) {
            return $standardized;
        }

        $standardized = $this->milliseconds < 1_000;
        if (!$standardized || $unit === TimeUnit::Millisecond) {
            return $standardized;
        }

        $standardized = $this->seconds < 60;
        if (!$standardized || $unit === TimeUnit::Second) {
            return $standardized;
        }

        $standardized = $this->minutes < 60;
        if (!$standardized || $unit === TimeUnit::Minute) {
            return $standardized;
        }

        $standardized = $this->hours < 24;
        if (!$standardized || $unit === TimeUnit::Hour) {
            return $standardized;
        }

        return true;
    }

    /**
     * Creates a new duration standardized to the given time unit where
     * - each microsecond as 1000 nanoseconds
     * - each millisecond as 1000 microseconds
     * - each second has 1000 milliseconds
     * - each minute has 60 seconds
     * - each hour has 60 minutes
     * - each day has 24 hours
     * - other parts of this duration are not touched
     *
     * Already standardized durations will return the same instance.
     *
     * @return self
     */
    public function standardizedTo(TimeUnit $unit): self
    {
        if ($this->isStandardized($unit)) {
            return $this;
        }

        $ns = $this->nanoseconds;
        $us = $this->microseconds;
        if ($ns >= 1_000) {
            $us += \floor($ns / 1_000);
            $ns = $ns % 1_000;
        }

        if ($unit === TimeUnit::Nanosecond) {
            return new Duration(
                isNegative: $this->isNegative,
                years: $this->years, months: $this->months, days: $this->days,
                hours: $this->hours, minutes: $this->minutes, seconds: $this->seconds,
                milliseconds: $this->milliseconds, microseconds: $us, nanoseconds: $ns,
            );
        }

        $ms = $this->milliseconds;
        if ($us >= 1_000) {
            $ms += \floor($us / 1_000);
            $us = $us % 1_000;
        }

        if ($unit === TimeUnit::Microsecond) {
            return new Duration(
                isNegative: $this->isNegative,
                years: $this->years, months: $this->months, days: $this->days,
                hours: $this->hours, minutes: $this->minutes, seconds: $this->seconds,
                milliseconds: $ms, microseconds: $us, nanoseconds: $ns,
            );
        }

        $s = $this->seconds;
        if ($ms >= 1_000) {
            $s += \floor($ms / 1_000);
            $ms = $ms % 1_000;
        }

        if ($unit === TimeUnit::Millisecond) {
            return new Duration(
                isNegative: $this->isNegative,
                years: $this->years, months: $this->months, days: $this->days,
                hours: $this->hours, minutes: $this->minutes, seconds: $s,
                milliseconds: $ms, microseconds: $us, nanoseconds: $ns,
            );
        }

        $m = $this->minutes;
        if ($s >= 60) {
            $m += \floor($s / 60);
            $s = $s % 60;
        }

        if ($unit === TimeUnit::Second) {
            return new Duration(
                isNegative: $this->isNegative,
                years: $this->years, months: $this->months, days: $this->days,
                hours: $this->hours, minutes: $m, seconds: $s,
                milliseconds: $ms, microseconds: $us, nanoseconds: $ns,
            );
        }

        $h = $this->hours;
        if ($m >= 60) {
            $h += \floor($m / 60);
            $m = $m % 60;
        }

        if ($unit === TimeUnit::Minute) {
            return new Duration(
                isNegative: $this->isNegative,
                years: $this->years, months: $this->months, days: $this->days,
                hours: $h, minutes: $m, seconds: $s,
                milliseconds: $ms, microseconds: $us, nanoseconds: $ns,
            );
        }

        $d = $this->days;
        if ($h >= 60) {
            $d += \floor($h / 60);
            $h = $h % 60;
        }

        return new Duration(
            isNegative: $this->isNegative,
            years: $this->years, months: $this->months, days: $d,
            hours: $h, minutes: $m, seconds: $s,
            milliseconds: $ms, microseconds: $us, nanoseconds: $ns,
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
