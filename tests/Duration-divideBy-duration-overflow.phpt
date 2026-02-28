--TEST--
Duration->divideBy(Duration) large numbers overflow fallback
--FILE--
<?php

include __DIR__ . '/include.php';

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

--EXPECT--
3
-3
-3
3
