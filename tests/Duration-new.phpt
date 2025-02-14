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

echo "new Duration(true, 10, 11, 12, 100, 101, 102)\n";
$duration = new time\Duration(true, 10, 11, 12, 100, 101, 102);
echo "  " . stringify($duration) . "\n";

echo "new Duration(false, -25, -61, -61, -1001, -1002, -1003)\n";
$duration = new time\Duration(false, -25, -61, -61, -1001, -1002, -1003);
echo "  " . stringify($duration) . "\n";

--EXPECTF--
new Duration()
  Duration('PT0S')
new Duration(hours: 123)
  Duration('PT123H')
new Duration(hours: -123)
  Duration('PT-123H')
new Duration(minutes: 123)
  Duration('PT123M')
new Duration(minutes: -123)
  Duration('PT-123M')
new Duration(seconds: 123)
  Duration('PT123S')
new time\Duration(seconds: -123)
  Duration('PT-123S')
new Duration(milliseconds: 123)
  Duration('PT0.123S')
new Duration(milliseconds: -123)
  Duration('PT-0.123S')
new Duration(microseconds: 123)
  Duration('PT0.000123S')
new Duration(microseconds: -123)
  Duration('PT-0.000123S')
new Duration(nanoseconds: 123)
  Duration('PT0.000000123S')
new Duration(nanoseconds: -123)
  Duration('PT-0.000000123S')
new Duration(true, 10, 11, 12, 100, 101, 102)
  Duration('-PT10H11M12.100101102S')
new Duration(false, -25, -61, -61, -1001, -1002, -1003)
  Duration('PT-25H-61M-61.1002003003S')
