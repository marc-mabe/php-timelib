--TEST--
Moment::fromXXX
--FILE--
<?php

include __DIR__ . '/../vendor/autoload.php';

echo "Moment::fromUnixTimestamp\n";

echo "  fromUnixTimestamp(1738599906)\n";
$moment = \dt\Moment::fromUnixTimestamp(1738599906);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(0)\n";
$moment = \dt\Moment::fromUnixTimestamp(0);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(-1)\n";
$moment = \dt\Moment::fromUnixTimestamp(-1);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(-1738599906)\n";
$moment = \dt\Moment::fromUnixTimestamp(-1738599906);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(1738599906, TimeUnit::Second)\n";
$moment = \dt\Moment::fromUnixTimestamp(1738599906, \dt\TimeUnit::Second);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(1738599906987, TimeUnit::Millisecond)\n";
$moment = \dt\Moment::fromUnixTimestamp(1738599906987, \dt\TimeUnit::Millisecond);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(1738599906987654, TimeUnit::Microsecond)\n";
$moment = \dt\Moment::fromUnixTimestamp(1738599906987654, \dt\TimeUnit::Microsecond);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestamp(1738599906987654321, TimeUnit::Nanosecond)\n";
$moment = \dt\Moment::fromUnixTimestamp(1738599906987654321, \dt\TimeUnit::Nanosecond);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "Moment::fromUnixTimestampTuple\n";

echo "  fromUnixTimestampTuple([1738599906, 123456789])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([1738599906, 123456789]);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestampTuple([0, 0])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([0, 0]);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestampTuple([-1, 1])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([-1, 1]);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromUnixTimestampTuple([-1738599906, 987654])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([-1738599906, 987654]);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "Moment::fromYd\n";

echo "  fromYd(2025, 34)\n";
$moment = \dt\Moment::fromYd(2025, 34, 16, 25, 6, 987654321);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromYd(1970, 1)\n";
$moment = \dt\Moment::fromYd(1970, 1);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

echo "  fromYd(0, 1)\n";
$moment = \dt\Moment::fromYd(0, 1);
echo "    {$moment->format('Y-m-d H:i:sf')} ({$moment->toUnixTimestamp(\dt\TimeUnit::Nanosecond)})\n";

--EXPECT--
Moment::fromUnixTimestamp
  fromUnixTimestamp(1738599906)
    2025-02-03 16:25:06 (1738599906000000000)
  fromUnixTimestamp(0)
    1970-01-01 00:00:00 (0)
  fromUnixTimestamp(-1)
    1969-12-31 23:59:59 (-1000000000)
  fromUnixTimestamp(-1738599906)
    1914-11-28 07:34:54 (-1738599906000000000)
  fromUnixTimestamp(1738599906, TimeUnit::Second)
    2025-02-03 16:25:06 (1738599906000000000)
  fromUnixTimestamp(1738599906987, TimeUnit::Millisecond)
    2025-02-03 16:25:06.987 (1738599906987000000)
  fromUnixTimestamp(1738599906987654, TimeUnit::Microsecond)
    2025-02-03 16:25:06.987654 (1738599906987654000)
  fromUnixTimestamp(1738599906987654321, TimeUnit::Nanosecond)
    2025-02-03 16:25:06.987654321 (1738599906987654321)
Moment::fromUnixTimestampTuple
  fromUnixTimestampTuple([1738599906, 123456789])
    2025-02-03 16:25:06.123456789 (1738599906123456789)
  fromUnixTimestampTuple([0, 0])
    1970-01-01 00:00:00 (0)
  fromUnixTimestampTuple([-1, 1])
    1969-12-31 23:59:59.000000001 (-999999999)
  fromUnixTimestampTuple([-1738599906, 987654])
    1914-11-28 07:34:54.000987654 (-1738599905999012346)
Moment::fromYd
  fromYd(2025, 34)
    2025-02-03 16:25:06.987654321 (1738599906987654321)
  fromYd(1970, 1)
    1970-01-01 00:00:00 (0)
  fromYd(0, 1)
    0-01-01 00:00:00 (-6.21672192E+19)
