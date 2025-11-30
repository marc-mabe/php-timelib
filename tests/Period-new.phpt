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

echo "new Period(true, 1, 2, 3, 4)\n";
$period = new time\Period(true, 1, 2, 3, 4);
echo "  " . stringify($period) . "\n";

echo "new Period(false, -1, -13, -25, -32)\n";
$period = new time\Period(false, -1, -13, -25, -32);
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
new Period(true, 1, 2, 3, 4)
  Period('-P1Y2M3W4D')
new Period(false, -1, -13, -25, -32)
  Period('P-1Y-13M-25W-32D')
