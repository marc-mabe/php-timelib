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
    public bool $isZero {
        get => !($this->totalSeconds || $this->nanosOfSecond);
    }

    public bool $isNegative {
        get => $this->totalSeconds < 0;
    }

    public int $totalHours {
        get => \intdiv($this->totalSeconds, 3_600);
    }

    public int $totalMinutes {
        get => \intdiv($this->totalSeconds, 60);
    }

    /** @var int<0, 59> */
    public int $minutesOfHour {
        get => \abs($this->totalMinutes % 60);
    }

    public int $totalSeconds;

    /** @var int<0, 59> */
    public int $secondsOfMinute {
        get => \abs($this->totalSeconds % 60);
    }

    public int $totalMilliseconds {
        get => \intdiv($this->nanosOfSecond, 1_000_000) + ($this->totalSeconds * 1_000);
    }

    /** @var int<0, 999> */
    public int $millisOfSecond {
        /** @phpstan-ignore return.type */
        get => \intdiv($this->nanosOfSecond, 1_000_000);
    }

    public int $totalMicroseconds {
        get => \intdiv($this->nanosOfSecond, 1_000) + ($this->totalSeconds * 1_000_000);
    }

    /** @var int<0, 999999> */
    public int $microsOfSecond {
        /** @phpstan-ignore return.type */
        get => \intdiv($this->nanosOfSecond, 1_000);
    }

    public int $totalNanoseconds {
        get => $this->nanosOfSecond + ($this->totalSeconds * 1_000_000_000);
    }

    /** @var int<0, 999999999> */
    public int $nanosOfSecond;

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

        $this->totalSeconds = $s;
        $this->nanosOfSecond = $ns;
    }

    public function add(self $other): self
    {
        return new self(
            seconds: $this->totalSeconds + $other->totalSeconds,
            nanoseconds: $this->nanosOfSecond + $other->nanosOfSecond,
        );
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
        $ns = $abs->nanosOfSecond;
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

        $s  = $this->totalSeconds * -1;
        $ns = $this->nanosOfSecond;

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

    /**
     * Adds this duration to the specified unix timestamp tuple.
     *
     * @param array{int, int<0,999999999>} $tuple
     * @return array{int, int<0,999999999>}
     */
    public function addToUnixTimestampTuple(array $tuple): array
    {
        $ns = $tuple[1] + $this->nanosOfSecond;
        $s  = $tuple[0] + $this->totalSeconds + \intdiv($ns, 1_000_000_000);
        $ns = $ns % 1_000_000_000;
        return [$s, $ns];
    }
}
