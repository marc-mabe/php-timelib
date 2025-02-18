<?php declare(strict_types=1);

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
    private readonly int $s;

    /** @var int<0, 999999999> */
    private readonly int $ns;

    public bool $isZero {
        get => !$this->s && !$this->ns;
    }

    public bool $isNegative {
        get => $this->s < 0;
    }

    public int $totalHours {
        get => \intdiv($this->totalSeconds, 3_600);
    }

    public int $totalMinutes {
        get => \intdiv($this->totalSeconds, 60);
    }

    public int $minutesOfHour {
        get => $this->totalMinutes % 60;
    }

    public int $totalSeconds {
        get => ($this->isNegative && $this->ns)  ? $this->s - 1 : $this->s;
    }

    public int $secondsOfMinute {
        get => $this->totalSeconds % 60;
    }

    public int $totalMilliseconds {
        get => \intdiv($this->ns, 1_000_000) + ($this->s * 1_000);
    }

    public int $millisOfSecond {
        get => $this->totalMilliseconds % 1_000;
    }

    public int $totalMicroseconds {
        get => \intdiv($this->ns, 1_000) + ($this->s * 1_000_000);
    }

    public int $microsOfSecond {
        get => $this->totalMilliseconds % 1_000_000;
    }

    public int $totalNanoseconds {
        get => $this->ns + ($this->s * 1_000_000_000);
    }

    public int $nanosOfSecond {
        get => $this->totalNanoseconds % 1_000_000_000;
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

        $this->s = $s;
        $this->ns = $ns;
    }

    public function toIso(): string
    {
        $timeIso  = '';
        $abs      = $this->isNegative ? $this->abs() : $this;

        if ($abs->totalHours) {
            $timeIso .= $abs->totalHours . 'H';
        }

        if ($abs->minutesOfHour) {
            $timeIso .= $abs->minutesOfHour . 'M';
        }

        $s  = $abs->secondsOfMinute;
        $ns = $abs->ns;
        if ($s || $ns) {
            $timeIso .= $s;

            if ($ns) {
                $timeIso .= '.' . \rtrim(\str_pad((string)$ns, 9, '0', STR_PAD_LEFT), '0');
            }

            $timeIso .= 'S';
        }

        // An ISO period must have defined at least one value
        if ($timeIso === '') {
            $timeIso = '0S';
        }

        return $this->isNegative ? '-PT' . $timeIso : 'PT' . $timeIso;
    }

    public function inverted(): self
    {
        if ($this->isZero) {
            return $this;
        }

        $s  = $this->s * -1;
        $ns = $this->ns;

        if ($ns) {
            $s -= 1;
            $ns = 1_000_000_000 - $ns;
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    public function abs(): self
    {
        return $this->isNegative ? $this->inverted() : $this;
    }

    public function negated(): self
    {
        return !$this->isNegative ? $this->inverted() : $this;
    }
}
