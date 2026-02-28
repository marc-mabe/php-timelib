<?php declare(strict_types=1);

namespace time;

require_once __DIR__ . '/include.php';

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
    public const int SECONDS_PER_MINUTE = 60;
    public const int SECONDS_PER_HOUR = 3600;
    public const int NANOS_PER_SECOND = 1_000_000_000;
    public const int MICROS_PER_SECOND = 1_000_000;
    public const int MILLIS_PER_SECOND = 1_000;

    public bool $isZero {
        get => !$this->totalSeconds && !$this->nanosOfSecond;
    }

    public bool $isNegative {
        get => $this->totalSeconds < 0;
    }

    public int $totalHours {
        get => \intdiv($this->totalSeconds, self::SECONDS_PER_HOUR);
    }

    public int $totalMinutes {
        get => \intdiv($this->totalSeconds, self::SECONDS_PER_MINUTE);
    }

    /** @var int<0,59> */
    public int $minutesOfHour {
        get => \abs($this->totalMinutes % self::SECONDS_PER_MINUTE);
    }

    public readonly int $totalSeconds;

    /** @var int<0,59> */
    public int $secondsOfMinute {
        get => \abs($this->totalSeconds % self::SECONDS_PER_MINUTE);
    }

    public int $totalMilliseconds {
        get =>_intAdd($this->millisOfSecond, $this->totalSeconds * 1_000, 'Total milliseconds overflowed');
    }

    /** @var int<0,999> */
    public int $millisOfSecond {
        /** @phpstan-ignore return.type */
        get => \intdiv($this->nanosOfSecond, 1_000_000);
    }

    public int $totalMicroseconds {
        get =>_intAdd($this->microsOfSecond, $this->totalSeconds * 1_000_000, 'Total microseconds overflowed');
    }

    /** @var int<0,999999> */
    public int $microsOfSecond {
        /** @phpstan-ignore return.type */
        get => \intdiv($this->nanosOfSecond, 1_000);
    }

    public int $totalNanoseconds {
        get =>_intAdd($this->nanosOfSecond, $this->totalSeconds * 1_000_000_000, 'Total nanoseconds overflowed');
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
            + ($minutes * self::SECONDS_PER_MINUTE)
            + ($hours * self::SECONDS_PER_HOUR)
            + \intdiv($milliseconds, self::MILLIS_PER_SECOND)
            + \intdiv($microseconds, self::MICROS_PER_SECOND)
            + \intdiv($nanoseconds, self::NANOS_PER_SECOND);
        $ns = ($nanoseconds % self::NANOS_PER_SECOND) + (($microseconds % self::MICROS_PER_SECOND) * 1_000);
        $s += \intdiv($ns, self::NANOS_PER_SECOND);
        $ns %= self::NANOS_PER_SECOND;

        $ns += ($milliseconds % self::MILLIS_PER_SECOND) * 1_000_000;
        $s += \intdiv($ns, self::NANOS_PER_SECOND);
        $ns %= self::NANOS_PER_SECOND;

        // nanosOfSecond must be positive
        if ($ns < 0) {
            $s -= 1;
            $ns += self::NANOS_PER_SECOND;
        }

        // @phpstan-ignore-next-line function.impossibleType booleanOr.alwaysFalse
        if (\is_float($s)) {
            throw new RangeError(\sprintf(
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
        $s  = $tuple[0] + $this->totalSeconds + \intdiv($ns, self::NANOS_PER_SECOND);
        $ns %= self::NANOS_PER_SECOND;
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

        return new self(
            seconds: _intAdd($this->totalSeconds, $other->totalSeconds, 'Total seconds overflowed during addition'),
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

        return new self(
            seconds: _intSub($this->totalSeconds, $other->totalSeconds, 'Total seconds overflowed during subtraction'),
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

        if (!\is_finite($multiplier)) {
            throw new \ValueError('Multiplier must be a finite number');
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
            $ns += (int)($f * self::NANOS_PER_SECOND);
        }

        if ($ns >= self::NANOS_PER_SECOND) {
            $s += (int)($ns / self::NANOS_PER_SECOND);
            $ns = (int)$ns % self::NANOS_PER_SECOND;
        } elseif ($ns < 0) {
            $s -= (int)(\abs($ns) / self::NANOS_PER_SECOND) + 1;
            $ns = self::NANOS_PER_SECOND - ((int)\abs($ns) % self::NANOS_PER_SECOND);
        } else {
            $ns = (int)$ns;
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    /**
     * Divides this duration by a divisor.
     *
     * Returns a Duration if the divisor is a number.
     * Returns a number if the divisor is a Duration.
     *
     * @return ($divisor is self ? int|float : self)
     * @throws RangeError On overflow.
     * @throws \DivisionByZeroError If the divisor is zero.
     * @throws \ValueError If the divisor is NaN.
     */
    public function divideBy(int|float|self $divisor): self|int|float
    {
        if ($divisor instanceof self) {
            if ($divisor->isZero) {
                throw new \DivisionByZeroError('Division by zero');
            }

            try {
                if ($this->nanosOfSecond || $divisor->nanosOfSecond) {
                    if ($this->nanosOfSecond % 1_000_000 || $divisor->nanosOfSecond % 1_000_000) {
                        return $this->totalNanoseconds / $divisor->totalNanoseconds;
                    } elseif ($this->nanosOfSecond % 1_000 || $divisor->nanosOfSecond % 1_000) {
                        return $this->totalMicroseconds / $divisor->totalMicroseconds;
                    }

                    return $this->totalMilliseconds / $divisor->totalMilliseconds;
                }

                return $this->totalSeconds / $divisor->totalSeconds;
            } catch (RangeError) {
                $thisTotalNsBe = self::toAbsTotalNanosecondsBe($this->totalSeconds, $this->nanosOfSecond);
                $divisorTotalNsBe = self::toAbsTotalNanosecondsBe($divisor->totalSeconds, $divisor->nanosOfSecond);

                [$quotient, ] = _beUnsignedDiv($thisTotalNsBe, $divisorTotalNsBe);
                $quotient = \str_pad($quotient, 8, "\x0", \STR_PAD_LEFT);
                $result = \unpack('J', $quotient)[1];

                return $this->isNegative !== $divisor->isNegative ? -$result : $result;
            }
        }

        if (\is_int($divisor)) {
            if ($divisor === 1) {
                return $this;
            } elseif ($divisor === 0) {
                throw new \DivisionByZeroError('Division by zero');
            } elseif ($this->isZero) {
                return $this;
            }

            $s = \intdiv($this->totalSeconds, $divisor);
            $sr = $this->totalSeconds % $divisor;
            $ns = \intdiv($this->nanosOfSecond, $divisor)
                + (int)(($sr / $divisor) * self::NANOS_PER_SECOND);

            return new self(seconds: $s, nanoseconds: $ns);
        }

        // $divisor is a float
        if ($divisor === 1.0) {
            return $this;
        } elseif ($divisor === 0.0) {
            throw new \DivisionByZeroError('Division by zero');
        } elseif (\is_nan($divisor)) {
            throw new \ValueError('Divisor can not be NaN');
        } elseif ($this->isZero) {
            return $this;
        }

        $s  = $this->totalSeconds / $divisor;
        $ns = $this->nanosOfSecond / $divisor;

        $f = \fmod($s, 1);
        $s -= $f;
        if ($s > PHP_INT_MAX || $s < PHP_INT_MIN) {
            throw new RangeError('Total seconds overflowed during division');
        }

        $s = (int)$s;
        $ns += (int)($f * self::NANOS_PER_SECOND);

        if ($ns >= self::NANOS_PER_SECOND) {
            $s += (int)($ns / self::NANOS_PER_SECOND);
            $ns = (int)$ns % self::NANOS_PER_SECOND;
        } elseif ($ns < 0) {
            $s -= (int)(-$ns / self::NANOS_PER_SECOND) + 1;
            $ns = self::NANOS_PER_SECOND - ((int)-$ns % self::NANOS_PER_SECOND);
        } else {
            $ns = (int)$ns;
        }

        return new self(seconds: $s, nanoseconds: $ns);
    }

    /**
     * Modulo of this duration by a divisor.
     *
     * This will answer the following:
     * - `THIS(duration) modulo OTHER(duration)`: What is the rest of the integer division of this duration by another duration?
     *   - e.g. `5.5s mod 1.2s = 0.7s` as 1.2s fits 4 times into 5.5s and 0.7s is the remainder.
     * - `THIS(duration) modulo NUMBER`: What is the remainder after dividing this duration into pieces?
     *   - e.g. `5.5s mod 750 = 250ns` as 5.5s divided into 750 pieces gives you 7,333,333ns for each piece
     *     with a reminder of 250ns. Means `5.5s = 750 * 7333333ns + 250ns`.
     *
     * @throws \DivisionByZeroError If the divisor is zero.
     * @throws \ValueError If the divisor is NaN.
     */
    public function moduloBy(int|float|self $divisor): self
    {
        if ($divisor instanceof self) {
            if ($divisor->isZero) {
                throw new \DivisionByZeroError('Modulo by zero');
            }

            try {
                if ($this->nanosOfSecond || $divisor->nanosOfSecond) {
                    if ($this->nanosOfSecond % 1_000_000 || $divisor->nanosOfSecond % 1_000_000) {
                        return new Duration(nanoseconds: $this->totalNanoseconds % $divisor->totalNanoseconds);
                    } elseif ($this->nanosOfSecond % 1_000 || $divisor->nanosOfSecond % 1_000) {
                        return new Duration(microseconds: $this->totalMicroseconds % $divisor->totalMicroseconds);
                    }

                    return new Duration(milliseconds: $this->totalMilliseconds % $divisor->totalMilliseconds);
                }

                return new Duration(seconds: $this->totalSeconds % $divisor->totalSeconds);
            } catch (RangeError) {
                $thisTotalNsBe = self::toAbsTotalNanosecondsBe($this->totalSeconds, $this->nanosOfSecond);
                $divisorTotalNsBe = self::toAbsTotalNanosecondsBe($divisor->totalSeconds, $divisor->nanosOfSecond);

                [, $remainderTotalNsBe] = _beUnsignedDiv($thisTotalNsBe, $divisorTotalNsBe);

                return self::fromNanosecondsBe($remainderTotalNsBe, $this->isNegative);
            }
        }

        if ($divisor == 0) {
            throw new \DivisionByZeroError('Modulo by zero');
        }

        if (!\is_finite($divisor)) {
            throw new \ValueError('Divisor must be a Duration or a finite number');
        }

        return $this->subtractBy($this->divideBy($divisor)->multiplyBy($divisor));
    }

    /**
     * Computes the absolute distance between this and the other duration.
     */
    public function difference(self $other): self
    {
        return $this->compare($other) >= 0
            ? $this->subtractBy($other)
            : $other->subtractBy($this);
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
            $ns = self::NANOS_PER_SECOND - $ns;
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
     * @return string Convert to absolute total nanoseconds as unsigned big-endian byte string.
     */
    private static function toAbsTotalNanosecondsBe(int $seconds, int $nanos): string
    {
        $binNanosPerSec = \pack('J', self::NANOS_PER_SECOND);

        if ($seconds >= 0) {
            return _beUnsignedAdd(
                _beUnsignedMul(\pack('J', $seconds), $binNanosPerSec),
                \pack('J', $nanos)
            );
        }

        $absSec = $seconds === \PHP_INT_MIN ? \PHP_INT_MIN : -$seconds;
        $secNs = _beUnsignedMul(\pack('J', $absSec), $binNanosPerSec);

        if ($nanos === 0) {
            return $secNs;
        }

        return _beUnsignedSub($secNs, \pack('J', $nanos));
    }

    /**
     * Construct a Duration from absolute nanoseconds as unsigned big-endian byte string.
     */
    private static function fromNanosecondsBe(string $absNanosBin, bool $negative): self
    {
        if (\ltrim($absNanosBin, "\x0") === '') {
            return new self();
        }

        [$secondsBin, $nanosBin] = _beUnsignedDiv($absNanosBin, \pack('J', self::NANOS_PER_SECOND));

        $seconds = \unpack('J', \str_pad($secondsBin, 8, "\x0", \STR_PAD_LEFT))[1];
        $nanos = \unpack('J', \str_pad($nanosBin, 8, "\x0", \STR_PAD_LEFT))[1];

        if (!$negative) {
            return new self(seconds: $seconds, nanoseconds: $nanos);
        }

        if ($nanos === 0) {
            return new self(seconds: -$seconds);
        }

        return new self(
            seconds: -($seconds + 1),
            nanoseconds: self::NANOS_PER_SECOND - $nanos
        );
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
