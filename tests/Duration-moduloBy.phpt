--TEST--
Duration->moduloBy()
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

$dividers = [...$durations, 0, 0.0, 1, 1.0, -1, -1.0, 1.5, -1.5, 2, 2.0, -2, -2.0, NAN, INF, -INF];

foreach ($durations as $duration) {
    foreach ($dividers as $divider) {
        echo stringify($duration) . ' % ' . stringify($divider) . ' = ';

        try {
            echo stringify($duration->moduloBy($divider)) . "\n";
        } catch (Throwable $e) {
            echo $e::class . ': ' . $e->getMessage() . "\n";
        }
    }
}

--EXPECT--
Duration('PT0S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('PT0S') % Duration('PT1.987654321S') = Duration('PT0S')
Duration('PT0S') % Duration('-PT1.987654321S') = Duration('PT0S')
Duration('PT0S') % Duration('PT2M3.987654321S') = Duration('PT0S')
Duration('PT0S') % Duration('-PT2M3.987654321S') = Duration('PT0S')
Duration('PT0S') % 0 = DivisionByZeroError: Modulo by zero
Duration('PT0S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('PT0S') % 1 = Duration('PT0S')
Duration('PT0S') % 1.0 = Duration('PT0S')
Duration('PT0S') % -1 = Duration('PT0S')
Duration('PT0S') % -1.0 = Duration('PT0S')
Duration('PT0S') % 1.5 = Duration('PT0S')
Duration('PT0S') % -1.5 = Duration('PT0S')
Duration('PT0S') % 2 = Duration('PT0S')
Duration('PT0S') % 2.0 = Duration('PT0S')
Duration('PT0S') % -2 = Duration('PT0S')
Duration('PT0S') % -2.0 = Duration('PT0S')
Duration('PT0S') % NAN = ValueError: Divisor must be a Duration or a finite number
Duration('PT0S') % INF = ValueError: Divisor must be a Duration or a finite number
Duration('PT0S') % -INF = ValueError: Divisor must be a Duration or a finite number
Duration('PT1.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('PT1.987654321S') % Duration('PT1.987654321S') = Duration('PT0S')
Duration('PT1.987654321S') % Duration('-PT1.987654321S') = Duration('PT0S')
Duration('PT1.987654321S') % Duration('PT2M3.987654321S') = Duration('PT1.987654321S')
Duration('PT1.987654321S') % Duration('-PT2M3.987654321S') = Duration('PT1.987654321S')
Duration('PT1.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('PT1.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('PT1.987654321S') % 1 = Duration('PT0S')
Duration('PT1.987654321S') % 1.0 = Duration('PT0S')
Duration('PT1.987654321S') % -1 = Duration('PT0S')
Duration('PT1.987654321S') % -1.0 = Duration('PT0S')
Duration('PT1.987654321S') % 1.5 = Duration('PT0.000000001S')
Duration('PT1.987654321S') % -1.5 = Duration('PT0.000000001S')
Duration('PT1.987654321S') % 2 = Duration('PT0.000000001S')
Duration('PT1.987654321S') % 2.0 = Duration('PT0.000000001S')
Duration('PT1.987654321S') % -2 = Duration('PT0.000000001S')
Duration('PT1.987654321S') % -2.0 = Duration('PT0.000000001S')
Duration('PT1.987654321S') % NAN = ValueError: Divisor must be a Duration or a finite number
Duration('PT1.987654321S') % INF = ValueError: Divisor must be a Duration or a finite number
Duration('PT1.987654321S') % -INF = ValueError: Divisor must be a Duration or a finite number
Duration('-PT1.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('-PT1.987654321S') % Duration('PT1.987654321S') = Duration('PT0S')
Duration('-PT1.987654321S') % Duration('-PT1.987654321S') = Duration('PT0S')
Duration('-PT1.987654321S') % Duration('PT2M3.987654321S') = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') % Duration('-PT2M3.987654321S') = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('-PT1.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('-PT1.987654321S') % 1 = Duration('PT0S')
Duration('-PT1.987654321S') % 1.0 = Duration('PT0S')
Duration('-PT1.987654321S') % -1 = Duration('PT0S')
Duration('-PT1.987654321S') % -1.0 = Duration('PT0S')
Duration('-PT1.987654321S') % 1.5 = Duration('-PT0.000000001S')
Duration('-PT1.987654321S') % -1.5 = Duration('-PT0.000000001S')
Duration('-PT1.987654321S') % 2 = Duration('PT0.000000001S')
Duration('-PT1.987654321S') % 2.0 = Duration('PT0.000000001S')
Duration('-PT1.987654321S') % -2 = Duration('PT0.000000001S')
Duration('-PT1.987654321S') % -2.0 = Duration('PT0.000000001S')
Duration('-PT1.987654321S') % NAN = ValueError: Divisor must be a Duration or a finite number
Duration('-PT1.987654321S') % INF = ValueError: Divisor must be a Duration or a finite number
Duration('-PT1.987654321S') % -INF = ValueError: Divisor must be a Duration or a finite number
Duration('PT2M3.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('PT2M3.987654321S') % Duration('PT1.987654321S') = Duration('PT0.753086419S')
Duration('PT2M3.987654321S') % Duration('-PT1.987654321S') = Duration('PT0.753086419S')
Duration('PT2M3.987654321S') % Duration('PT2M3.987654321S') = Duration('PT0S')
Duration('PT2M3.987654321S') % Duration('-PT2M3.987654321S') = Duration('PT0S')
Duration('PT2M3.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('PT2M3.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('PT2M3.987654321S') % 1 = Duration('PT0S')
Duration('PT2M3.987654321S') % 1.0 = Duration('PT0S')
Duration('PT2M3.987654321S') % -1 = Duration('PT0S')
Duration('PT2M3.987654321S') % -1.0 = Duration('PT0S')
Duration('PT2M3.987654321S') % 1.5 = Duration('PT0S')
Duration('PT2M3.987654321S') % -1.5 = Duration('PT0S')
Duration('PT2M3.987654321S') % 2 = Duration('PT0.000000001S')
Duration('PT2M3.987654321S') % 2.0 = Duration('PT0.000000001S')
Duration('PT2M3.987654321S') % -2 = Duration('PT0.000000001S')
Duration('PT2M3.987654321S') % -2.0 = Duration('PT0.000000001S')
Duration('PT2M3.987654321S') % NAN = ValueError: Divisor must be a Duration or a finite number
Duration('PT2M3.987654321S') % INF = ValueError: Divisor must be a Duration or a finite number
Duration('PT2M3.987654321S') % -INF = ValueError: Divisor must be a Duration or a finite number
Duration('-PT2M3.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('-PT2M3.987654321S') % Duration('PT1.987654321S') = Duration('-PT0.753086419S')
Duration('-PT2M3.987654321S') % Duration('-PT1.987654321S') = Duration('-PT0.753086419S')
Duration('-PT2M3.987654321S') % Duration('PT2M3.987654321S') = Duration('PT0S')
Duration('-PT2M3.987654321S') % Duration('-PT2M3.987654321S') = Duration('PT0S')
Duration('-PT2M3.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('-PT2M3.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('-PT2M3.987654321S') % 1 = Duration('PT0S')
Duration('-PT2M3.987654321S') % 1.0 = Duration('PT0S')
Duration('-PT2M3.987654321S') % -1 = Duration('PT0S')
Duration('-PT2M3.987654321S') % -1.0 = Duration('PT0S')
Duration('-PT2M3.987654321S') % 1.5 = Duration('-PT0.000000001S')
Duration('-PT2M3.987654321S') % -1.5 = Duration('-PT0.000000002S')
Duration('-PT2M3.987654321S') % 2 = Duration('PT0.000000001S')
Duration('-PT2M3.987654321S') % 2.0 = Duration('PT0.000000001S')
Duration('-PT2M3.987654321S') % -2 = Duration('PT0.000000001S')
Duration('-PT2M3.987654321S') % -2.0 = Duration('PT0.000000001S')
Duration('-PT2M3.987654321S') % NAN = ValueError: Divisor must be a Duration or a finite number
Duration('-PT2M3.987654321S') % INF = ValueError: Divisor must be a Duration or a finite number
Duration('-PT2M3.987654321S') % -INF = ValueError: Divisor must be a Duration or a finite number
