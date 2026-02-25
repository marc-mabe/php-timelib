--TEST--
Duration->divideBy()
--FILE--
<?php

include __DIR__ . '/include.php';

$durations = [
    new time\Duration(),
    new time\Duration(nanoseconds: 123),
    new time\Duration(microseconds: 123),
    new time\Duration(milliseconds: 123),
    new time\Duration(seconds: 123),
    new time\Duration(seconds: 1, nanoseconds: 987654321),
    new time\Duration(seconds: -1, nanoseconds: -987654321),
    new time\Duration(seconds: 123, nanoseconds: 987654321),
    new time\Duration(seconds: -123, nanoseconds: -987654321),
];

$divisors = [0, 0.0, 1, 1.0, -1, -1.0, 1.5, -1.5, 2, 2.0, NAN, INF, -INF, ...$durations];

foreach ($durations as $baseDuration) {
    foreach ($divisors as $divisor) {
        echo stringify($baseDuration) . ' / ' . stringify($divisor) . ' = ';

        try {
            echo stringify($baseDuration->divideBy($divisor)) . "\n";
        } catch (Throwable $e) {
            echo $e::class . ': ' . $e->getMessage() . "\n";
        }
    }
}

--EXPECT--
Duration('PT0S') / 0 = DivisionByZeroError: Division by zero
Duration('PT0S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT0S') / 1 = Duration('PT0S')
Duration('PT0S') / 1.0 = Duration('PT0S')
Duration('PT0S') / -1 = Duration('PT0S')
Duration('PT0S') / -1.0 = Duration('PT0S')
Duration('PT0S') / 1.5 = Duration('PT0S')
Duration('PT0S') / -1.5 = Duration('PT0S')
Duration('PT0S') / 2 = Duration('PT0S')
Duration('PT0S') / 2.0 = Duration('PT0S')
Duration('PT0S') / NAN = ValueError: Divisor can not be NaN
Duration('PT0S') / INF = Duration('PT0S')
Duration('PT0S') / -INF = Duration('PT0S')
Duration('PT0S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT0S') / Duration('PT0.000000123S') = 0
Duration('PT0S') / Duration('PT0.000123S') = 0
Duration('PT0S') / Duration('PT0.123S') = 0
Duration('PT0S') / Duration('PT2M3S') = 0
Duration('PT0S') / Duration('PT1.987654321S') = 0
Duration('PT0S') / Duration('-PT1.987654321S') = 0
Duration('PT0S') / Duration('PT2M3.987654321S') = 0
Duration('PT0S') / Duration('-PT2M3.987654321S') = 0
Duration('PT0.000000123S') / 0 = DivisionByZeroError: Division by zero
Duration('PT0.000000123S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT0.000000123S') / 1 = Duration('PT0.000000123S')
Duration('PT0.000000123S') / 1.0 = Duration('PT0.000000123S')
Duration('PT0.000000123S') / -1 = Duration('-PT0.000000123S')
Duration('PT0.000000123S') / -1.0 = Duration('-PT0.000000123S')
Duration('PT0.000000123S') / 1.5 = Duration('PT0.000000082S')
Duration('PT0.000000123S') / -1.5 = Duration('-PT0.000000082S')
Duration('PT0.000000123S') / 2 = Duration('PT0.000000061S')
Duration('PT0.000000123S') / 2.0 = Duration('PT0.000000061S')
Duration('PT0.000000123S') / NAN = ValueError: Divisor can not be NaN
Duration('PT0.000000123S') / INF = Duration('PT0S')
Duration('PT0.000000123S') / -INF = Duration('PT0S')
Duration('PT0.000000123S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT0.000000123S') / Duration('PT0.000000123S') = 1
Duration('PT0.000000123S') / Duration('PT0.000123S') = 0.001
Duration('PT0.000000123S') / Duration('PT0.123S') = 1.0E-6
Duration('PT0.000000123S') / Duration('PT2M3S') = 1.0E-9
Duration('PT0.000000123S') / Duration('PT1.987654321S') = 6.188198757725539E-8
Duration('PT0.000000123S') / Duration('-PT1.987654321S') = -6.188198757725539E-8
Duration('PT0.000000123S') / Duration('PT2M3.987654321S') = 9.92034252713234E-10
Duration('PT0.000000123S') / Duration('-PT2M3.987654321S') = -9.92034252713234E-10
Duration('PT0.000123S') / 0 = DivisionByZeroError: Division by zero
Duration('PT0.000123S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT0.000123S') / 1 = Duration('PT0.000123S')
Duration('PT0.000123S') / 1.0 = Duration('PT0.000123S')
Duration('PT0.000123S') / -1 = Duration('-PT0.000123S')
Duration('PT0.000123S') / -1.0 = Duration('-PT0.000123S')
Duration('PT0.000123S') / 1.5 = Duration('PT0.000082S')
Duration('PT0.000123S') / -1.5 = Duration('-PT0.000082S')
Duration('PT0.000123S') / 2 = Duration('PT0.0000615S')
Duration('PT0.000123S') / 2.0 = Duration('PT0.0000615S')
Duration('PT0.000123S') / NAN = ValueError: Divisor can not be NaN
Duration('PT0.000123S') / INF = Duration('PT0S')
Duration('PT0.000123S') / -INF = Duration('PT0S')
Duration('PT0.000123S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT0.000123S') / Duration('PT0.000000123S') = 1000
Duration('PT0.000123S') / Duration('PT0.000123S') = 1
Duration('PT0.000123S') / Duration('PT0.123S') = 0.001
Duration('PT0.000123S') / Duration('PT2M3S') = 1.0E-6
Duration('PT0.000123S') / Duration('PT1.987654321S') = 6.188198757725539E-5
Duration('PT0.000123S') / Duration('-PT1.987654321S') = -6.188198757725539E-5
Duration('PT0.000123S') / Duration('PT2M3.987654321S') = 9.920342527132339E-7
Duration('PT0.000123S') / Duration('-PT2M3.987654321S') = -9.920342527132339E-7
Duration('PT0.123S') / 0 = DivisionByZeroError: Division by zero
Duration('PT0.123S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT0.123S') / 1 = Duration('PT0.123S')
Duration('PT0.123S') / 1.0 = Duration('PT0.123S')
Duration('PT0.123S') / -1 = Duration('-PT0.123S')
Duration('PT0.123S') / -1.0 = Duration('-PT0.123S')
Duration('PT0.123S') / 1.5 = Duration('PT0.082S')
Duration('PT0.123S') / -1.5 = Duration('-PT0.082S')
Duration('PT0.123S') / 2 = Duration('PT0.0615S')
Duration('PT0.123S') / 2.0 = Duration('PT0.0615S')
Duration('PT0.123S') / NAN = ValueError: Divisor can not be NaN
Duration('PT0.123S') / INF = Duration('PT0S')
Duration('PT0.123S') / -INF = Duration('PT0S')
Duration('PT0.123S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT0.123S') / Duration('PT0.000000123S') = 1000000
Duration('PT0.123S') / Duration('PT0.000123S') = 1000
Duration('PT0.123S') / Duration('PT0.123S') = 1
Duration('PT0.123S') / Duration('PT2M3S') = 0.001
Duration('PT0.123S') / Duration('PT1.987654321S') = 0.06188198757725539
Duration('PT0.123S') / Duration('-PT1.987654321S') = -0.06188198757725539
Duration('PT0.123S') / Duration('PT2M3.987654321S') = 0.000992034252713234
Duration('PT0.123S') / Duration('-PT2M3.987654321S') = -0.000992034252713234
Duration('PT2M3S') / 0 = DivisionByZeroError: Division by zero
Duration('PT2M3S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT2M3S') / 1 = Duration('PT2M3S')
Duration('PT2M3S') / 1.0 = Duration('PT2M3S')
Duration('PT2M3S') / -1 = Duration('-PT2M3S')
Duration('PT2M3S') / -1.0 = Duration('-PT2M3S')
Duration('PT2M3S') / 1.5 = Duration('PT1M22S')
Duration('PT2M3S') / -1.5 = Duration('-PT1M22S')
Duration('PT2M3S') / 2 = Duration('PT1M1.5S')
Duration('PT2M3S') / 2.0 = Duration('PT1M1.5S')
Duration('PT2M3S') / NAN = ValueError: Divisor can not be NaN
Duration('PT2M3S') / INF = Duration('PT0S')
Duration('PT2M3S') / -INF = Duration('PT0S')
Duration('PT2M3S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT2M3S') / Duration('PT0.000000123S') = 1000000000
Duration('PT2M3S') / Duration('PT0.000123S') = 1000000
Duration('PT2M3S') / Duration('PT0.123S') = 1000
Duration('PT2M3S') / Duration('PT2M3S') = 1
Duration('PT2M3S') / Duration('PT1.987654321S') = 61.881987577255394
Duration('PT2M3S') / Duration('-PT1.987654321S') = -61.881987577255394
Duration('PT2M3S') / Duration('PT2M3.987654321S') = 0.9920342527132339
Duration('PT2M3S') / Duration('-PT2M3.987654321S') = -0.9920342527132339
Duration('PT1.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('PT1.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT1.987654321S') / 1 = Duration('PT1.987654321S')
Duration('PT1.987654321S') / 1.0 = Duration('PT1.987654321S')
Duration('PT1.987654321S') / -1 = Duration('-PT1.987654321S')
Duration('PT1.987654321S') / -1.0 = Duration('-PT1.987654321S')
Duration('PT1.987654321S') / 1.5 = Duration('PT1.32510288S')
Duration('PT1.987654321S') / -1.5 = Duration('-PT1.32510288S')
Duration('PT1.987654321S') / 2 = Duration('PT0.99382716S')
Duration('PT1.987654321S') / 2.0 = Duration('PT0.99382716S')
Duration('PT1.987654321S') / NAN = ValueError: Divisor can not be NaN
Duration('PT1.987654321S') / INF = Duration('PT0S')
Duration('PT1.987654321S') / -INF = Duration('PT0S')
Duration('PT1.987654321S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT1.987654321S') / Duration('PT0.000000123S') = 16159791.227642277
Duration('PT1.987654321S') / Duration('PT0.000123S') = 16159.791227642276
Duration('PT1.987654321S') / Duration('PT0.123S') = 16.159791227642277
Duration('PT1.987654321S') / Duration('PT2M3S') = 0.016159791227642276
Duration('PT1.987654321S') / Duration('PT1.987654321S') = 1
Duration('PT1.987654321S') / Duration('-PT1.987654321S') = -1
Duration('PT1.987654321S') / Duration('PT2M3.987654321S') = 0.016031066414515977
Duration('PT1.987654321S') / Duration('-PT2M3.987654321S') = -0.016031066414515977
Duration('-PT1.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('-PT1.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('-PT1.987654321S') / 1 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') / 1.0 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') / -1 = Duration('PT1.987654321S')
Duration('-PT1.987654321S') / -1.0 = Duration('PT1.987654321S')
Duration('-PT1.987654321S') / 1.5 = Duration('-PT1.32510288S')
Duration('-PT1.987654321S') / -1.5 = Duration('PT1.32510288S')
Duration('-PT1.987654321S') / 2 = Duration('-PT0.993827161S')
Duration('-PT1.987654321S') / 2.0 = Duration('-PT0.993827161S')
Duration('-PT1.987654321S') / NAN = ValueError: Divisor can not be NaN
Duration('-PT1.987654321S') / INF = Duration('PT0S')
Duration('-PT1.987654321S') / -INF = Duration('PT0S')
Duration('-PT1.987654321S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('-PT1.987654321S') / Duration('PT0.000000123S') = -16159791.227642277
Duration('-PT1.987654321S') / Duration('PT0.000123S') = -16159.791227642276
Duration('-PT1.987654321S') / Duration('PT0.123S') = -16.159791227642277
Duration('-PT1.987654321S') / Duration('PT2M3S') = -0.016159791227642276
Duration('-PT1.987654321S') / Duration('PT1.987654321S') = -1
Duration('-PT1.987654321S') / Duration('-PT1.987654321S') = 1
Duration('-PT1.987654321S') / Duration('PT2M3.987654321S') = -0.016031066414515977
Duration('-PT1.987654321S') / Duration('-PT2M3.987654321S') = 0.016031066414515977
Duration('PT2M3.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('PT2M3.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT2M3.987654321S') / 1 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') / 1.0 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') / -1 = Duration('-PT2M3.987654321S')
Duration('PT2M3.987654321S') / -1.0 = Duration('-PT2M3.987654321S')
Duration('PT2M3.987654321S') / 1.5 = Duration('PT1M22.658436214S')
Duration('PT2M3.987654321S') / -1.5 = Duration('-PT1M22.658436214S')
Duration('PT2M3.987654321S') / 2 = Duration('PT1M1.99382716S')
Duration('PT2M3.987654321S') / 2.0 = Duration('PT1M1.99382716S')
Duration('PT2M3.987654321S') / NAN = ValueError: Divisor can not be NaN
Duration('PT2M3.987654321S') / INF = Duration('PT0S')
Duration('PT2M3.987654321S') / -INF = Duration('PT0S')
Duration('PT2M3.987654321S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('PT2M3.987654321S') / Duration('PT0.000000123S') = 1008029709.9268292
Duration('PT2M3.987654321S') / Duration('PT0.000123S') = 1008029.7099268293
Duration('PT2M3.987654321S') / Duration('PT0.123S') = 1008.0297099268292
Duration('PT2M3.987654321S') / Duration('PT2M3S') = 1.0080297099268292
Duration('PT2M3.987654321S') / Duration('PT1.987654321S') = 62.37888198719641
Duration('PT2M3.987654321S') / Duration('-PT1.987654321S') = -62.37888198719641
Duration('PT2M3.987654321S') / Duration('PT2M3.987654321S') = 1
Duration('PT2M3.987654321S') / Duration('-PT2M3.987654321S') = -1
Duration('-PT2M3.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('-PT2M3.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('-PT2M3.987654321S') / 1 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') / 1.0 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') / -1 = Duration('PT2M3.987654321S')
Duration('-PT2M3.987654321S') / -1.0 = Duration('PT2M3.987654321S')
Duration('-PT2M3.987654321S') / 1.5 = Duration('-PT1M22.658436213S')
Duration('-PT2M3.987654321S') / -1.5 = Duration('PT1M22.658436213S')
Duration('-PT2M3.987654321S') / 2 = Duration('-PT1M1.993827161S')
Duration('-PT2M3.987654321S') / 2.0 = Duration('-PT1M1.993827161S')
Duration('-PT2M3.987654321S') / NAN = ValueError: Divisor can not be NaN
Duration('-PT2M3.987654321S') / INF = Duration('PT0S')
Duration('-PT2M3.987654321S') / -INF = Duration('PT0S')
Duration('-PT2M3.987654321S') / Duration('PT0S') = DivisionByZeroError: Division by zero
Duration('-PT2M3.987654321S') / Duration('PT0.000000123S') = -1008029709.9268292
Duration('-PT2M3.987654321S') / Duration('PT0.000123S') = -1008029.7099268293
Duration('-PT2M3.987654321S') / Duration('PT0.123S') = -1008.0297099268292
Duration('-PT2M3.987654321S') / Duration('PT2M3S') = -1.0080297099268292
Duration('-PT2M3.987654321S') / Duration('PT1.987654321S') = -62.37888198719641
Duration('-PT2M3.987654321S') / Duration('-PT1.987654321S') = 62.37888198719641
Duration('-PT2M3.987654321S') / Duration('PT2M3.987654321S') = -1
Duration('-PT2M3.987654321S') / Duration('-PT2M3.987654321S') = 1
