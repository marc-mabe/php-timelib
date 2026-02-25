--TEST--
Duration->divideBy(Duration) large milliseconds overflow fallback
--FILE--
<?php

include __DIR__ . '/include.php';

$divisorSeconds = intdiv(PHP_INT_MAX, 3) - 1;
$divisor = new time\Duration(seconds: $divisorSeconds, nanoseconds: 1000000);
$dividend = new time\Duration(seconds: $divisorSeconds * 3, nanoseconds: 3000000);

try {
    echo stringify($dividend->divideBy($divisor)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECT--
3
