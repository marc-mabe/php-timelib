--TEST--
Duration->divideBy(Duration) large numbers overflow fallback
--FILE--
<?php

include __DIR__ . '/include.inc';

$divisorSeconds = intdiv(PHP_INT_MAX, 3) - 1;

$posDivisor  = new time\Duration(seconds: $divisorSeconds, nanoseconds: 1000000);
$posDividend = new time\Duration(seconds: $divisorSeconds * 3, nanoseconds: 3000000);
$negDivisor  = new time\Duration(seconds: -$divisorSeconds, nanoseconds: -1000000);
$negDividend = new time\Duration(seconds: -$divisorSeconds * 3, nanoseconds: -3000000);

// +dividend / +divisor = 3
try {
    echo stringify($posDividend->divideBy($posDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// -dividend / +divisor = -3
try {
    echo stringify($negDividend->divideBy($posDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// +dividend / -divisor = -3
try {
    echo stringify($posDividend->divideBy($negDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// -dividend / -divisor = 3
try {
    echo stringify($negDividend->divideBy($negDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

$intMax = new time\Duration(seconds: PHP_INT_MAX, nanoseconds: 1000000);
$intMin = new time\Duration(seconds: PHP_INT_MIN, nanoseconds: 1000000);
$intMinP1 = new time\Duration(seconds: PHP_INT_MIN + 1, nanoseconds: -1000000);

// PHP_INT_MAX / PHP_INT_MAX = 1
try {
    echo stringify($intMax->divideBy($intMax)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// PHP_INT_MIN / PHP_INT_MIN = 1
try {
    echo stringify($intMin->divideBy($intMin)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// PHP_INT_MIN+1 / PHP_INT_MAX = -1
try {
    echo stringify($intMinP1->divideBy($intMax)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// PHP_INT_MAX / PHP_INT_MIN+1 = -1
try {
    echo stringify($intMax->divideBy($intMinP1)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// 1 / 3 = 0.333333333...
$multiplier = PHP_INT_SIZE === 4 ? 1 : 1_000_000;
$d1 = new time\Duration(seconds: 1_000_000 * $multiplier, nanoseconds: 1);
$d3 = new time\Duration(seconds: 3_000_000 * $multiplier, nanoseconds: 3);
try {
    echo stringify($d1->divideBy($d3)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// -1 / -3 = 0.333333333...
$multiplier = PHP_INT_SIZE === 4 ? 1 : 1_000_000;
$d1 = new time\Duration(seconds: -1_000_000 * $multiplier, nanoseconds: -1);
$d3 = new time\Duration(seconds: -3_000_000 * $multiplier, nanoseconds: -3);
try {
    echo stringify($d1->divideBy($d3)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// -1 / 3 = -0.333333333...
$multiplier = PHP_INT_SIZE === 4 ? 1 : 1_000_000;
$d1 = new time\Duration(seconds: -1_000_000 * $multiplier, nanoseconds: -1);
$d3 = new time\Duration(seconds: 3_000_000 * $multiplier, nanoseconds: 3);
try {
    echo stringify($d1->divideBy($d3)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// 1 / -3 = -0.333333333...
$multiplier = PHP_INT_SIZE === 4 ? 1 : 1_000_000;
$d1 = new time\Duration(seconds: 1_000_000 * $multiplier, nanoseconds: 1);
$d3 = new time\Duration(seconds: -3_000_000 * $multiplier, nanoseconds: -3);
try {
    echo stringify($d1->divideBy($d3)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECTF--
3
-3
-3
3
1
1
-1
-1
0.3333333333333333
0.3333333333333333
-0.3333333333333333
-0.3333333333333333
