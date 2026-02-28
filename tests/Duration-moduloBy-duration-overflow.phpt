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

$intMax = new time\Duration(seconds: PHP_INT_MAX, nanoseconds: 1000000);
$intMin = new time\Duration(seconds: PHP_INT_MIN, nanoseconds: 1000000);

// PHP_INT_MAX+1ns % PHP_INT_MAX = 1ns remainder
$intMaxPlus1ns = new time\Duration(seconds: PHP_INT_MAX, nanoseconds: 1000001);
try {
    echo stringify($intMaxPlus1ns->moduloBy($intMax)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// PHP_INT_MIN % PHP_INT_MIN = zero
try {
    echo stringify($intMin->moduloBy($intMin)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

// PHP_INT_MIN % PHP_INT_MAX = negative remainder
try {
    echo stringify($intMin->moduloBy($intMax)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECT--
Duration('PT0.000000001S')
Duration('-PT0.000000001S')
Duration('PT0.000000001S')
Duration('-PT0.000000001S')
Duration('PT0.000000001S')
Duration('PT0S')
Duration('-PT0.998S')
