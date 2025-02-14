--TEST--
Duration->abs(), Duration->negated() and Duration->isNegative
--FILE--
<?php

include __DIR__ . '/include.php';

$durations = [
    new time\Duration(),
    new time\Duration(hours: 123),
    new time\Duration(hours: -123),
    new time\Duration(milliseconds: 123),
    new time\Duration(milliseconds: -123),
    new time\Duration(hours: 123, milliseconds: 456),
    new time\Duration(hours: -123, milliseconds: -456),
];

echo "Duration\tisNegative\tabs\tnegated\n";
foreach ($durations as $duration) {
    echo stringify($duration)
        . "\t" . stringify($duration->isNegative)
        . "\t" . stringify($duration->abs())
        . "\t" . stringify($duration->negated())
        . "\n";
}

--EXPECTF--
Duration	isNegative	abs	negated
Duration('PT0S')	false	Duration('PT0S')	Duration('PT0S')
Duration('PT123H')	false	Duration('PT123H')	Duration('-PT123H')
Duration('-PT123H')	true	Duration('PT123H')	Duration('-PT123H')
Duration('PT0.123S')	false	Duration('PT0.123S')	Duration('-PT0.123S')
Duration('-PT0.123S')	true	Duration('PT0.123S')	Duration('-PT0.123S')
Duration('PT123H0.456S')	false	Duration('PT123H0.456S')	Duration('-PT123H0.456S')
Duration('-PT123H0.456S')	true	Duration('PT123H0.456S')	Duration('-PT123H0.456S')
