--TEST--
Duration->isNegative, Duration->inverted(), Duration->abs() and Duration->negated()
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

foreach ($durations as $duration) {
    echo stringify($duration) . "\n";
    echo '  isNegative: ' . stringify($duration->isNegative) . "\n";
    echo '  inverted: ' . stringify($duration->inverted())
        . ($duration === $duration->inverted() ? " (same instance)\n" : "\n");
    echo '  abs: ' . stringify($duration->abs())
            . ($duration === $duration->abs() ? " (same instance)\n" : "\n");
    echo '  negated: ' . stringify($duration->negated())
            . ($duration === $duration->negated() ? " (same instance)\n" : "\n");
}

--EXPECT--
Duration('PT0S')
  isNegative: false
  inverted: Duration('PT0S') (same instance)
  abs: Duration('PT0S') (same instance)
  negated: Duration('PT0S') (same instance)
Duration('PT123H')
  isNegative: false
  inverted: Duration('-PT123H')
  abs: Duration('PT123H') (same instance)
  negated: Duration('-PT123H')
Duration('-PT123H')
  isNegative: true
  inverted: Duration('PT123H')
  abs: Duration('PT123H')
  negated: Duration('-PT123H') (same instance)
Duration('PT0.123S')
  isNegative: false
  inverted: Duration('-PT0.123S')
  abs: Duration('PT0.123S') (same instance)
  negated: Duration('-PT0.123S')
Duration('-PT0.123S')
  isNegative: true
  inverted: Duration('PT0.123S')
  abs: Duration('PT0.123S')
  negated: Duration('-PT0.123S') (same instance)
Duration('PT123H0.456S')
  isNegative: false
  inverted: Duration('-PT123H0.456S')
  abs: Duration('PT123H0.456S') (same instance)
  negated: Duration('-PT123H0.456S')
Duration('-PT123H0.456S')
  isNegative: true
  inverted: Duration('PT123H0.456S')
  abs: Duration('PT123H0.456S')
  negated: Duration('-PT123H0.456S') (same instance)
