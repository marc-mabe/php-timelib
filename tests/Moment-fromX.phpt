--TEST--
Moment::fromXXX
--FILE--
<?php

include __DIR__ . '/include.php';

echo "Moment::fromUnixTimestamp\n";

echo "  fromUnixTimestamp(1738599906)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(1738599906)) . "\n";

echo "  fromUnixTimestamp(1738599906.987654321)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(1738599906.987654321)) . "\n";

echo "  fromUnixTimestamp(0)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(0)) . "\n";

echo "  fromUnixTimestamp(0.987654321)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(0.987654321)) . "\n";

echo "  fromUnixTimestamp(-1)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(-1)) . "\n";

echo "  fromUnixTimestamp(-1.987654321)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(-1.987654321)) . "\n";

echo "  fromUnixTimestamp(-1738599906)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(-1738599906)) . "\n";

echo "  fromUnixTimestamp(-1738599906.987654321)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(-1738599906.987654321)) . "\n";

echo "  fromUnixTimestamp(1738599906, TimeUnit::Second)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(1738599906, \dt\TimeUnit::Second)) . "\n";

echo "  fromUnixTimestamp(1738599906987, TimeUnit::Millisecond)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(1738599906987, \dt\TimeUnit::Millisecond)) . "\n";

echo "  fromUnixTimestamp(1738599906987654, TimeUnit::Microsecond)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(1738599906987654, \dt\TimeUnit::Microsecond)) . "\n";

echo "  fromUnixTimestamp(1738599906987654321, TimeUnit::Nanosecond)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestamp(1738599906987654321, \dt\TimeUnit::Nanosecond)) . "\n";

echo "Moment::fromUnixTimestampTuple\n";

echo "  fromUnixTimestampTuple([1738599906, 123456789])\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestampTuple([1738599906, 123456789])) . "\n";

echo "  fromUnixTimestampTuple([0, 0])\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestampTuple([0, 0])) . "\n";

echo "  fromUnixTimestampTuple([-1, 1])\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestampTuple([-1, 1])) . "\n";

echo "  fromUnixTimestampTuple([-1738599906, 987654])\n";
echo '    ' . stringifyMoment(\dt\Moment::fromUnixTimestampTuple([-1738599906, 987654])) . "\n";

echo "Moment::fromYd\n";

echo "  fromYd(2025, 34, 16, 25, 6, 987654321)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromYd(2025, 34, 16, 25, 6, 987654321)) . "\n";

echo "  fromYd(1970, 1)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromYd(1970, 1)) . "\n";

echo "  fromYd(0, 1)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromYd(0, 1)) . "\n";

echo "Moment::fromYmd\n";

echo "  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromYmd(2025, 2, 3, 16, 25, 6, 987654321)) . "\n";

echo "  fromYmd(1970, 1, 1)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromYmd(1970, 1, 1)) . "\n";

echo "  fromYmd(0, 1, 1)\n";
echo '    ' . stringifyMoment(\dt\Moment::fromYmd(0, 1, 1)) . "\n";

--EXPECT--
Moment::fromUnixTimestamp
  fromUnixTimestamp(1738599906)
    Moment('2025-02-03 16:25:06', 1738599906, 0)
  fromUnixTimestamp(1738599906.987654321)
    Moment('2025-02-03 16:25:06.987654209', 1738599906, 987654209)
  fromUnixTimestamp(0)
    Moment('1970-01-01 00:00:00', 0, 0)
  fromUnixTimestamp(0.987654321)
    Moment('1970-01-01 00:00:00.987654321', 0, 987654321)
  fromUnixTimestamp(-1)
    Moment('1969-12-31 23:59:59', -1, 0)
  fromUnixTimestamp(-1.987654321)
    Moment('1969-12-31 23:59:58.012345679', -2, 12345679)
  fromUnixTimestamp(-1738599906)
    Moment('1914-11-28 07:34:54', -1738599906, 0)
  fromUnixTimestamp(-1738599906.987654321)
    Moment('1914-11-28 07:34:53.01234579', -1738599907, 12345790)
  fromUnixTimestamp(1738599906, TimeUnit::Second)
    Moment('2025-02-03 16:25:06', 1738599906, 0)
  fromUnixTimestamp(1738599906987, TimeUnit::Millisecond)
    Moment('2025-02-03 16:25:06.987', 1738599906, 987000000)
  fromUnixTimestamp(1738599906987654, TimeUnit::Microsecond)
    Moment('2025-02-03 16:25:06.987654', 1738599906, 987654000)
  fromUnixTimestamp(1738599906987654321, TimeUnit::Nanosecond)
    Moment('2025-02-03 16:25:06.987654321', 1738599906, 987654321)
Moment::fromUnixTimestampTuple
  fromUnixTimestampTuple([1738599906, 123456789])
    Moment('2025-02-03 16:25:06.123456789', 1738599906, 123456789)
  fromUnixTimestampTuple([0, 0])
    Moment('1970-01-01 00:00:00', 0, 0)
  fromUnixTimestampTuple([-1, 1])
    Moment('1969-12-31 23:59:59.000000001', -1, 1)
  fromUnixTimestampTuple([-1738599906, 987654])
    Moment('1914-11-28 07:34:54.000987654', -1738599906, 987654)
Moment::fromYd
  fromYd(2025, 34, 16, 25, 6, 987654321)
    Moment('2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYd(1970, 1)
    Moment('1970-01-01 00:00:00', 0, 0)
  fromYd(0, 1)
    Moment('0-01-01 00:00:00', -62167219200, 0)
Moment::fromYmd
  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)
    Moment('2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYmd(1970, 1, 1)
    Moment('1970-01-01 00:00:00', 0, 0)
  fromYmd(0, 1, 1)
    Moment('0-01-01 00:00:00', -62167219200, 0)
