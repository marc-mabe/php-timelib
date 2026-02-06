<?php declare(strict_types=1);

namespace time;

/**
 * A `Duration` is a relative amount of time that strictly follows the following definition:
 *   - each microsecond has 1000 nanoseconds
 *   - each millisecond has 1000 microseconds
 *   - each second has 1000 milliseconds
 *   - each minute has 60 seconds
 *   - each hour has 60 minutes
 *
 * It's measured in the number of total seconds with nanosOfSecond adjustment.
 *
 * NOTE: There is no DST (daylight saving time), nor leap seconds or similar edge cases involved.
 *
 * @see Period for date based equivalent.
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

    /** @var int<0,59> */
    public int $minutesOfHour {
        get => \abs($this->totalMinutes % 60);
    }

    public readonly int $totalSeconds;

    /** @var int<0,59> */
    public int $secondsOfMinute {
        get => \abs($this->totalSeconds % 60);
    }

    public int $totalMilliseconds {
        get {
            $totalMilliseconds = \intdiv($this->nanosOfSecond, 1_000_000) + ($this->totalSeconds * 1_000);

            if (\is_float($totalMilliseconds)) { // @phpstan-ignore function.impossibleType
                throw new RangeError('Total milliseconds overflowed');
            }

            return $totalMilliseconds;
        }
    }

    /** @var int<0,999> */
    public int $millisOfSecond {
        /** @phpstan-ignore return.type */
        get => \intdiv($this->nanosOfSecond, 1_000_000);
    }

    public int $totalMicroseconds {
        get {
            $totalMicroseconds = \intdiv($this->nanosOfSecond, 1_000_000) + ($this->totalSeconds * 1_000_000);

            if (\is_float($totalMicroseconds)) { // @phpstan-ignore function.impossibleType
                throw new RangeError('Total microseconds overflowed');
            }

            return $totalMicroseconds;
        }
    }

    /** @var int<0,999999> */
    public int $microsOfSecond {
        /** @phpstan-ignore return.type */
        get => \intdiv($this->nanosOfSecond, 1_000);
    }

    public int $totalNanoseconds {
        get {
            $totalNanoseconds = \intdiv($this->nanosOfSecond, 1_000_000) + ($this->totalSeconds * 1_000_000_000);

            if (\is_float($totalNanoseconds)) { // @phpstan-ignore function.impossibleType
                throw new RangeError('Total nanoseconds overflowed');
            }

            return $totalNanoseconds;
        }
    }

    /** @var int<0,999999999> */
    public readonly int $nanosOfSecond;

    public function __construct(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
        int $nanoseconds = 0,
    ) {
        $s = $seconds
            + ($minutes * 60)
            + ($hours * 3_600)
            + \intdiv($milliseconds, 1_000)
            + \intdiv($microseconds, 1_000_000)
            + \intdiv($nanoseconds, 1_000_000_000);

        $ns = ($nanoseconds % 1_000_000_000) + (($microseconds % 1_000_000) * 1_000);
        $s += \intdiv($ns, 1_000_000_000);
        $ns %= 1_000_000_000;

        $ns += ($milliseconds % 1_000) * 1_000_000;
        $s += \intdiv($ns, 1_000_000_000);
        $ns %= 1_000_000_000;

        // nanosOfSecond must be positive
        if ($ns < 0) {
            $s -= 1;
            $ns += 1_000_000_000;
        }

        // @phpstan-ignore-next-line function.impossibleType booleanOr.alwaysFalse
        if (\is_float($s)) {
            throw new RangeError(sprintf(
                'Duration must be within %d and %d total seconds',
                PHP_INT_MIN,
                PHP_INT_MAX
            ));
        }

        $this->totalSeconds  = $s;
        $this->nanosOfSecond = $ns;
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
        $ns %= 1_000_000_000;
        /** @phpstan-var int<0,999999999> $ns */
        return [$s, $ns];
    }

    /**
     * Compares this duration to another duration.
     * 
     * @return int<-1,1>
     */
    public function compare(self $other): int
    {
        return $this->totalSeconds === $other->totalSeconds
            ? $this->nanosOfSecond <=> $other->nanosOfSecond
            : $this->totalSeconds <=> $other->totalSeconds;
    }

    /**
     * Checks if this durations equals another duration.
     */
    public function isEqual(self $other): bool
    {
        return $this->totalSeconds === $other->totalSeconds
            && $this->nanosOfSecond === $other->nanosOfSecond;
    }

    public function isLessThan(self $other): bool
    {
        return $this->compare($other) < 0;
    }

    public function isLessThanOrEqual(self $other): bool
    {
        return $this->compare($other) <= 0;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->compare($other) > 0;
    }

    public function isGreaterThanOrEqual(self $other): bool
    {
        return $this->compare($other) >= 0;
    }

    /**
     * Adds another duration to this duration.
     */
    public function addBy(self $other): self
    {
        if ($other->isZero) {
            return $this;
        } elseif ($this->isZero) {
            return $other;
        }

        $s = $this->totalSeconds + $other->totalSeconds;
        if (\is_float($s)) { // @phpstan-ignore function.impossibleType
            throw new RangeError('Total seconds overflowed during addition');
        }

        return new self(
            seconds: $s,
            nanoseconds: $this->nanosOfSecond + $other->nanosOfSecond,
        );
    }

    /**
     * Subtracts another duration from this duration.
     */
    public function subtractBy(self $other): self
    {
        if ($other->isZero) {
            return $this;
        }

        $s = $this->totalSeconds - $other->totalSeconds;
        if (\is_float($s)) { // @phpstan-ignore function.impossibleType
            throw new RangeError('Total seconds overflowed during subtraction');
        }

        return new self(
            seconds: $s,
            nanoseconds: $this->nanosOfSecond - $other->nanosOfSecond,
        );
    }

    /**
     * Multiplies this duration by a multiplier.
     * 
     * @throws \ValueError If the multiplier is NaN.
     */
    public function multiplyBy(int|float $multiplier): self
    {
        if ($multiplier == 1) {
            return $this;
        }

        if (\is_nan($multiplier)) {
            throw new \ValueError('Multiplier cannot be NaN');
        }

        $s  = $this->totalSeconds * $multiplier;
        $ns = $this->nanosOfSecond * $multiplier;

        if (\is_float($s)) {
            $f = \fmod($s, 1);
            $s -= $f;
            if ($s > PHP_INT_MAX || $s < PHP_INT_MIN) {
                throw new RangeError('Total seconds overflowed during multiplication');
            }

            $s = (int)$s;
            $ns += (int)($f * 1_000_000_000);
        }

        if ($ns >= 1_000_000_000) {
            $s += (int)($ns / 1_000_000_000);
            $ns = (int)$ns % 1_000_000_000;
        } elseif ($ns < 0) {
            $s -= (int)(\abs($ns) / 1_000_000_000) + 1;
            $ns = 1_000_000_000 - ((int)\abs($ns) % 1_000_000_000);
        } else {
            $ns = (int)$ns;
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    /**
     * Divides this duration by a divisor.
     * 
     * @throws \DivisionByZeroError If the divisor is zero.
     * @throws \ValueError If the divisor is NaN.
     */
    public function divideBy(int|float $divisor): self
    {
        if ($divisor == 1) {
            return $this;
        }

        if (\is_nan($divisor)) {
            throw new \ValueError('Divisor cannot be NaN');
        }

        if ($divisor == 0) {
            throw new \DivisionByZeroError('Division by zero');
        }

        $s  = $this->totalSeconds / $divisor;
        $ns = $this->nanosOfSecond / $divisor;

        if (\is_float($s)) {
            $ns += (int)(\fmod($s, 1) * 1_000_000_000);
            $s = (int)$s;
        }

        if ($ns >= 1_000_000_000) {
            $s += (int)($ns / 1_000_000_000);
            $ns = (int)$ns % 1_000_000_000;
        } elseif ($ns < 0) {
            $s -= (int)(\abs($ns) / 1_000_000_000) + 1;
            $ns = 1_000_000_000 - ((int)\abs($ns) % 1_000_000_000);
        } else {
            $ns = (int)$ns;
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    /**
     * Modulo of this duration by a divisor.
     * 
     * @throws \DivisionByZeroError If the divisor is zero.
     * @throws \ValueError If the divisor is NaN.
     */
    public function moduloBy(int|float $divisor): self
    {
        if ($divisor == 1) {
            return $this;
        }

        if (\is_nan($divisor)) {
            throw new \ValueError('Divisor cannot be NaN');
        }

        if ($divisor == 0) {
            throw new \DivisionByZeroError('Modulo by zero');
        }

        if (\is_float($divisor)) {
            $s  = \fmod($this->totalSeconds, $divisor);
            $ns = \fmod($this->nanosOfSecond, $divisor);

            $ns += (int)(\fmod($s, 1) * 1_000_000_000);
            $s = (int)$s;

            if ($ns >= 1_000_000_000) {
                $s += (int)($ns / 1_000_000_000);
                $ns = (int)$ns % 1_000_000_000;
            } elseif ($ns < 0) {
                $s -= (int)(\abs($ns) / 1_000_000_000) + 1;
                $ns = 1_000_000_000 - ((int)\abs($ns) % 1_000_000_000);
            } else {
                $ns = (int)$ns;
            }
        } else {
            $s  = $this->totalSeconds % $divisor;
            $ns = $this->nanosOfSecond % $divisor;
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    /**
     * Modulo of this duration by another duration.
     * 
     * @throws \DivisionByZeroError If the other duration is zero.
     */
    public function moduloOf(self $other): float
    {
        if ($other->isZero) {
            throw new \DivisionByZeroError('Modulo by zero');
        }

        $s  = $other->totalSeconds === 0 ? 0 : $this->totalSeconds % $other->totalSeconds;
        $ns = $other->nanosOfSecond === 0 ? 0 : $this->nanosOfSecond % $other->nanosOfSecond;

        return $s + ($ns / 1_000_000_000);
    }

    /**
     * Creates the difference of this duration and another duration.
     */
    public function difference(self $other): self
    {
        $s = $this->totalSeconds - $other->totalSeconds;
        if (\is_float($s)) { // @phpstan-ignore function.impossibleType
            throw new RangeError('Total seconds overflowed during subtraction');
        }

        return new self(
            seconds: \abs($s),
            nanoseconds: \abs($this->nanosOfSecond - $other->nanosOfSecond),
        );
    }

    /**
     * Inverts this duration.
     */
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

        if (\is_float($s)) { // @phpstan-ignore function.impossibleType
            throw new RangeError('Total seconds overflowed during inversion');
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    /**
     * Makes this duration absolute.
     */
    public function abs(): self
    {
        return $this->isNegative ? $this->inverted() : $this;
    }

    /**
     * Negates this duration.
     */
    public function negated(): self
    {
        return !$this->isNegative ? $this->inverted() : $this;
    }

    /**
     * Converts this duration to an ISO 8601 duration string.
     * 
     * @return non-empty-string
     */
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

        return $this->isNegative ? "-PT$timeIso" : "PT$timeIso";
    }
}
