--TEST--
new Period
--FILE--
<?php

include __DIR__ . '/include.php';

echo "new Period()\n";
$period = new time\Period();
echo "  " . stringify($period) . "\n";

echo "new Period(years: 123)\n";
$period = new time\Period(years: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(years: -123)\n";
$period = new time\Period(years: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(months: 123)\n";
$period = new time\Period(months: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(months: -123)\n";
$period = new time\Period(months: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(days: 123)\n";
$period = new time\Period(days: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(days: -123)\n";
$period = new time\Period(days: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(hours: 123)\n";
$period = new time\Period(hours: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(hours: -123)\n";
$period = new time\Period(hours: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(minutes: 123)\n";
$period = new time\Period(minutes: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(minutes: -123)\n";
$period = new time\Period(minutes: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(seconds: 123)\n";
$period = new time\Period(seconds: 123);
echo "  " . stringify($period) . "\n";

echo "new time\Period(seconds: -123)\n";
$period = new time\Period(seconds: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(milliseconds: 123)\n";
$period = new time\Period(milliseconds: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(milliseconds: -123)\n";
$period = new time\Period(milliseconds: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(microseconds: 123)\n";
$period = new time\Period(microseconds: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(microseconds: -123)\n";
$period = new time\Period(microseconds: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(nanoseconds: 123)\n";
$period = new time\Period(nanoseconds: 123);
echo "  " . stringify($period) . "\n";

echo "new Period(nanoseconds: -123)\n";
$period = new time\Period(nanoseconds: -123);
echo "  " . stringify($period) . "\n";

echo "new Period(true, 1, 2, 3, 10, 11, 12, 100, 101, 102)\n";
$period = new time\Period(true, 1, 2, 3, 10, 11, 12, 100, 101, 102);
echo "  " . stringify($period) . "\n";

echo "new Period(false, -1, -13, -32, -25, -61, -61, -1001, -1002, -1003)\n";
$period = new time\Period(false, -1, -13, -32, -25, -61, -61, -1001, -1002, -1003);
echo "  " . stringify($period) . "\n";

--EXPECT--
new Period()
  Period('P0D')
new Period(years: 123)
  Period('P123Y')
new Period(years: -123)
  Period('P-123Y')
new Period(months: 123)
  Period('P123M')
new Period(months: -123)
  Period('P-123M')
new Period(days: 123)
  Period('P123D')
new Period(days: -123)
  Period('P-123D')
new Period(hours: 123)
  Period('PT123H')
new Period(hours: -123)
  Period('PT-123H')
new Period(minutes: 123)
  Period('PT123M')
new Period(minutes: -123)
  Period('PT-123M')
new Period(seconds: 123)
  Period('PT123S')
new time\Period(seconds: -123)
  Period('PT-123S')
new Period(milliseconds: 123)
  Period('PT0.123S')
new Period(milliseconds: -123)
  Period('PT-0.123S')
new Period(microseconds: 123)
  Period('PT0.000123S')
new Period(microseconds: -123)
  Period('PT-0.000123S')
new Period(nanoseconds: 123)
  Period('PT0.000000123S')
new Period(nanoseconds: -123)
  Period('PT-0.000000123S')
new Period(true, 1, 2, 3, 10, 11, 12, 100, 101, 102)
  Period('-P1Y2M3DT10H11M12.100101102S')
new Period(false, -1, -13, -32, -25, -61, -61, -1001, -1002, -1003)
  Period('P-1Y-13M-32DT-25H-61M-62.002003003S')
