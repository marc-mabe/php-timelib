--TEST--
Duration->moduloBy(Duration) large nanoseconds overflow fallback
--FILE--
<?php

include __DIR__ . '/include.php';

$divisorSeconds = intdiv(PHP_INT_MAX, 3) - 1;
$divisor = new time\Duration(seconds: $divisorSeconds, nanoseconds: 1000000);
$dividend = new time\Duration(seconds: $divisorSeconds * 3, nanoseconds: 3000001);

try {
    echo stringify($dividend->moduloBy($divisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECT--
Duration('PT0.000000001S')
