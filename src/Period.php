<?php declare(strict_types=1);

namespace time;

final class Period {
    public function __construct(
        public readonly bool $isInverted = false,
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

    public function diff(Period $other): self
    {
        // TODO: Handle isInverted
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
        if (!$s && $ns < 0) {
            $timeIso .= '-0.' . \rtrim(\str_pad((string)($ns * -1), 9, '0', STR_PAD_LEFT), '0') . 'S';
        } elseif ($s || $ns) {
            if ($ns < 0) {
                $s = $s < 0 ? $s + 1 : $s - 1;
                $ns = 1_000_000_000 - $ns;
            }

            $timeIso .= $s;

            if ($ns) {
                $timeIso .= '.' . \rtrim(\str_pad((string)$ns, 9, '0', STR_PAD_LEFT), '0');
            }

            $timeIso .= 'S';
        }

        $dateTimeIso = $dateIso . ($timeIso !== '' ? 'T' . $timeIso : '');

        // An ISO period must have defined at least one value
        if ($dateTimeIso === '') {
            $dateTimeIso = '0D';
        }

        return $this->isInverted ? '-P' . $dateTimeIso : 'P' . $dateTimeIso;
    }

    public function abs(): self {
        return new self(
            isInverted: false,
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
            isInverted: true,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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
            isInverted: $this->isInverted,
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

    public function toLegacyInterval(): \DateInterval
    {
        return new \DateInterval($this->toIso());
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
