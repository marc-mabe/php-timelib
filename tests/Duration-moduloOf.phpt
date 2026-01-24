--TEST--
Duration->moduloOf()
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

foreach ($durations as $baseDuration) {
    foreach ($durations as $otherDuration) {
        echo stringify($baseDuration) . ' % ' . stringify($otherDuration) . ' = ';

        try {
            echo stringify($baseDuration->moduloOf($otherDuration)) . "\n";
        } catch (Throwable $e) {
            echo $e::class . ': ' . $e->getMessage() . "\n";
        }
    }
}

--EXPECT--
Duration('PT0S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('PT0S') % Duration('PT1.987654321S') = 0.0
Duration('PT0S') % Duration('-PT1.987654321S') = 0.0
Duration('PT0S') % Duration('PT2M3.987654321S') = 0.0
Duration('PT0S') % Duration('-PT2M3.987654321S') = 0.0
Duration('PT1.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('PT1.987654321S') % Duration('PT1.987654321S') = 0.0
Duration('PT1.987654321S') % Duration('-PT1.987654321S') = 1.000000001
Duration('PT1.987654321S') % Duration('PT2M3.987654321S') = 1.0
Duration('PT1.987654321S') % Duration('-PT2M3.987654321S') = 1.000000001
Duration('-PT1.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('-PT1.987654321S') % Duration('PT1.987654321S') = 0.012345679
Duration('-PT1.987654321S') % Duration('-PT1.987654321S') = 0.0
Duration('-PT1.987654321S') % Duration('PT2M3.987654321S') = -1.987654321
Duration('-PT1.987654321S') % Duration('-PT2M3.987654321S') = -2.0
Duration('PT2M3.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('PT2M3.987654321S') % Duration('PT1.987654321S') = 0.0
Duration('PT2M3.987654321S') % Duration('-PT1.987654321S') = 1.000000001
Duration('PT2M3.987654321S') % Duration('PT2M3.987654321S') = 0.0
Duration('PT2M3.987654321S') % Duration('-PT2M3.987654321S') = 123.000000001
Duration('-PT2M3.987654321S') % Duration('PT0S') = DivisionByZeroError: Modulo by zero
Duration('-PT2M3.987654321S') % Duration('PT1.987654321S') = 0.012345679
Duration('-PT2M3.987654321S') % Duration('-PT1.987654321S') = 0.0
Duration('-PT2M3.987654321S') % Duration('PT2M3.987654321S') = -0.987654321
Duration('-PT2M3.987654321S') % Duration('-PT2M3.987654321S') = 0.0
