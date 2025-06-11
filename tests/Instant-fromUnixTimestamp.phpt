--TEST--
Instant::fromUnixTimestamp
--FILE--
<?php

include __DIR__ . '/include.php';

echo "fromUnixTimestamp(PHP_INT_MAX)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX)) . "\n";

echo "fromUnixTimestamp(PHP_INT_MIN)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN)) . "\n";

echo "fromUnixTimestamp(1738599906)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(1738599906)) . "\n";

echo "fromUnixTimestamp(1738599906.987654321)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(1738599906.987654321)) . "\n";

echo "fromUnixTimestamp(0)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(0)) . "\n";

echo "fromUnixTimestamp(0.987654321)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(0.987654321)) . "\n";

echo "fromUnixTimestamp(-1)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(-1)) . "\n";

echo "fromUnixTimestamp(-1.987654321)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(-1.987654321)) . "\n";

echo "fromUnixTimestamp(-1738599906)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(-1738599906)) . "\n";

echo "fromUnixTimestamp(-1738599906.987654321)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(-1738599906.987654321)) . "\n";

echo "fromUnixTimestamp(1738599906, TimeUnit::Second)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(1738599906, time\TimeUnit::Second)) . "\n";

echo "fromUnixTimestamp(1599906987, TimeUnit::Millisecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(1599906987, time\TimeUnit::Millisecond)) . "\n";

echo "fromUnixTimestamp(1906987654, TimeUnit::Microsecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(1906987654, time\TimeUnit::Microsecond)) . "\n";

echo "fromUnixTimestamp(1987654321, TimeUnit::Nanosecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(1987654321, time\TimeUnit::Nanosecond)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MAX / 60) . " /* PHP_INT_MAX / 60 */, TimeUnit::Minute)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX / 60, time\TimeUnit::Minute)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MIN / 60) . " /* PHP_INT_MIN / 60 */, TimeUnit::Minute)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN / 60, time\TimeUnit::Minute)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MAX / 3600) . " /* PHP_INT_MAX / 3600 */, TimeUnit::Hour)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX / 3600, time\TimeUnit::Hour)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MIN / 3600) . " /* PHP_INT_MIN / 3600 */, TimeUnit::Hour)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN / 3600, time\TimeUnit::Hour)) . "\n";
--EXPECTF--
fromUnixTimestamp(PHP_INT_MAX)
  Instant('%s', %d, 0)
fromUnixTimestamp(PHP_INT_MIN)
  Instant('%s', -%d, 0)
fromUnixTimestamp(1738599906)
  Instant('Mon 2025-02-03 16:25:06', 1738599906, 0)
fromUnixTimestamp(1738599906.987654321)
  Instant('Mon 2025-02-03 16:25:06.987654209', 1738599906, 987654209)
fromUnixTimestamp(0)
  Instant('Thu 1970-01-01 00:00:00', 0, 0)
fromUnixTimestamp(0.987654321)
  Instant('Thu 1970-01-01 00:00:00.987654321', 0, 987654321)
fromUnixTimestamp(-1)
  Instant('Wed 1969-12-31 23:59:59', -1, 0)
fromUnixTimestamp(-1.987654321)
  Instant('Wed 1969-12-31 23:59:58.012345679', -2, 12345679)
fromUnixTimestamp(-1738599906)
  Instant('Sat 1914-11-28 07:34:54', -1738599906, 0)
fromUnixTimestamp(-1738599906.987654321)
  Instant('Sat 1914-11-28 07:34:53.012345791', -1738599907, 12345791)
fromUnixTimestamp(1738599906, TimeUnit::Second)
  Instant('Mon 2025-02-03 16:25:06', 1738599906, 0)
fromUnixTimestamp(1599906987, TimeUnit::Millisecond)
  Instant('Mon 1970-01-19 12:25:06.987', 1599906, 987000000)
fromUnixTimestamp(1906987654, TimeUnit::Microsecond)
  Instant('Thu 1970-01-01 00:31:46.987654', 1906, 987654000)
fromUnixTimestamp(1987654321, TimeUnit::Nanosecond)
  Instant('Thu 1970-01-01 00:00:01.987654321', 1, 987654321)
fromUnixTimestamp(%f /* PHP_INT_MAX / 60 */, TimeUnit::Minute)
  Instant('%s', %d, %d)
fromUnixTimestamp(%f /* PHP_INT_MIN / 60 */, TimeUnit::Minute)
  Instant('%s', -%d, %d)
fromUnixTimestamp(%f /* PHP_INT_MAX / 3600 */, TimeUnit::Hour)
  Instant('%s', %d, %d)
fromUnixTimestamp(-%f /* PHP_INT_MIN / 3600 */, TimeUnit::Hour)
  Instant('%s', -%d, %d)
