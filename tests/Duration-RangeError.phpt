--TEST--
Duration - RangeError
--FILE--
<?php

include __DIR__ . '/include.php';

try {
    $duration1 = new time\Duration(seconds: PHP_INT_MAX);
    $duration2 = new time\Duration(seconds: 1);
    echo stringify($duration1) . ' + ' . stringify($duration2) . ' = ';
    echo stringify($duration1->addBy($duration2)) . "\n";
} catch (time\RangeError $e) {
    echo $e::class . ": {$e->getMessage()}\n";
}

try {
    $duration1 = new time\Duration(seconds: PHP_INT_MAX);
    $duration2 = new time\Duration(nanoseconds: 999_999_999);
    echo stringify($duration1) . ' + ' . stringify($duration2) . ' = ';
    echo stringify($duration1->addBy($duration2)) . "\n";
} catch (time\RangeError $e) {
    echo $e::class . ": {$e->getMessage()}\n";
}

try {
    $duration1 = new time\Duration(seconds: PHP_INT_MIN + 1);
    $duration2 = new time\Duration(seconds: 2);
    echo stringify($duration1) . ' - ' . stringify($duration2) . ' = ';
    echo stringify($duration1->subtractBy($duration2)) . "\n";
} catch (time\RangeError $e) {
    echo $e::class . ": {$e->getMessage()}\n";
}

try {
    $intMaxDiv2 = intdiv(PHP_INT_MAX, 2);
    $duration1 = new time\Duration(seconds: $intMaxDiv2);
    echo stringify($duration1) . ' * 2 = ' . stringify($duration1->multiplyBy(2)) . "\n";
    echo stringify($duration1) . ' * 3 = ';
    echo stringify($duration1->multiplyBy(3)) . "\n";
} catch (time\RangeError $e) {
    echo $e::class . ": {$e->getMessage()}\n";
}

--EXPECTF--
Duration('PT%sS') + Duration('PT1S') = time\RangeError: Total seconds overflowed during addition
Duration('PT%sS') + Duration('PT0.999999999S') = Duration('PT%s.999999999S')
Duration('-PT%sS') - Duration('PT2S') = time\RangeError: Total seconds overflowed during subtraction
Duration('PT%sS') * 2 = Duration('PT%sS')
Duration('PT%sS') * 3 = time\RangeError: Total seconds overflowed during multiplication
