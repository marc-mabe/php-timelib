--TEST--
Instant::fromUnixTimestamp (64bit)
--SKIPIF--
<?php if (PHP_INT_SIZE != 8) { die('This test is for 64 bit only'); }
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

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MAX + 2048) . " /* PHP_INT_MAX + 2048 */)\n";
try {
    echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MAX + 2048)) . "\n";
} catch (Throwable $e) {
    echo '  ' . $e::class . ": {$e->getMessage()}\n";
}

echo "fromUnixTimestamp(PHP_INT_MIN)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', (float)PHP_INT_MIN) . " /* (float)PHP_INT_MIN */)\n";
echo '  ' . stringify(time\Instant::fromUnixTimestamp((float)PHP_INT_MIN)) . "\n";

echo "fromUnixTimestamp(" . sprintf('%f', PHP_INT_MIN - 2048) . " /* PHP_INT_MIN - 2048 */)\n";
try {
    echo '  ' . stringify(time\Instant::fromUnixTimestamp(PHP_INT_MIN - 2048)) . "\n";
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
  Instant('Sun 292277026596-12-04 15:30:07', 9223372036854775807, 0)
fromUnixTimestamp(9223372036854775808.000000 /* (float)PHP_INT_MAX */)
  time\RangeError: Timestamp must be between -9223372036854775808 and 9223372036854775807.999999999
fromUnixTimestamp(9223372036854777856.000000 /* PHP_INT_MAX + 2048 */)
  time\RangeError: Timestamp must be between -9223372036854775808 and 9223372036854775807.999999999
fromUnixTimestamp(PHP_INT_MIN)
  Instant('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)
fromUnixTimestamp(-9223372036854775808.000000 /* (float)PHP_INT_MIN */)
  Instant('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)
fromUnixTimestamp(-9223372036854777856.000000 /* PHP_INT_MIN - 2048 */)
  time\RangeError: Timestamp must be between -9223372036854775808 and 9223372036854775807.999999999
fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Millisecond)
  Instant('Sun 292278994-08-17 07:12:55.807', 9223372036854775, 807000000)
fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Millisecond)
  Instant('Sun -292275055-05-16 16:47:04.192', -9223372036854776, 192000000)
fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Microsecond)
  Instant('Sun 294247-01-10 04:00:54.775807', 9223372036854, 775807000)
fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Microsecond)
  Instant('Sun -290308-12-21 19:59:05.224192', -9223372036855, 224192000)
fromUnixTimestamp(PHP_INT_MAX, time\TimeUnit::Nanosecond)
  Instant('Fri 2262-04-11 23:47:16.854775807', 9223372036, 854775807)
fromUnixTimestamp(PHP_INT_MIN, time\TimeUnit::Nanosecond)
  Instant('Tue 1677-09-21 00:12:43.145224192', -9223372037, 145224192)
fromUnixTimestamp(153722867280912928.000000 /* PHP_INT_MAX / 60 */, TimeUnit::Minute)
  Instant('Sun 292277026596-12-04 15:28:00', 9223372036854775680, 0)
fromUnixTimestamp(-153722867280912928.000000 /* PHP_INT_MIN / 60 */, TimeUnit::Minute)
  Instant('Sun -292277022657-01-27 08:32:00', -9223372036854775680, 0)
fromUnixTimestamp(2562047788015215.500000 /* PHP_INT_MAX / 3600 */, TimeUnit::Hour)
  Instant('Sun 292277026596-12-04 15:30:00', 9223372036854775800, 0)
fromUnixTimestamp(-2562047788015215.500000 /* PHP_INT_MIN / 3600 */, TimeUnit::Hour)
  Instant('Sun -292277022657-01-27 08:30:00', -9223372036854775800, 0)
