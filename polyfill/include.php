<?php declare(strict_types=1);

namespace time;

/**
 * Safe integer addition that throws a RangeError on overflow or a provided fallback value.
 *
 * @param int $a
 * @param int $b
 * @return int
 * @throws RangeError
 * @internal
 */
function _intAdd(int|float $a, int|float $b, int|string $onOverflow): int
{
    $s = $a + $b;

    if (\is_float($s)) {
        if (\is_string($onOverflow)) {
            throw new RangeError($onOverflow);
        } else {
            return $onOverflow;
        }
    }

    return $s;
}

/**
 * Safe integer subtraction that throws a RangeError on overflow or a provided fallback value.
 *
 * @param int $a
 * @param int $b
 * @return int
 * @throws RangeError
 * @internal
 */
function _intSub(int|float $a, int|float $b, int|string $onOverflow): int
{
    $s = $a - $b;

    if (\is_float($s)) {
        if (\is_string($onOverflow)) {
            throw new RangeError($onOverflow);
        } else {
            return $onOverflow;
        }
    }

    return $s;
}

/**
 * Add two unsigned big-endian byte strings.
 *
 * @return string Sum as unsigned big-endian byte string
 * @internal
 */
function _beUnsignedAdd(string $a, string $b): string
{
    $len = \max(\strlen($a), \strlen($b));
    $a = \str_pad($a, $len, "\x0", \STR_PAD_LEFT);
    $b = \str_pad($b, $len, "\x0", \STR_PAD_LEFT);
    $carry = 0;
    $result = \str_repeat("\x0", $len);

    for ($i = $len - 1; $i >= 0; $i--) {
        $sum = \ord($a[$i]) + \ord($b[$i]) + $carry;
        $result[$i] = \chr($sum & 0xFF);
        $carry = $sum >> 8;
    }

    if ($carry) {
        $result = "\x01" . $result;
    }

    return \ltrim($result, "\x0") ?: "\x0";
}

/**
 * Subtract two unsigned big-endian byte strings.
 *
 * @param string $a Minuend ($a >= $b)
 * @param string $b Subtrahend ($a >= $b)
 * @return string Difference as unsigned big-endian byte string
 * @internal
 */
function _beUnsignedSub(string $a, string $b): string
{
    $len = \max(\strlen($a), \strlen($b));
    $a = \str_pad($a, $len, "\x0", \STR_PAD_LEFT);
    $b = \str_pad($b, $len, "\x0", \STR_PAD_LEFT);
    $borrow = 0;
    $result = \str_repeat("\x0", $len);

    for ($i = $len - 1; $i >= 0; $i--) {
        $diff = \ord($a[$i]) - \ord($b[$i]) - $borrow;
        if ($diff < 0) {
            $diff += 256;
            $borrow = 1;
        } else {
            $borrow = 0;
        }
        $result[$i] = \chr($diff);
    }

    return \ltrim($result, "\x0") ?: "\x0";
}

/**
 * Multiply two unsigned big-endian byte strings.
 *
 * @return string Product as unsigned big-endian byte string
 * @internal
 */
function _beUnsignedMul(string $a, string $b): string
{
    $la = \strlen($a);
    $lb = \strlen($b);
    $result = \str_repeat("\x0", $la + $lb);

    for ($i = $la - 1; $i >= 0; $i--) {
        $carry = 0;
        $ai = \ord($a[$i]);

        for ($j = $lb - 1; $j >= 0; $j--) {
            $pos = $i + $j + 1;
            $sum = \ord($result[$pos]) + $ai * \ord($b[$j]) + $carry;
            $result[$pos] = \chr($sum & 0xFF);
            $carry = $sum >> 8;
        }

        for ($k = $i; $carry > 0; $k--) {
            $sum = \ord($result[$k]) + $carry;
            $result[$k] = \chr($sum & 0xFF);
            $carry = $sum >> 8;
        }
    }

    return \ltrim($result, "\x0") ?: "\x0";
}

/**
 * Divide two unsigned big-endian byte strings.
 *
 * @param string $dividend Dividend
 * @param string $divisor Divisor
 * @return array{string, string} Tuple of quotient and remainder as unsigned big-endian byte strings
 * @internal
 */
function _beUnsignedDiv(string $dividend, string $divisor): array
{
    if (\ltrim($divisor, "\x0") === '') {
        throw new \DivisionByZeroError('Division by zero');
    }

    $bitLen = \strlen($dividend) * 8;
    $quotient = \str_repeat("\x0", \strlen($dividend));
    $rem = "\x0";

    for ($i = 0; $i < $bitLen; $i++) {
        $rem = _beShiftLeft1($rem);
        $byteIdx = $i >> 3;
        $bitIdx  = 7 - ($i & 7);

        if (\ord($dividend[$byteIdx]) & (1 << $bitIdx)) {
            $lastIdx = \strlen($rem) - 1;
            $rem[$lastIdx] = \chr(\ord($rem[$lastIdx]) | 1);
        }

        if (_beUnsignedCmp($rem, $divisor) >= 0) {
            $rem = _beUnsignedSub($rem, $divisor);
            $quotient[$byteIdx] = \chr(\ord($quotient[$byteIdx]) | (1 << $bitIdx));
        }
    }

    return [
        \ltrim($quotient, "\x0") ?: "\x0",
        \ltrim($rem, "\x0") ?: "\x0",
    ];
}

/**
 * Shift an unsigned big-endian byte string left by 1 bit.
 *
 * @internal
 */
function _beShiftLeft1(string $be): string
{
    $len = \strlen($be);
    $carry = 0;
    $result = \str_repeat("\x0", $len);

    for ($i = $len - 1; $i >= 0; $i--) {
        $byte = \ord($be[$i]);
        $result[$i] = \chr((($byte << 1) | $carry) & 0xFF);
        $carry = ($byte >> 7) & 1;
    }

    if ($carry) {
        $result = "\x01" . $result;
    }

    return $result;
}

/**
 * Compare two unsigned big-endian unsigned byte strings.
 *
 * @return int -1, 0, or 1
 * @internal
 */
function _beUnsignedCmp(string $a, string $b): int
{
    $a = \ltrim($a, "\x0") ?: "\x0";
    $b = \ltrim($b, "\x0") ?: "\x0";
    $la = \strlen($a);
    $lb = \strlen($b);

    if ($la !== $lb) {
        return $la <=> $lb;
    }

    return $a <=> $b;
}
