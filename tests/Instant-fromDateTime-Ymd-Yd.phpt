--TEST--
Instant::fromDateTime, fromYmd and fromYd
--FILE--
<?php

include __DIR__ . '/include.php';

$min = time\LocalDateTime::min();

$zoneUtc    = \time\Zone::fromIdentifier('UTC');
$zoneZero   = \time\Zone::fromIdentifier('+00:00');
$zoneBerlin = \time\Zone::fromIdentifier('Europe/Berlin');
$dateMin    = $min->date;
$dateEpoch  = \time\LocalDate::fromYmd(1970, 1, 1);
$dateUsual  = \time\LocalDate::fromYmd(2025, 2, 3);
$timeZero   = \time\LocalTime::fromHms(0, 0, 0);
$timeMax    = \time\LocalTime::fromHms(23, 59, 59, 999999999);
$dtMin      = \time\LocalDateTime::fromDateTime($min->date, $min->time);
$dtEpoch    = \time\LocalDateTime::fromDateTime($dateEpoch, $timeZero);
$dtUsual    = \time\LocalDateTime::fromDateTime($dateUsual, $timeMax);
$zdtMin     = \time\ZonedDateTime::fromDateTime($min->date, $min->time, zone: $zoneZero);
$zdtEpoch   = \time\ZonedDateTime::fromDateTime($dateEpoch, $timeZero, zone: $zoneUtc);
$zdtUsual   = \time\ZonedDateTime::fromDateTime($dateUsual, $timeMax, zone: $zoneBerlin);

echo "Instant::fromDateTime\n";

echo "  fromDateTime(" . stringify($dateUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual)) . "\n";

echo "  fromDateTime(" . stringifyLocalDate($dateEpoch) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateEpoch)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual, $timeZero)) . "\n";

echo "  fromDateTime(" . stringify($dateEpoch) . ", " . stringify($timeZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateEpoch, $timeZero)) . "\n";

echo "  fromDateTime(" . stringify($dateMin) . ", " . stringify($timeMax) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateMin, $timeMax)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ", " . stringify($dtUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtUsual, $dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dtEpoch) . ", " . stringify($dtEpoch) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtEpoch, $dtEpoch)) . "\n";

echo "  fromDateTime(" . stringify($dtMin) . ", " . stringify($dtMin) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtMin, $dtMin)) . "\n";

echo "  fromDateTime(" . stringify($dtUsual) . ", " . stringify($dtMin) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtUsual, $dtMin)) . "\n";

echo "  fromDateTime(" . stringify($dtMin) . ", " . stringify($dtUsual) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dtMin, $dtUsual)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeMax) . ", " . stringify($zoneBerlin) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual, $timeMax, $zoneBerlin)) . "\n";

echo "  fromDateTime(" . stringify($dateUsual) . ", " . stringify($timeMax) . ", " . stringify($zoneUtc) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateUsual, $timeMax, $zoneUtc)) . "\n";

echo "  fromDateTime(" . stringify($dateMin) . ", " . stringify($timeMax) . ", " . stringify($zoneZero) . ")\n";
echo '    ' . stringify(time\Instant::fromDateTime($dateMin, $timeMax, $zoneZero)) . "\n";

echo "Instant::fromYmd\n";

echo "  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)\n";
echo '    ' . stringify(time\Instant::fromYmd(2025, 2, 3, 16, 25, 6, 987654321)) . "\n";

echo "  fromYmd(1970, 1, 1)\n";
echo '    ' . stringify(time\Instant::fromYmd(1970, 1, 1)) . "\n";

echo "  fromYmd({$dateMin->year}, {$dateMin->month}, {$dateMin->dayOfMonth}, {$timeMax->hour}, {$timeMax->minute}, {$timeMax->second}, {$timeMax->nanoOfSecond})\n";
echo '    ' . stringify(time\Instant::fromYmd($dateMin->year, $dateMin->month, $dateMin->dayOfMonth, $timeMax->hour, $timeMax->minute, $timeMax->second, $timeMax->nanoOfSecond)) . "\n";

echo "Instant::fromYd\n";

echo "  fromYd(2025, 34, 16, 25, 6, 987654321)\n";
echo '    ' . stringify(time\Instant::fromYd(2025, 34, 16, 25, 6, 987654321)) . "\n";

echo "  fromYd(1970, 1)\n";
echo '    ' . stringify(time\Instant::fromYd(1970, 1)) . "\n";

echo "  fromYd({$dateMin->year}, {$dateMin->dayOfYear}, {$timeMax->hour}, {$timeMax->minute}, {$timeMax->second}, {$timeMax->nanoOfSecond})\n";
echo '    ' . stringify(time\Instant::fromYd($dateMin->year, $dateMin->dayOfYear, $timeMax->hour, $timeMax->minute, $timeMax->second, $timeMax->nanoOfSecond)) . "\n";

--EXPECTF--
Instant::fromDateTime
  fromDateTime(LocalDate('Mon 2025-02-03'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDate('Thu 1970-01-01'))
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDate('Mon 2025-02-03'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('00:00:00'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDate('Thu 1970-01-01'), LocalTime('00:00:00'))
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDate('%s'), LocalTime('23:59:59.999999999'))
    Instant('%s 23:59:59.999999999', -%d, 999999999)
  fromDateTime(LocalDateTime('Mon 2025-02-03 23:59:59.999999999'))
    Instant('Mon 2025-02-03 00:00:00', 1738540800, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 23:59:59.999999999'), LocalDateTime('Mon 2025-02-03 23:59:59.999999999'))
    Instant('Mon 2025-02-03 23:59:59.999999999', 1738627199, 999999999)
  fromDateTime(LocalDateTime('Thu 1970-01-01 00:00:00'), LocalDateTime('Thu 1970-01-01 00:00:00'))
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromDateTime(LocalDateTime('%s'), LocalDateTime('%s'))
    Instant('%s', -%d, 0)
  fromDateTime(LocalDateTime('Mon 2025-02-03 23:59:59.999999999'), LocalDateTime('%s'))
    Instant('Mon 2025-02-03 %s', %d, 0)
  fromDateTime(LocalDateTime('%s'), LocalDateTime('Mon 2025-02-03 23:59:59.999999999'))
    Instant('%s 23:59:59.999999999', -%d, 999999999)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('23:59:59.999999999'), time\Zone('Europe/Berlin'))
    Instant('Mon 2025-02-03 22:59:59.999999999', 1738623599, 999999999)
  fromDateTime(LocalDate('Mon 2025-02-03'), LocalTime('23:59:59.999999999'), time\Zone('UTC'))
    Instant('Mon 2025-02-03 23:59:59.999999999', 1738627199, 999999999)
  fromDateTime(LocalDate('%s'), LocalTime('23:59:59.999999999'), time\ZoneOffset('+00:00'))
    Instant('%s 23:59:59.999999999', -%d, 999999999)
Instant::fromYmd
  fromYmd(2025, 2, 3, 16, 25, 6, 987654321)
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYmd(1970, 1, 1)
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromYmd(%i, %d, %d, 23, 59, 59, 999999999)
    Instant('%s 23:59:59.999999999', -%d, 999999999)
Instant::fromYd
  fromYd(2025, 34, 16, 25, 6, 987654321)
    Instant('Mon 2025-02-03 16:25:06.987654321', 1738599906, 987654321)
  fromYd(1970, 1)
    Instant('Thu 1970-01-01 00:00:00', 0, 0)
  fromYd(%i, %d, 23, 59, 59, 999999999)
    Instant('%s 23:59:59.999999999', -%d, 999999999)
