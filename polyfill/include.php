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
