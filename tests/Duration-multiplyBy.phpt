--TEST--
Duration->multiplyBy()
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

$multipliers = [0, 0.0, 1, 1.0, -1, -1.0, 1.5, -1.5, NAN, INF, -INF];

foreach ($durations as $baseDuration) {
    foreach ($multipliers as $multiplier) {
        echo stringify($baseDuration) . ' * ' . stringify($multiplier) . ' = ';

        try {
            echo stringify($baseDuration->multiplyBy($multiplier)) . "\n";
        } catch (Throwable $e) {
            echo $e::class . ': ' . $e->getMessage() . "\n";
        }
    }
}

--EXPECT--
Duration('PT0S') * 0 = Duration('PT0S')
Duration('PT0S') * 0.0 = Duration('PT0S')
Duration('PT0S') * 1 = Duration('PT0S')
Duration('PT0S') * 1.0 = Duration('PT0S')
Duration('PT0S') * -1 = Duration('PT0S')
Duration('PT0S') * -1.0 = Duration('PT0S')
Duration('PT0S') * 1.5 = Duration('PT0S')
Duration('PT0S') * -1.5 = Duration('PT0S')
Duration('PT0S') * NAN = ValueError: Multiplier must be a finite number
Duration('PT0S') * INF = ValueError: Multiplier must be a finite number
Duration('PT0S') * -INF = ValueError: Multiplier must be a finite number
Duration('PT1.987654321S') * 0 = Duration('PT0S')
Duration('PT1.987654321S') * 0.0 = Duration('PT0S')
Duration('PT1.987654321S') * 1 = Duration('PT1.987654321S')
Duration('PT1.987654321S') * 1.0 = Duration('PT1.987654321S')
Duration('PT1.987654321S') * -1 = Duration('-PT1.987654321S')
Duration('PT1.987654321S') * -1.0 = Duration('-PT1.987654321S')
Duration('PT1.987654321S') * 1.5 = Duration('PT2.981481481S')
Duration('PT1.987654321S') * -1.5 = Duration('-PT2.981481481S')
Duration('PT1.987654321S') * NAN = ValueError: Multiplier must be a finite number
Duration('PT1.987654321S') * INF = ValueError: Multiplier must be a finite number
Duration('PT1.987654321S') * -INF = ValueError: Multiplier must be a finite number
Duration('-PT1.987654321S') * 0 = Duration('PT0S')
Duration('-PT1.987654321S') * 0.0 = Duration('PT0S')
Duration('-PT1.987654321S') * 1 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') * 1.0 = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') * -1 = Duration('PT1.987654321S')
Duration('-PT1.987654321S') * -1.0 = Duration('PT1.987654321S')
Duration('-PT1.987654321S') * 1.5 = Duration('-PT2.981481482S')
Duration('-PT1.987654321S') * -1.5 = Duration('PT2.981481482S')
Duration('-PT1.987654321S') * NAN = ValueError: Multiplier must be a finite number
Duration('-PT1.987654321S') * INF = ValueError: Multiplier must be a finite number
Duration('-PT1.987654321S') * -INF = ValueError: Multiplier must be a finite number
Duration('PT2M3.987654321S') * 0 = Duration('PT0S')
Duration('PT2M3.987654321S') * 0.0 = Duration('PT0S')
Duration('PT2M3.987654321S') * 1 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') * 1.0 = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') * -1 = Duration('-PT2M3.987654321S')
Duration('PT2M3.987654321S') * -1.0 = Duration('-PT2M3.987654321S')
Duration('PT2M3.987654321S') * 1.5 = Duration('PT3M5.981481481S')
Duration('PT2M3.987654321S') * -1.5 = Duration('-PT3M5.981481481S')
Duration('PT2M3.987654321S') * NAN = ValueError: Multiplier must be a finite number
Duration('PT2M3.987654321S') * INF = ValueError: Multiplier must be a finite number
Duration('PT2M3.987654321S') * -INF = ValueError: Multiplier must be a finite number
Duration('-PT2M3.987654321S') * 0 = Duration('PT0S')
Duration('-PT2M3.987654321S') * 0.0 = Duration('PT0S')
Duration('-PT2M3.987654321S') * 1 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') * 1.0 = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') * -1 = Duration('PT2M3.987654321S')
Duration('-PT2M3.987654321S') * -1.0 = Duration('PT2M3.987654321S')
Duration('-PT2M3.987654321S') * 1.5 = Duration('-PT3M5.981481482S')
Duration('-PT2M3.987654321S') * -1.5 = Duration('PT3M5.981481482S')
Duration('-PT2M3.987654321S') * NAN = ValueError: Multiplier must be a finite number
Duration('-PT2M3.987654321S') * INF = ValueError: Multiplier must be a finite number
Duration('-PT2M3.987654321S') * -INF = ValueError: Multiplier must be a finite number
