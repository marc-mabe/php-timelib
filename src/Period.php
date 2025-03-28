<?php declare(strict_types=1);

namespace time;

final class Period {
    public bool $isZero {
        get => !($this->hasDate || $this->hasTime);
    }

    public bool $hasDate {
        get => $this->years || $this->months || $this->days;
    }

    public bool $hasTime {
        get => $this->hours || $this->minutes || $this->seconds || $this->milliseconds || $this->microseconds || $this->nanoseconds;
    }

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

    public function equals(self $other): bool
    {
        if ($this->isNegative !== $other->isNegative) {
            $other = $other->allInverted();
        }

        return $this->years === $other->years
            && $this->months === $other->months
            && $this->days === $other->days
            && $this->hours === $other->hours
            && $this->minutes === $other->minutes
            && $this->seconds === $other->seconds
            && $this->milliseconds === $other->milliseconds
            && $this->microseconds === $other->microseconds
            && $this->nanoseconds === $other->nanoseconds;
    }

    public function add(self $other): self
    {
        if ($this->isNegative !== $other->isNegative) {
            $other = $other->allInverted();
        }

        return new self(
            years: $this->years + $other->years,
            months: $this->months + $other->months,
            days: $this->days + $other->days,
            hours: $this->hours + $other->hours,
            minutes: $this->minutes + $other->minutes,
            seconds: $this->seconds + $other->seconds,
            milliseconds: $this->milliseconds + $other->milliseconds,
            microseconds: $this->microseconds + $other->microseconds,
            nanoseconds: $this->nanoseconds + $other->nanoseconds,
        );
    }

    public function diff(self $other): self
    {
        $self  = $this->isNegative ? $this->allInverted() : $this;
        $other = $other->isNegative ? $other->allInverted() : $other;

        return new self(
            years: \abs($self->years - $other->years),
            months: \abs($self->months - $other->months),
            days: \abs($self->days - $other->days),
            hours: \abs($self->hours - $other->hours),
            minutes: \abs($self->minutes - $other->minutes),
            seconds: \abs($self->seconds - $other->seconds),
            milliseconds: \abs($self->milliseconds - $other->milliseconds),
            microseconds: \abs($self->microseconds - $other->microseconds),
            nanoseconds: \abs($self->nanoseconds - $other->nanoseconds),
        );
    }

    /**
     * Inverts all fields incl. the "isNegative" flag resulting in the same meaning of this period.
     *
     * E.g. "P1Y" will result in "-P-1Y" and vise versa.
     */
    public function allInverted(): self
    {
        return new self(
            isNegative: !$this->isNegative,
            years: $this->years * -1,
            months: $this->months * -1,
            days: $this->days * -1,
            hours: $this->hours * -1,
            minutes: $this->minutes * -1,
            seconds: $this->seconds * -1,
            milliseconds: $this->milliseconds * -1,
            microseconds: $this->microseconds * -1,
            nanoseconds: $this->nanoseconds * -1,
        );
    }

    public function inverted(): self
    {
        return new self(
            isNegative: !$this->isNegative,
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

    public function abs(): self {
        return $this->isNegative ? $this->inverted() : $this;
    }

    public function negated(): self {
        return $this->isNegative ? $this : $this->inverted();
    }

    /**
     * The months unit is adjusted to have an absolute value less than +/-11,
     * with the years unit being adjusted to compensate.
     *
     * The sign of the years and months units will be the same after normalization.
     * E.g, a period of "1 year and -25 months" will be normalized to "-1 year and -1 month".
     *
     * All other units will be unaffected by this.
     */
    public function withNormalizedMonthsIntoYears(): self
    {
        if ($this->months < 12 && $this->months > -12) {
            return $this;
        }

        return new self(
            isNegative: $this->isNegative,
            years: $this->years + \intdiv($this->months, 12),
            months: $this->months % 12,
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
     * The fraction units are adjusted to have an absolute value less than +/-1000,
     * with the other milli-, micro- and seconds units being adjusted to compensate.
     *
     * The sign of the seconds and fraction units will be the same after normalization.
     * E.g, a period of "789s -1234ms 2345us and -3456ns" will be normalized to "787s 768ms 341us and 544n".
     *
     * All other units will be unaffected by this.
     */
    public function withNormalizedFractionsIntoSeconds(): self
    {
        $us = $this->microseconds + \intdiv($this->nanoseconds, 1_000);
        $ns = $this->nanoseconds % 1_000;

        $ms = $this->milliseconds + \intdiv($us, 1_000);
        $us = $us % 1_000;

        $s = $this->seconds + \intdiv($ms, 1_000);
        $ms = $ms % 1_000;

        if ($s < 0 !== $ms < 0) {
            if ($s < 0) {
                $s += 1;
                $ms = 1_000 - $ms;
            } else {
                $s -= 1;
                $ms = 1_000 + $ms;
            }
        }

        if ($ms < 0 !== $us < 0) {
            if ($ms < 0) {
                $ms += 1;
                $us = 1_000 - $us;
            } else {
                $ms -= 1;
                $us = 1_000 + $us;
            }
        }

        if ($us < 0 !== $ns < 0) {
            if ($us < 0) {
                $us += 1;
                $ns = 1_000 - $ns;
            } else {
                $us -= 1;
                $ns = 1_000 + $ns;
            }
        }

        return (
            $this->seconds === $s
            && $this->milliseconds === $ms
            && $this->microseconds === $us
            && $this->nanoseconds === $ns
        ) ? $this : new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            days: $this->days,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $s,
            milliseconds: $ms,
            microseconds: $us,
            nanoseconds: $ns,
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

        if (!$s && $ns) {
            $timeIso .= ($ns < 0 ? '-' : '')
                . '0.'
                . \rtrim(\str_pad((string)\abs($ns), 9, '0', STR_PAD_LEFT), '0')
                . 'S';
        } else {
            if ($ns && $s < 0 !== $ns < 0) {
                if ($s < 0) {
                    $s += 1;
                    $ns = 1_000_000_000 - $ns;
                } else {
                    $s -= 1;
                    $ns = 1_000_000_000 + $ns;
                }
            }

            if ($s || $ns) {
                $timeIso .= $s;

                if ($ns) {
                    $timeIso .= '.' . \rtrim(\str_pad((string)\abs($ns), 9, '0', STR_PAD_LEFT), '0');
                }

                $timeIso .= 'S';
            }
        }

        $dateTimeIso = $dateIso . ($timeIso !== '' ? 'T' . $timeIso : '');

        // An ISO period must have defined at least one value
        if ($dateTimeIso === '') {
            $dateTimeIso = '0D';
        }

        return $this->isNegative ? '-P' . $dateTimeIso : 'P' . $dateTimeIso;
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
}
