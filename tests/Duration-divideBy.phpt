--TEST--
Duration->divideBy()
--FILE--
<?php

include __DIR__ . '/include.php';

$durations = [
    new time\Duration(),
    new time\Duration(seconds: 1, nanoseconds: 987654321),
    new time\Duration(seconds: -1, nanoseconds: -987654321),
    new time\Duration(seconds: 123, nanoseconds: 987654321),
    new time\Duration(seconds: -123, nanoseconds: -987654321),
];

$divisors = [0, 0.0, 1, 1.0, -1, -1.0, 1.5, -1.5, NAN]; 

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
Duration('PT0S') / NAN = ValueError: Divisor cannot be NaN
Duration('PT1.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('PT1.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT1.987654321S') / 1 = Duration('PT1.987654321S')
Duration('PT1.987654321S') / 1.0 = Duration('PT1.987654321S')
Duration('PT1.987654321S') / -1 = Duration('-PT1.987654321S')
Duration('PT1.987654321S') / -1.0 = Duration('-PT1.987654321S')
Duration('PT1.987654321S') / 1.5 = Duration('PT1.32510288S')
Duration('PT1.987654321S') / -1.5 = Duration('-PT1.32510288S')
Duration('PT1.987654321S') / NAN = ValueError: Divisor cannot be NaN
Duration('-PT1.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('-PT1.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('-PT1.987654321S') / 1 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') / 1.0 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') / -1 = Duration('PT1.987654321S')
Duration('-PT1.987654321S') / -1.0 = Duration('PT1.987654321S')
Duration('-PT1.987654321S') / 1.5 = Duration('-PT1.32510288S')
Duration('-PT1.987654321S') / -1.5 = Duration('PT1.32510288S')
Duration('-PT1.987654321S') / NAN = ValueError: Divisor cannot be NaN
Duration('PT2M3.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('PT2M3.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('PT2M3.987654321S') / 1 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') / 1.0 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') / -1 = Duration('-PT2M3.987654321S')
Duration('PT2M3.987654321S') / -1.0 = Duration('-PT2M3.987654321S')
Duration('PT2M3.987654321S') / 1.5 = Duration('PT1M22.658436214S')
Duration('PT2M3.987654321S') / -1.5 = Duration('-PT1M22.658436214S')
Duration('PT2M3.987654321S') / NAN = ValueError: Divisor cannot be NaN
Duration('-PT2M3.987654321S') / 0 = DivisionByZeroError: Division by zero
Duration('-PT2M3.987654321S') / 0.0 = DivisionByZeroError: Division by zero
Duration('-PT2M3.987654321S') / 1 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') / 1.0 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') / -1 = Duration('PT2M3.987654321S')
Duration('-PT2M3.987654321S') / -1.0 = Duration('PT2M3.987654321S')
Duration('-PT2M3.987654321S') / 1.5 = Duration('-PT1M22.658436213S')
Duration('-PT2M3.987654321S') / -1.5 = Duration('PT1M22.658436213S')
Duration('-PT2M3.987654321S') / NAN = ValueError: Divisor cannot be NaN
