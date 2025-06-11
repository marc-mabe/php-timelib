--TEST--
Instant::fromUnixTimestampTuple
--FILE--
<?php

include __DIR__ . '/include.php';

echo "fromUnixTimestampTuple([1738599906, 123456789])\n";
echo '  ' . stringify(time\Instant::fromUnixTimestampTuple([1738599906, 123456789])) . "\n";

echo "fromUnixTimestampTuple([0, 0])\n";
echo '  ' . stringify(time\Instant::fromUnixTimestampTuple([0, 0])) . "\n";

echo "fromUnixTimestampTuple([-1, 1])\n";
echo '  ' . stringify(time\Instant::fromUnixTimestampTuple([-1, 1])) . "\n";

echo "fromUnixTimestampTuple([-1738599906, 987654])\n";
echo '  ' . stringify(time\Instant::fromUnixTimestampTuple([-1738599906, 987654])) . "\n";

--EXPECT--
fromUnixTimestampTuple([1738599906, 123456789])
  Instant('Mon 2025-02-03 16:25:06.123456789', 1738599906, 123456789)
fromUnixTimestampTuple([0, 0])
  Instant('Thu 1970-01-01 00:00:00', 0, 0)
fromUnixTimestampTuple([-1, 1])
  Instant('Wed 1969-12-31 23:59:59.000000001', -1, 1)
fromUnixTimestampTuple([-1738599906, 987654])
  Instant('Sat 1914-11-28 07:34:54.000987654', -1738599906, 987654)
