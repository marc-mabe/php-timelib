<?php

namespace time;

/**
 * A `Duration` is an amount of time that strictly follows the following definition:
 *   - each microsecond has 1000 nanoseconds
 *   - each millisecond has 1000 microseconds
 *   - each second has 1000 milliseconds
 *   - each minute has 60 seconds
 *   - each hour has 60 minutes
 *
 * It's measured in the number of seconds with nanosecond adjustment.
 *
 * NOTE: There is no DST (daylight saving time), nor leap seconds or similar edge cases involved.
 *
 * @see Period For date based equivalent.
 */
final class Duration
{
    public readonly int $seconds;

    /** @var int<0, 999999999> */
    public readonly int $nanoOfSeconds;

    public bool $isNegative {
        get => $this->seconds < 0;
    }

    public int $hours {
        get => \intdiv($this->seconds, 3_600);
    }

    public int $minutes {
        get => \intdiv($this->seconds, 60);
    }

    public int $minuteOfHours {
        get => $this->minutes % 60;
    }

    public int $secondOfMinutes {
        get => $this->seconds % 60;
    }

    public int $milliseconds {
        get => \intdiv($this->nanoOfSeconds, 1_000_000) + ($this->seconds * 1_000);
    }

    public int $milliOfSeconds {
        get => $this->milliseconds % 1_000;
    }

    public int $microseconds {
        get => \intdiv($this->nanoOfSeconds, 1_000) + ($this->seconds * 1_000_000);
    }

    public int $microOfSeconds {
        get => $this->milliseconds % 1_000;
    }

    public int $nanoseconds {
        get => $this->nanoOfSeconds + ($this->seconds * 1_000_000_000);
    }

    public function __construct(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
        int $nanoseconds = 0,
    ) {
        $ns = $nanoseconds + ($microseconds * 1_000) + ($milliseconds * 1_000_000);
        $s  = \intdiv($ns, 1_000_000_000) + $seconds + ($minutes * 60) + ($hours * 3_600);
        $ns = $ns % 1_000_000_000;

        // nanoOfSecond must be positive
        if ($ns < 0) {
            $s -= 1;
            $ns += 1_000_000_000;
        }

        $this->seconds = $s;
        $this->nanoOfSeconds = $ns;
    }

    public function isEmpty(): bool {
        return !$this->seconds && !$this->nanoOfSeconds;
    }

    public function toIso(): string
    {
        $timeIso  = '';
        $abs      = $this->isNegative ? $this->abs() : $this;

        if ($abs->hours) {
            $timeIso .= $abs->hours . 'H';
        }

        if ($abs->minuteOfHours) {
            $timeIso .= $abs->minuteOfHours . 'M';
        }

        $s  = $abs->secondOfMinutes;
        $ns = $abs->nanoOfSeconds;
        if ($s || $ns) {
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

    public function abs(): self
    {
        $s  = $this->seconds;
        $ns = $this->nanoOfSeconds;

        if ($s < 0) {
            $s *= -1;

            if ($ns) {
                $s -= 1;
                $ns = 1_000_000_000 - $ns;
            }
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    public function negated(): self
    {
        $s  = $this->seconds;
        $ns = $this->nanoOfSeconds;

        if ($s >= 0) {
            $s *= -1;

            if ($ns) {
                $s -= 1;
                $ns = 1_000_000_000 - $ns;
            }
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }
}
