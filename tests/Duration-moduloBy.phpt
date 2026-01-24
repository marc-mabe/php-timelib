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

$modulos = [0, 0.0, 1, 1.0, -1, -1.0, 1.5, -1.5, NAN]; 

foreach ($durations as $baseDuration) {
    foreach ($modulos as $modulo) {
        echo stringify($baseDuration) . ' % ' . stringify($modulo) . ' = ';

        try {
            echo stringify($baseDuration->moduloBy($modulo)) . "\n";
        } catch (Throwable $e) {
            echo $e::class . ': ' . $e->getMessage() . "\n";
        }
    }
}

--EXPECT--
Duration('PT0S') % 0 = DivisionByZeroError: Modulo by zero
Duration('PT0S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('PT0S') % 1 = Duration('PT0S')
Duration('PT0S') % 1.0 = Duration('PT0S')
Duration('PT0S') % -1 = Duration('PT0S')
Duration('PT0S') % -1.0 = Duration('PT0S')
Duration('PT0S') % 1.5 = Duration('PT0S')
Duration('PT0S') % -1.5 = Duration('PT0S')
Duration('PT0S') % NAN = ValueError: Divisor cannot be NaN
Duration('PT1.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('PT1.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('PT1.987654321S') % 1 = Duration('PT1.987654321S')
Duration('PT1.987654321S') % 1.0 = Duration('PT1.987654321S')
Duration('PT1.987654321S') % -1 = Duration('PT0S')
Duration('PT1.987654321S') % -1.0 = Duration('PT0S')
Duration('PT1.987654321S') % 1.5 = Duration('PT1S')
Duration('PT1.987654321S') % -1.5 = Duration('PT1S')
Duration('PT1.987654321S') % NAN = ValueError: Divisor cannot be NaN
Duration('-PT1.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('-PT1.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('-PT1.987654321S') % 1 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') % 1.0 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') % -1 = Duration('PT0S')
Duration('-PT1.987654321S') % -1.0 = Duration('PT0S')
Duration('-PT1.987654321S') % 1.5 = Duration('-PT0.499999999S')
Duration('-PT1.987654321S') % -1.5 = Duration('-PT0.499999999S')
Duration('-PT1.987654321S') % NAN = ValueError: Divisor cannot be NaN
Duration('PT2M3.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('PT2M3.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('PT2M3.987654321S') % 1 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') % 1.0 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') % -1 = Duration('PT0S')
Duration('PT2M3.987654321S') % -1.0 = Duration('PT0S')
Duration('PT2M3.987654321S') % 1.5 = Duration('PT0S')
Duration('PT2M3.987654321S') % -1.5 = Duration('PT0S')
Duration('PT2M3.987654321S') % NAN = ValueError: Divisor cannot be NaN
Duration('-PT2M3.987654321S') % 0 = DivisionByZeroError: Modulo by zero
Duration('-PT2M3.987654321S') % 0.0 = DivisionByZeroError: Modulo by zero
Duration('-PT2M3.987654321S') % 1 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') % 1.0 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') % -1 = Duration('PT0S')
Duration('-PT2M3.987654321S') % -1.0 = Duration('PT0S')
Duration('-PT2M3.987654321S') % 1.5 = Duration('-PT0.999999999S')
Duration('-PT2M3.987654321S') % -1.5 = Duration('-PT0.999999999S')
Duration('-PT2M3.987654321S') % NAN = ValueError: Divisor cannot be NaN
