--TEST--
Instant::fromUnixTimestamp (32bit)
--SKIPIF--
<?php if (PHP_INT_SIZE != 4) { die('This test is for 32 bit only'); }
--FILE--
<?php

include __DIR__ . '/include.php';

echo "fromUnixTimestamp(PHP_INT_MAX)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', (float)PHP_INT_MAX) . " /* (float)PHP_INT_MAX */)\n";
try {
    echo '  ' . stringify(time\Instant::fromUnixTimestamp((float)PHP_INT_MAX)) . "\n";
} catch (Throwable $e) {
    echo '  ' . $e::class . ": {$e->getMessage()}\n";
}

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MAX + 1) . " /* PHP_INT_MAX + 1 */)\n";
try {
    echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX + 1)) . "\n";
} catch (Throwable $e) {
    echo '  ' . $e::class . ": {$e->getMessage()}\n";
}

echo "fromUnixTimestamp(PHP_INT_MIN)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', (float)PHP_INT_MIN) . " /* (float)PHP_INT_MIN */)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp((float)PHP_INT_MIN)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MIN - 1) . " /* PHP_INT_MIN - 1 */)\n";
try {
    echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN - 1)) . "\n";
} catch (Throwable $e) {
    echo '  ' . $e::class . ": {$e->getMessage()}\n";
}

echo "fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Millisecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Millisecond)) . "\n";

echo "fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Millisecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Millisecond)) . "\n";

echo "fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Microsecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Microsecond)) . "\n";

echo "fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Microsecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Microsecond)) . "\n";

echo "fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Nanosecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Nanosecond)) . "\n";

echo "fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Nanosecond)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Nanosecond)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MAX / 60) . " /* PHP_INT_MAX / 60 */, TimeUnit::Minute)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX / 60, time\TimeUnit::Minute)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MIN / 60) . " /* PHP_INT_MIN / 60 */, TimeUnit::Minute)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN / 60, time\TimeUnit::Minute)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MAX / 3600) . " /* PHP_INT_MAX / 3600 */, TimeUnit::Hour)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX / 3600, time\TimeUnit::Hour)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MIN / 3600) . " /* PHP_INT_MIN / 3600 */, TimeUnit::Hour)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN / 3600, time\TimeUnit::Hour)) . "\n";

--EXPECT--
fromUnixTimestamp(PHP_INT_MAX)
  Instant('Tue 2038-01-19 03:14:07', 2147483647, 0)
fromUnixTimestamp(2147483647.000000 /* (float)PHP_INT_MAX */)
  Instant('Tue 2038-01-19 03:14:07', 2147483647, 0)
fromUnixTimestamp(2147483648.000000 /* PHP_INT_MAX + 1 */)
  time\RangeError: Timestamp must be between -2147483648 and 2147483647.999999999
fromUnixTimestamp(PHP_INT_MIN)
  Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)
fromUnixTimestamp(-2147483648.000000 /* (float)PHP_INT_MIN */)
  Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)
fromUnixTimestamp(-2147483649.000000 /* PHP_INT_MIN - 1 */)
  time\RangeError: Timestamp must be between -2147483648 and 2147483647.999999999
fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Millisecond)
  Instant('Sun 1970-01-25 20:31:23.647', 2147483, 647000000)
fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Millisecond)
  Instant('Sun 1969-12-07 03:28:36.352', -2147484, 352000000)
fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Microsecond)
  Instant('Thu 1970-01-01 00:35:47.483647', 2147, 483647000)
fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Microsecond)
  Instant('Wed 1969-12-31 23:24:12.516352', -2148, 516352000)
fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Nanosecond)
  Instant('Thu 1970-01-01 00:00:02.147483647', 2, 147483647)
fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Nanosecond)
  Instant('Wed 1969-12-31 23:59:57.852516352', -3, 852516352)
fromUnixTimestamp(35791394.116667 /* PHP_INT_MAX / 60 */, TimeUnit::Minute)
  Instant('Tue 2038-01-19 03:14:07.000000029', 2147483647, 29)
fromUnixTimestamp(-35791394.133333 /* PHP_INT_MIN / 60 */, TimeUnit::Minute)
  Instant('Fri 1901-12-13 20:45:52.00000003', -2147483648, 30)
fromUnixTimestamp(596523.235278 /* PHP_INT_MAX / 3600 */, TimeUnit::Hour)
  Instant('Tue 2038-01-19 03:14:06.999999973', 2147483646, 999999973)
fromUnixTimestamp(-596523.235556 /* PHP_INT_MIN / 3600 */, TimeUnit::Hour)
  Instant('Fri 1901-12-13 20:45:52.000000002', -2147483648, 2)
