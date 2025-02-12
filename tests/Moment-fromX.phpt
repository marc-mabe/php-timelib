--TEST--
Moment::fromXXX
--FILE--
<?php

include __DIR__ . '/include.php';

$zoneUtc    = \time\ZoneOffset::fromIdentifier('UTC');
$zoneZero   = \time\ZoneOffset::fromIdentifier('+00:00');
$zoneBerlin = \time\ZoneOffset::fromIdentifier('Europe/Berlin');
$dateZero   = \time\LocalDate::fromYmd(0, 1, 1);
$dateEpoch  = \time\LocalDate::fromYmd(1970, 1, 1);
$dateUsual  = \time\LocalDate::fromYmd(2025, 2, 3);
$timeZero   = \time\LocalTime::fromHms(0, 0, 0);
$timeUsual  = \time\LocalTime::fromHms(16, 25, 6, 987654321);
$dtZero     = \time\LocalDateTime::fromDateTime($dateZero, $timeZero);
$dtEpoch    = \time\LocalDateTime::fromDateTime($dateEpoch, $timeZero);
$dtUsual    = \time\LocalDateTime::fromDateTime($dateUsual, $timeUsual);
$zdtZero    = \time\ZonedDateTime::fromDateTime($zoneZero, $dateZero, $timeZero);
$zdtEpoch   = \time\ZonedDateTime::fromDateTime($zoneUtc, $dateEpoch, $timeZero);
$zdtUsual   = \time\ZonedDateTime::fromDateTime($zoneBerlin, $dateUsual, $timeUsual);

echo "Moment::fromUnixTimestamp\n";

echo "  fromUnixTimestamp(1738599906)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(1738599906)) . "\n";

echo "  fromUnixTimestamp(1738599906.987654321)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(1738599906.987654321)) . "\n";

echo "  fromUnixTimestamp(0)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(0)) . "\n";

echo "  fromUnixTimestamp(0.987654321)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(0.987654321)) . "\n";

echo "  fromUnixTimestamp(-1)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(-1)) . "\n";

echo "  fromUnixTimestamp(-1.987654321)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(-1.987654321)) . "\n";

echo "  fromUnixTimestamp(-1738599906)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(-1738599906)) . "\n";

echo "  fromUnixTimestamp(-1738599906.987654321)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(-1738599906.987654321)) . "\n";

echo "  fromUnixTimestamp(1738599906, TimeUnit::Second)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(1738599906, \time\TimeUnit::Second)) . "\n";

echo "  fromUnixTimestamp(1738599906987, TimeUnit::Millisecond)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(1738599906987, \time\TimeUnit::Millisecond)) . "\n";

echo "  fromUnixTimestamp(1738599906987654, TimeUnit::Microsecond)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(1738599906987654, \time\TimeUnit::Microsecond)) . "\n";

echo "  fromUnixTimestamp(1738599906987654321, TimeUnit::Nanosecond)\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestamp(1738599906987654321, \time\TimeUnit::Nanosecond)) . "\n";

echo "Moment::fromUnixTimestampTuple\n";

echo "  fromUnixTimestampTuple([1738599906, 123456789])\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestampTuple([1738599906, 123456789])) . "\n";

echo "  fromUnixTimestampTuple([0, 0])\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestampTuple([0, 0])) . "\n";

echo "  fromUnixTimestampTuple([-1, 1])\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestampTuple([-1, 1])) . "\n";

echo "  fromUnixTimestampTuple([-1738599906, 987654])\n";
echo '    ' . stringify(\time\Moment::fromUnixTimestampTuple([-1738599906, 987654])) . "\n";

echo "Moment::fromYd\n";

echo "  fromYd(2025, 34, 16, 25, 6, 987654321)\n";
echo '    ' . stringify(\time\Moment::fromYd(2025, 34, 16, 25, 6, 987654321)) . "\n";

echo "  fromYd(1970, 1)\n";
echo '    ' . stringify(\time\Moment::fromYd(1970, 1)) . "\n";

echo "  fromYd(0, 1)\n";
echo '    ' . stringify(\time\Moment::fromYd(0, 1)) . "\n";

echo "Moment::fromYmd\n";

echo "  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)\n";
echo '    ' . stringify(\time\Moment::fromYmd(2025, 2, 3, 16, 25, 6, 987654321)) . "\n";

echo "  fromYmd(1970, 1, 1)\n";
echo '    ' . stringify(\time\Moment::fromYmd(1970, 1, 1)) . "\n";

echo "  fromYmd(0, 1, 1)\n";
echo '    ' . stringify(\time\Moment::fromYmd(0, 1, 1)) . "\n";

echo "Moment::fromDateTime\n";

echo "  fromDateTime(" . stringify($dateUsual) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateUsual)) . "\n";

echo "  fromDateTime(" . stringifyLocalDate($dateEpoch) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateEpoch)) . "\n";

echo "  fromDateTime(" . stringify($dateZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateZero)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeUsual) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateUsual, $timeUsual)) . "\n";

echo "  fromDateTime(" . stringify($dateEpoch) . ", " . stringify($timeZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateEpoch, $timeZero)) . "\n";

echo "  fromDateTime(" . stringify($dateZero) . ", " . stringify($timeZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateZero, $timeZero)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ", " . stringify($dtUsual) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dtUsual, $dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dtEpoch) . ", " . stringify($dtEpoch) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dtEpoch, $dtEpoch)) . "\n";

echo "  fromDateTime(" . stringify($dtZero) . ", " . stringify($dtZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dtZero, $dtZero)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ", " . stringify($dtZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dtUsual, $dtZero)) . "\n";

