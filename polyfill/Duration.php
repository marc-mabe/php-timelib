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
                // Fallback for very large values where total* accessors may overflow.
                [$thisNegative, $thisAbs] = self::toAbsTotalNanoseconds($this->totalSeconds, $this->nanosOfSecond);
                [$divNegative, $divAbs] = self::toAbsTotalNanoseconds($divisor->totalSeconds, $divisor->nanosOfSecond);
                [$qAbs, $r] = self::decDivMod($thisAbs, $divAbs);
                if ($r === '0') {
                    $q = self::decToIntIfFits($qAbs, $thisNegative !== $divNegative);
                    if ($q !== null) {
                        return $q;
                    }
                }

                return ($this->totalSeconds + ($this->nanosOfSecond / self::NANOS_PER_SECOND))
                    / ($divisor->totalSeconds + ($divisor->nanosOfSecond / self::NANOS_PER_SECOND));
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
                // Fallback for very large values where total* accessors may overflow.
                [$thisNegative, $thisAbs] = self::toAbsTotalNanoseconds($this->totalSeconds, $this->nanosOfSecond);
                [, $divAbs] = self::toAbsTotalNanoseconds($divisor->totalSeconds, $divisor->nanosOfSecond);
                [, $rAbs] = self::decDivMod($thisAbs, $divAbs);

                return self::durationFromSignedNanoseconds($rAbs, $thisNegative);
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
     * @return array{bool, string}
     */
    private static function toAbsTotalNanoseconds(int $seconds, int $nanos): array
    {
        if ($seconds >= 0) {
            $abs = \ltrim((string)$seconds . \str_pad((string)$nanos, 9, '0', STR_PAD_LEFT), '0');
            return [false, $abs === '' ? '0' : $abs];
        }

        $secondsAbs = \ltrim((string)$seconds, '-');
        $base = $secondsAbs . '000000000';
        if ($nanos === 0) {
            return [true, $base];
        }

        return [true, self::decSub($base, (string)$nanos)];
    }

    private static function decCmp(string $a, string $b): int
    {
        $a = \ltrim($a, '0');
        $b = \ltrim($b, '0');
        $a = $a === '' ? '0' : $a;
        $b = $b === '' ? '0' : $b;

        return \strlen($a) === \strlen($b)
            ? ($a <=> $b)
            : (\strlen($a) <=> \strlen($b));
    }

    private static function decSub(string $a, string $b): string
    {
        $carry = 0;
        $out = '';
        $ia = \strlen($a) - 1;
        $ib = \strlen($b) - 1;
        while ($ia >= 0 || $ib >= 0) {
            $da = $ia >= 0 ? (\ord($a[$ia]) - 48) : 0;
            $db = $ib >= 0 ? (\ord($b[$ib]) - 48) : 0;
            $d = $da - $db - $carry;
            if ($d < 0) {
                $d += 10;
                $carry = 1;
            } else {
                $carry = 0;
            }
            $out = (string)$d . $out;
            $ia--;
            $ib--;
        }

        $out = \ltrim($out, '0');
        return $out === '' ? '0' : $out;
    }

    /**
     * @return array{string, string}
     */
    private static function decDivMod(string $dividend, string $divisor): array
    {
        $dividend = \ltrim($dividend, '0');
        $divisor = \ltrim($divisor, '0');
        $dividend = $dividend === '' ? '0' : $dividend;
        $divisor = $divisor === '' ? '0' : $divisor;

        if ($divisor === '0') {
            throw new \DivisionByZeroError('Division by zero');
        } elseif (self::decCmp($dividend, $divisor) < 0) {
            return ['0', $dividend];
        }

        $q = '';
        $r = '0';
        $len = \strlen($dividend);
        for ($i = 0; $i < $len; $i++) {
            $r = \ltrim(($r === '0' ? '' : $r) . $dividend[$i], '0');
            $r = $r === '' ? '0' : $r;

            $digit = 0;
            while (self::decCmp($r, $divisor) >= 0) {
                $r = self::decSub($r, $divisor);
                $digit++;
            }

            $q .= (string)$digit;
        }

        $q = \ltrim($q, '0');
        return [$q === '' ? '0' : $q, $r];
    }

    private static function decToIntIfFits(string $abs, bool $negative): ?int
    {
        $abs = \ltrim($abs, '0');
        $abs = $abs === '' ? '0' : $abs;

        if (!$negative) {
            $max = (string)PHP_INT_MAX;
            if (self::decCmp($abs, $max) > 0) {
                return null;
            }
            return (int)$abs;
        }

        $minAbs = \ltrim((string)PHP_INT_MIN, '-');
        if (self::decCmp($abs, $minAbs) > 0) {
            return null;
        } elseif ($abs === $minAbs) {
            return PHP_INT_MIN;
        }

        return -((int)$abs);
    }

    private static function durationFromSignedNanoseconds(string $absNanos, bool $negative): self
    {
        $absNanos = \ltrim($absNanos, '0');
        if ($absNanos === '') {
            return new self();
        }

        [$secondsAbs, $nanos] = self::decDivMod($absNanos, (string)self::NANOS_PER_SECOND);
        $seconds = self::decToIntIfFits($secondsAbs, false);
        if ($seconds === null) {
            throw new RangeError('Total seconds overflowed during modulo');
        }

        if (!$negative) {
            return new self(seconds: $seconds, nanoseconds: (int)$nanos);
        } elseif ($nanos === '0') {
            return new self(seconds: -$seconds);
        }

        return new self(
            seconds: -($seconds + 1),
            nanoseconds: self::NANOS_PER_SECOND - (int)$nanos
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
