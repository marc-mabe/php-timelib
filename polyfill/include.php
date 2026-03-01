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
 * Pack code for unsigned integer machine dependent size, big endian byte order.
 *
 * @internal
 */
\define('time\\_BE_UNSIGNED_PACK_CODE', match (\PHP_INT_SIZE) {
    8 => 'J',
    4 => 'N',
    default => throw new \LogicException('Unsupported PHP_INT_SIZE'),
});

/**
 * Converts an unsigned integer to a big-endian byte string.
 *
 * @internal
 */
function _toBeUnsigned(int $num): string
{
    return \pack(_BE_UNSIGNED_PACK_CODE, $num);
}

/**
 * Converts an unsigned byte string to unsigned integer.
 *
 * @internal
 */
function _fromBeUnsigned(string $bytes): int
{
    $bytes = \ltrim($bytes, "\0") ?: "\0";

    if (\strlen($bytes) > \PHP_INT_SIZE) {
        throw new RangeError('Integer overflow');
    }

    $bytes = \str_pad($bytes, \PHP_INT_SIZE, "\0", \STR_PAD_LEFT);

    /** @var array{1: int} $unpacked */
    $unpacked = \unpack(_BE_UNSIGNED_PACK_CODE, $bytes);
    return $unpacked[1];
}

/**
 * Add two unsigned big-endian byte strings.
 *
 * @return string Sum as unsigned big-endian byte string
 * @internal
 */
function _beUnsignedAdd(string $a, string $b): string
{
    $a = \ltrim($a, "\0") ?: "\0";
    $b = \ltrim($b, "\0") ?: "\0";
    $l = \max(\strlen($a), \strlen($b));
    $a = \str_pad($a, $l, "\0", \STR_PAD_LEFT);
    $b = \str_pad($b, $l, "\0", \STR_PAD_LEFT);
    $carry = 0;
    $result = \str_repeat("\0", $l);

    for ($i = $l - 1; $i >= 0; $i--) {
        $sum = \ord($a[$i]) + \ord($b[$i]) + $carry;
        $result[$i] = \chr($sum & 0xFF);
        $carry = $sum >> 8;
    }

    if ($carry) {
        $result = "\x01" . $result;
    }

    return \ltrim($result, "\0") ?: "\0";
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
    $a = \ltrim($a, "\0") ?: "\0";
    $b = \ltrim($b, "\0") ?: "\0";
    $l = \max(\strlen($a), \strlen($b));
    $a = \str_pad($a, $l, "\0", \STR_PAD_LEFT);
    $b = \str_pad($b, $l, "\0", \STR_PAD_LEFT);
    $borrow = 0;
    $result = \str_repeat("\0", $l);

    for ($i = $l - 1; $i >= 0; $i--) {
        $diff = \ord($a[$i]) - \ord($b[$i]) - $borrow;
        if ($diff < 0) {
            $diff += 256;
            $borrow = 1;
        } else {
            $borrow = 0;
        }
        $result[$i] = \chr($diff);
    }

    return \ltrim($result, "\0") ?: "\0";
}

/**
 * Multiply two unsigned big-endian byte strings.
 *
 * @return string Product as unsigned big-endian byte string
 * @internal
 */
function _beUnsignedMul(string $a, string $b): string
{
    $a = \ltrim($a, "\0") ?: "\0";
    $b = \ltrim($b, "\0") ?: "\0";
    $la = \strlen($a);
    $lb = \strlen($b);
    $result = \str_repeat("\0", $la + $lb);

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

    return \ltrim($result, "\0") ?: "\0";
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
    $dividend = \ltrim($dividend, "\0") ?: "\0";
    $divisor = \ltrim($divisor, "\0") ?: "\0";

    if ($divisor === "\0") {
        throw new \DivisionByZeroError('Division by zero');
    }

    $bitLen = \strlen($dividend) * 8;
    $quotient = \str_repeat("\0", \strlen($dividend));
    $rem = "\0";

    for ($i = 0; $i < $bitLen; $i++) {
        $rem = _beShiftLeft1($rem);
        $byteIdx = $i >> 3;
        $bitIdx  = 7 - ($i & 7);

        if (\ord($dividend[$byteIdx]) & (1 << $bitIdx)) {
            $lastIdx = \strlen($rem) - 1;
            /** @var int<1,255> $codepoint */
            $codepoint = \ord($rem[$lastIdx]) | 1;
            $rem[$lastIdx] = \chr($codepoint);
        }

        if (_beUnsignedCmp($rem, $divisor) >= 0) {
            $rem = _beUnsignedSub($rem, $divisor);
            /** @var int<0,255> $codepoint */
            $codepoint = \ord($quotient[$byteIdx]) | (1 << $bitIdx);
            $quotient[$byteIdx] = \chr($codepoint);
        }
    }

    return [
        \ltrim($quotient, "\0") ?: "\0",
        \ltrim($rem, "\0") ?: "\0",
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
    $result = \str_repeat("\0", $len);

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
    $a = \ltrim($a, "\0") ?: "\0";
    $b = \ltrim($b, "\0") ?: "\0";
    $la = \strlen($a);
    $lb = \strlen($b);

    if ($la !== $lb) {
        return $la <=> $lb;
    }

    return $a <=> $b;
}