echo "  fromDateTime(" . stringify($dtZero) . ", " . stringify($dtUsual) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dtZero, $dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeUsual) . ", " . stringify($zoneBerlin) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateUsual, $timeUsual, $zoneBerlin)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeUsual) . ", " . stringify($zoneUtc) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateUsual, $timeUsual, $zoneUtc)) . "\n";

echo "  fromDateTime(" . stringify($dateZero) . ", " . stringify($timeZero) . ", " . stringify($zoneZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromDateTime($dateZero, $timeZero, $zoneZero)) . "\n";

echo "Moment::fromZonedDateTime\n";

echo "  fromZonedDateTime(" . stringify($zdtUsual) . ")\n";
echo '    ' . stringify(\time\Moment::fromZonedDateTime($zdtUsual)) . "\n";

echo "  fromZonedDateTime(" . stringify($zdtEpoch) . ")\n";
echo '    ' . stringify(\time\Moment::fromZonedDateTime($zdtEpoch)) . "\n";

echo "  fromZonedDateTime(" . stringify($zdtZero) . ")\n";
echo '    ' . stringify(\time\Moment::fromZonedDateTime($zdtZero)) . "\n";

--EXPECT--
Moment::fromUnixTimestamp
  fromUnixTimestamp(1738599906)
    Moment('Mon 2025-02-03 16:25:06', 1738599906, 0)
  fromUnixTimestamp(1738599906.987654321)
    Moment('Mon 2025-02-03 16:25:06.987654209', 1738599906, 987654209)
  fromUnixTimestamp(0)
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromUnixTimestamp(0.987654321)
    Moment('Thu 1970-01-01 00:00:00.987654321', 0, 987654321)
  fromUnixTimestamp(-1)
    Moment('Wed 1969-12-31 23:59:59', -1, 0)
  fromUnixTimestamp(-1.987654321)
    Moment('Wed 1969-12-31 23:59:58.012345679', -2, 12345679)
  fromUnixTimestamp(-1738599906)
    Moment('Sat 1914-11-28 07:34:54', -1738599906, 0)
  fromUnixTimestamp(-1738599906.987654321)
    Moment('Sat 1914-11-28 07:34:53.01234579', -1738599907, 12345790)
  fromUnixTimestamp(1738599906, TimeUnit::Second)
    Moment('Mon 2025-02-03 16:25:06', 1738599906, 0)
  fromUnixTimestamp(1738599906987, TimeUnit::Millisecond)
    Moment('Mon 2025-02-03 16:25:06.987', 1738599906, 987000000)
  fromUnixTimestamp(1738599906987654, TimeUnit::Microsecond)
    Moment('Mon 2025-02-03 16:25:06.987654', 1738599906, 987654000)
  fromUnixTimestamp(1738599906987654321, TimeUnit::Nanosecond)
    Moment('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
Moment::fromUnixTimestampTuple
  fromUnixTimestampTuple([1738599906, 123456789])
    Moment('Mon 2025-02-03 16:25:06.123456789', 1738599906, 123456789)
  fromUnixTimestampTuple([0, 0])
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromUnixTimestampTuple([-1, 1])
    Moment('Wed 1969-12-31 23:59:59.000000001', -1, 1)
  fromUnixTimestampTuple([-1738599906, 987654])
    Moment('Sat 1914-11-28 07:34:54.000987654', -1738599906, 987654)
Moment::fromYd
  fromYd(2025, 34, 16, 25, 6, 987654321)
    Moment('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYd(1970, 1)
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromYd(0, 1)
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
Moment::fromYmd
  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)
    Moment('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYmd(1970, 1, 1)
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromYmd(0, 1, 1)
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
Moment::fromDateTime
  fromDateTime(LocalDate('Mon 2025-02-03'))
    Moment('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDate('Thu 1970-01-01'))
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDate('Sat 0-01-01'))
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('16:25:06.987654321'))
    Moment('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromDateTime(LocalDate('Thu 1970-01-01'), LocalTime('00:00:00'))
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDate('Sat 0-01-01'), LocalTime('00:00:00'))
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 16:25:06.987654321'))
    Moment('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 16:25:06.987654321'), LocalDateTime('Mon 2025-02-03 16:25:06.987654321'))
    Moment('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromDateTime(LocalDateTime('Thu 1970-01-01 00:00:00'), LocalDateTime('Thu 1970-01-01 00:00:00'))
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDateTime('Sat 0-01-01 00:00:00'), LocalDateTime('Sat 0-01-01 00:00:00'))
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 16:25:06.987654321'), LocalDateTime('Sat 0-01-01 00:00:00'))
    Moment('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDateTime('Sat 0-01-01 00:00:00'), LocalDateTime('Mon 2025-02-03 16:25:06.987654321'))
    Moment('Sat 0-01-01 16:25:06.987654321', -62167160094, 987654321)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('16:25:06.987654321'), ZoneOffset('Europe/Berlin'))
    Moment('Mon 2025-02-03 15:25:06.987654321', 1738596306, 987654321)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('16:25:06.987654321'), ZoneOffset('UTC'))
    Moment('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromDateTime(LocalDate('Sat 0-01-01'), LocalTime('00:00:00'), ZoneOffset('+00:00'))
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
Moment::fromZonedDateTime
  fromZonedDateTime(ZonedDateTime('Mon 2025-02-03 16:25:06.987654321 +01:00 [Europe/Berlin]'))
    Moment('Mon 2025-02-03 15:25:06.987654321', 1738596306, 987654321)
  fromZonedDateTime(ZonedDateTime('Thu 1970-01-01 00:00:00 +00:00 [UTC]'))
    Moment('Thu 1970-01-01 00:00:00', 0, 0)
  fromZonedDateTime(ZonedDateTime('Sat 0-01-01 00:00:00 +00:00 [+00:00]'))
    Moment('Sat 0-01-01 00:00:00', -62167219200, 0)
