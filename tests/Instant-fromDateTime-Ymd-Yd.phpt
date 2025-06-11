--TEST--
Instant::fromDateTime, fromYmd and fromYd
--FILE--
<?php

include __DIR__ . '/include.php';

$zoneUtc    = \time\Zone::fromIdentifier('UTC');
$zoneZero   = \time\Zone::fromIdentifier('+00:00');
$zoneBerlin = \time\Zone::fromIdentifier('Europe/Berlin');
$dateZero   = \time\LocalDate::fromYmd(0, 1, 1);
$dateEpoch  = \time\LocalDate::fromYmd(1970, 1, 1);
$dateUsual  = \time\LocalDate::fromYmd(2025, 2, 3);
$timeZero   = \time\LocalTime::fromHms(0, 0, 0);
$timeUsual  = \time\LocalTime::fromHms(16, 25, 6, 987654321);
$dtZero     = \time\LocalDateTime::fromDateTime($dateZero, $timeZero);
$dtEpoch    = \time\LocalDateTime::fromDateTime($dateEpoch, $timeZero);
$dtUsual    = \time\LocalDateTime::fromDateTime($dateUsual, $timeUsual);
$zdtZero    = \time\ZonedDateTime::fromDateTime($dateZero, $timeZero, zone: $zoneZero);
$zdtEpoch   = \time\ZonedDateTime::fromDateTime($dateEpoch, $timeZero, zone: $zoneUtc);
$zdtUsual   = \time\ZonedDateTime::fromDateTime($dateUsual, $timeUsual, zone: $zoneBerlin);

echo "Instant::fromDateTime\n";

echo "  fromDateTime(" . stringify($dateUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual)) . "\n";

echo "  fromDateTime(" . stringifyLocalDate($dateEpoch) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateEpoch)) . "\n";

echo "  fromDateTime(" . stringify($dateZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateZero)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual, $timeUsual)) . "\n";

echo "  fromDateTime(" . stringify($dateEpoch) . ", " . stringify($timeZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateEpoch, $timeZero)) . "\n";

echo "  fromDateTime(" . stringify($dateZero) . ", " . stringify($timeZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateZero, $timeZero)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ", " . stringify($dtUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtUsual, $dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dtEpoch) . ", " . stringify($dtEpoch) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtEpoch, $dtEpoch)) . "\n";

echo "  fromDateTime(" . stringify($dtZero) . ", " . stringify($dtZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtZero, $dtZero)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ", " . stringify($dtZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtUsual, $dtZero)) . "\n";

echo "  fromDateTime(" . stringify($dtZero) . ", " . stringify($dtUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtZero, $dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeUsual) . ", " . stringify($zoneBerlin) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual, $timeUsual, $zoneBerlin)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeUsual) . ", " . stringify($zoneUtc) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual, $timeUsual, $zoneUtc)) . "\n";

echo "  fromDateTime(" . stringify($dateZero) . ", " . stringify($timeZero) . ", " . stringify($zoneZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateZero, $timeZero, $zoneZero)) . "\n";

echo "Instant::fromYmd\n";

echo "  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)\n";
echo '    ' . stringify(time\Instant::fromYmd(2025, 2, 3, 16, 25, 6, 987654321)) . "\n";

echo "  fromYmd(1970, 1, 1)\n";
echo '    ' . stringify(time\Instant::fromYmd(1970, 1, 1)) . "\n";

echo "  fromYmd(0, 1, 1)\n";
echo '    ' . stringify(time\Instant::fromYmd(0, 1, 1)) . "\n";

echo "Instant::fromYd\n";

echo "  fromYd(2025, 34, 16, 25, 6, 987654321)\n";
echo '    ' . stringify(time\Instant::fromYd(2025, 34, 16, 25, 6, 987654321)) . "\n";

echo "  fromYd(1970, 1)\n";
echo '    ' . stringify(time\Instant::fromYd(1970, 1)) . "\n";

echo "  fromYd(0, 1)\n";
echo '    ' . stringify(time\Instant::fromYd(0, 1)) . "\n";

--EXPECT--
Instant::fromDateTime
  fromDateTime(LocalDate('Mon 2025-02-03'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDate('Thu 1970-01-01'))
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDate('Sat 0-01-01'))
    Instant('Sat 0-01-01 00:00:00', -62167219200, 0)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('16:25:06.987654321'))
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromDateTime(LocalDate('Thu 1970-01-01'), LocalTime('00:00:00'))
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDate('Sat 0-01-01'), LocalTime('00:00:00'))
    Instant('Sat 0-01-01 00:00:00', -62167219200, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 16:25:06.987654321'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 16:25:06.987654321'), LocalDateTime('Mon 2025-02-03 16:25:06.987654321'))
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromDateTime(LocalDateTime('Thu 1970-01-01 00:00:00'), LocalDateTime('Thu 1970-01-01 00:00:00'))
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDateTime('Sat 0-01-01 00:00:00'), LocalDateTime('Sat 0-01-01 00:00:00'))
    Instant('Sat 0-01-01 00:00:00', -62167219200, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 16:25:06.987654321'), LocalDateTime('Sat 0-01-01 00:00:00'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDateTime('Sat 0-01-01 00:00:00'), LocalDateTime('Mon 2025-02-03 16:25:06.987654321'))
    Instant('Sat 0-01-01 16:25:06.987654321', -62167160094, 987654321)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('16:25:06.987654321'), time\Zone('Europe/Berlin'))
    Instant('Mon 2025-02-03 15:25:06.987654321', 1738596306, 987654321)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('16:25:06.987654321'), time\Zone('UTC'))
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromDateTime(LocalDate('Sat 0-01-01'), LocalTime('00:00:00'), time\ZoneOffset('+00:00'))
    Instant('Sat 0-01-01 00:00:00', -62167219200, 0)
Instant::fromYmd
  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYmd(1970, 1, 1)
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromYmd(0, 1, 1)
    Instant('Sat 0-01-01 00:00:00', -62167219200, 0)
Instant::fromYd
  fromYd(2025, 34, 16, 25, 6, 987654321)
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYd(1970, 1)
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromYd(0, 1)
    Instant('Sat 0-01-01 00:00:00', -62167219200, 0)
