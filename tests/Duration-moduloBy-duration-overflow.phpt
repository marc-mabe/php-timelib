--TEST--
Duration->moduloBy(Duration) large nanoseconds overflow fallback
--FILE--
<?php

include __DIR__ . '/include.php';

$divisorSeconds = intdiv(PHP_INT_MAX, 3) - 1;

$posDivisor  = new time\Duration(seconds: $divisorSeconds, nanoseconds: 1000000);
$posDividend = new time\Duration(seconds: $divisorSeconds * 3, nanoseconds: 3000001);
$negDivisor  = new time\Duration(seconds: -$divisorSeconds, nanoseconds: -1000000);
$negDividend = new time\Duration(seconds: -$divisorSeconds * 3, nanoseconds: -3000001);

// +dividend % +divisor
try {
    echo stringify($posDividend->moduloBy($posDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// -dividend % +divisor
try {
    echo stringify($negDividend->moduloBy($posDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// +dividend % -divisor
try {
    echo stringify($posDividend->moduloBy($negDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// -dividend % -divisor
try {
    echo stringify($negDividend->moduloBy($negDivisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECT--
Duration('PT0.000000001S')
Duration('-PT0.000000001S')
Duration('PT0.000000001S')
Duration('-PT0.000000001S')
