--TEST--
new Duration
--FILE--
<?php

include __DIR__ . '/include.php';

echo "new Duration()\n";
$duration = new time\Duration();
echo "  " . stringify($duration) . "\n";

echo "new Duration(hours: 123)\n";
$duration = new time\Duration(hours: 123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(hours: -123)\n";
$duration = new time\Duration(hours: -123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(minutes: 123)\n";
$duration = new time\Duration(minutes: 123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(minutes: -123)\n";
$duration = new time\Duration(minutes: -123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(seconds: 123)\n";
$duration = new time\Duration(seconds: 123);
echo "  " . stringify($duration) . "\n";

echo "new time\Duration(seconds: -123)\n";
$duration = new time\Duration(seconds: -123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(milliseconds: 123)\n";
$duration = new time\Duration(milliseconds: 123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(milliseconds: -123)\n";
$duration = new time\Duration(milliseconds: -123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(microseconds: 123)\n";
$duration = new time\Duration(microseconds: 123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(microseconds: -123)\n";
$duration = new time\Duration(microseconds: -123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(nanoseconds: 123)\n";
$duration = new time\Duration(nanoseconds: 123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(nanoseconds: -123)\n";
$duration = new time\Duration(nanoseconds: -123);
echo "  " . stringify($duration) . "\n";

echo "new Duration(10, 11, 12, 100, 101, 102)\n";
$duration = new time\Duration(10, 11, 12, 100, 101, 102);
echo "  " . stringify($duration) . "\n";

echo "new Duration(-25, -61, -61, -1001, -1002, -1003)\n";
$duration = new time\Duration(-25, -61, -61, -1001, -1002, -1003);
echo "  " . stringify($duration) . "\n";

--EXPECT--
new Duration()
  Duration('PT0S')
new Duration(hours: 123)
  Duration('PT123H')
new Duration(hours: -123)
  Duration('-PT123H')
new Duration(minutes: 123)
  Duration('PT2H3M')
new Duration(minutes: -123)
  Duration('-PT2H3M')
new Duration(seconds: 123)
  Duration('PT2M3S')
new time\Duration(seconds: -123)
  Duration('-PT2M3S')
new Duration(milliseconds: 123)
  Duration('PT0.123S')
new Duration(milliseconds: -123)
  Duration('-PT0.123S')
new Duration(microseconds: 123)
  Duration('PT0.000123S')
new Duration(microseconds: -123)
  Duration('-PT0.000123S')
new Duration(nanoseconds: 123)
  Duration('PT0.000000123S')
new Duration(nanoseconds: -123)
  Duration('-PT0.000000123S')
new Duration(10, 11, 12, 100, 101, 102)
  Duration('PT10H11M12.100101102S')
new Duration(-25, -61, -61, -1001, -1002, -1003)
  Duration('-PT26H2M2.002003003S')
