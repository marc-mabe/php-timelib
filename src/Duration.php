<?php

namespace time;

/**
 * A `Duration` is an amount of time without dates which strictly follows the following definition:
 *   - each microsecond has 1000 nanoseconds
 *   - each millisecond has 1000 microseconds
 *   - each second has 1000 milliseconds
 *   - each minute has 60 seconds
 *   - each hour has 60 minutes
 *
 * A normalized `Duration` is an instance of `Duration` with no negative values
 * and all values to be in the range of the above definition.
 *
 * NOTE: There is no DST (daylight saving time), nor leap seconds or similar edge cases involved.
 *
 * @see Period For date based equivalent.
 */
final class Duration
{
    /**
     * Are all values defined in their normalized form?
     */
    public bool $isNormalized {
        get => $this->nanoseconds < 1_000 && $this->nanoseconds >= 0
            && $this->microseconds < 1_000 && $this->microseconds >= 0
            && $this->milliseconds < 1_000 && $this->milliseconds >= 0
            && $this->seconds < 60 && $this->seconds >= 0
            && $this->minutes < 60 && $this->minutes >= 0
            && $this->hours >= 0;
    }

    public function __construct(
        public readonly bool $isNegative = false,
        public readonly int $hours = 0,
        public readonly int $minutes = 0,
        public readonly int $seconds = 0,
        public readonly int $milliseconds = 0,
        public readonly int $microseconds = 0,
        public readonly int $nanoseconds = 0,
    ) {}

    public function isEmpty(): bool {
        return !$this->hours
            && !$this->minutes
            && !$this->seconds
            && !$this->milliseconds
            && !$this->microseconds
            && !$this->nanoseconds;
    }

    public function diff(self $other): self
    {
        // TODO: Handle isNegative
        return new self(
            hours: abs($this->hours - $other->hours),
            minutes: abs($this->minutes - $other->minutes),
            seconds: abs($this->seconds - $other->seconds),
            milliseconds: abs($this->milliseconds - $other->milliseconds),
            microseconds: abs($this->microseconds - $other->microseconds),
            nanoseconds: abs($this->nanoseconds - $other->nanoseconds),
        );
    }

    public function toIso(): string
    {
        $timeIso = '';

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
            $timeIso .= '-0.' . \rtrim(\str_pad($ns * -1, 9, '0', STR_PAD_LEFT), '0') . 'S';
        } elseif ($s || $ns) {
            if ($ns < 0) {
                $s = $s < 0 ? $s + 1 : $s - 1;
                $ns = 1_000_000_000 - $ns;
            }

            $timeIso .= $s;

            if ($ns) {
                $timeIso .= '.' . \rtrim(\str_pad($ns, 9, '0', STR_PAD_LEFT), '0');
            }

            $timeIso .= 'S';
        }

        // An ISO period must have defined at least one value
        if ($timeIso === '') {
            $timeIso = '0S';
        }

        return $this->isNegative ? '-PT' . $timeIso : 'PT' . $timeIso;
    }

    /**
     * Sets the `isNegative` flag to false.
     *
     * NOTE: Values will not be modified.
     *
     * @see $isNegative
     * @see $isNormalized
     * @see normalized())
     */
    public function abs(): self {
        return new self(
            isNegative: false,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    /**
     * Sets the `isNegative` flag to true.
     *
     * NOTE: Values will not be modified.
     *
     * @see $isNegative
     * @see $isNormalized
     * @see normalized()
     */
    public function negated(): self
    {
        return new self(
            isNegative: true,
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $this->nanoseconds,
        );
    }

    /**
     * Inverts the `isNegative` flag.
     *
     * @see $isNegative
     * @see $isNormalized
     * @see normalized()
     */
    public function inverted(): self
    {
        return $this->isNegative ? $this->abs() : $this->negated();
    }

    /**
     * Returns a new `Duration` instance with all values been converted to their normalized form
     * without changing the actual meaning of the duration.
     *
     * On an already normalized `Duration` the method will return the same instance unmodified.
     *
     * @see $isNormalized
     */
    public function normalized(): self
    {
        if ($this->isNormalized) {
            return $this;
        }

        $isNeg = $this->isNegative;
        $ns = $this->nanoseconds;
        $us = $this->microseconds;
        $ms = $this->milliseconds;
        $s = $this->seconds;
        $m = $this->minutes;
        $h = $this->hours;

        if ($ns >= 1_000 || $ns <= -1_000) {
            $us += \intdiv($ns, 1_000);
            $ns = $ns % 1_000;
        }

        if ($us >= 1_000 || $us <= -1_000) {
            $ms += \intdiv($us, 1_000);
            $us = $us % 1_000;
        }

        if ($ms >= 1_000 || $ms <= -1_000) {
            $s += \intdiv($ms, 1_000);
            $ms = $ms % 1_000;
        }

        if ($s >= 60 || $s <= -60) {
            $m += \intdiv($s, 60);
            $s = $s % 60;
        }

        if ($m >= 60 || $m <= -60) {
            $h += \intdiv($m, 60);
            $m = $m % 60;
        }

        if ($h < 0
            || (!$h && $m < 0)
            || ((!$h && !$m) && $s < 0)
            || ((!$h && !$m && !$s) && $ms < 0)
            || ((!$h && !$m && !$s && !$ms) && $us < 0)
            || ((!$h && !$m && !$s && !$ms && !$us) && $ns < 0)
        ) {
            $isNeg = !$isNeg;
            $h = $h * -1;
            $m = $m * -1;
            $s = $s * -1;
            $ms = $ms * -1;
            $us = $us * -1;
            $ns = $ns * -1;

            if ($m < 0) {
                $h -= 1;
                $m = 60 + $m;
            }

            if ($s < 0) {
                $m -= 1;
                $s = 60 + $s;
            }

            if ($ms < 0) {
                $s -= 1;
                $ms = 1_000 + $ms;
            }

            if ($us < 0) {
                $ms -= 1;
                $us = 1_000 + $us;
            }

            if ($ns < 0) {
                $us -= 1;
                $ns = 1_000 + $ns;
            }
        }

        return new self(
            isNegative: $isNeg,
            hours: $h, minutes: $m, seconds: $s,
            milliseconds: $ms, microseconds: $us, nanoseconds: $ns,
        );
    }

    public function withHours(int $hours): self
    {
        return new self(
            isNegative: $this->isNegative,
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
            hours: $this->hours,
            minutes: $this->minutes,
            seconds: $this->seconds,
            milliseconds: $this->milliseconds,
            microseconds: $this->microseconds,
            nanoseconds: $nanoseconds,
        );
    }

    public function toPeriod(): Period
    {
        return new Period(
            isInverted: $this->isNegative,
            hours: $this->hours, minutes: $this->minutes, seconds: $this->seconds,
            milliseconds: $this->milliseconds, microseconds: $this->microseconds, nanoseconds: $this->nanoseconds,
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

    public static function fromDateTimeDiff(Date&Time $start, Date&Time $end): self {}

    public static function fromIso(string $isoFormat): self {}
}
