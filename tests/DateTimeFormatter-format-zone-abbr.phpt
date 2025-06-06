--TEST--
DateTimeFormatter->format zone abbreviation of offset
--FILE--
<?php

include __DIR__ . '/include.php';

$fmt = new \time\DateTimeFormatter('T');

$dt  = time\Instant::fromYmd(2000, 1, 1);
echo stringify($dt) . ' = ' . $fmt->format($dt) . "\n";

$berlin = time\Zone::fromIdentifier('Europe/Berlin');
$london = time\Zone::fromIdentifier('Europe/London');
//var_dump($london->info->getTransitionAt(time\Instant::fromUnixTimestampTuple([PHP_INT_MIN,0])));

$dt  = time\ZonedDateTime::fromYmd(2000, 1, 1, zone: $berlin);
echo stringify($dt) . ' = ' . $fmt->format($dt) . "\n";

$dt  = time\ZonedDateTime::fromYmd(2000, 7, 1, zone: $berlin);
echo stringify($dt) . ' = ' . $fmt->format($dt) . "\n";

$dt  = time\ZonedDateTime::fromYmd(1970, 1, 1, zone: $london);
echo stringify($dt) . ' = ' . $fmt->format($dt) . "\n";

$dt  = time\ZonedDateTime::fromYmd(2000, 1, 1, zone: $london);
echo stringify($dt) . ' = ' . $fmt->format($dt) . "\n";

$dt  = time\ZonedDateTime::fromYmd(2000, 7, 1, zone: $london);
echo stringify($dt) . ' = ' . $fmt->format($dt) . "\n";

--EXPECT--
Instant('Sat 2000-01-01 00:00:00', 946684800, 0) = GMT+0000
ZonedDateTime('Sat 2000-01-01 00:00:00 +01:00 [Europe/Berlin]') = CET
ZonedDateTime('Sat 2000-07-01 00:00:00 +02:00 [Europe/Berlin]') = CEST
ZonedDateTime('Thu 1970-01-01 00:00:00 +01:00 [Europe/London]') = BST
ZonedDateTime('Sat 2000-01-01 00:00:00 +00:00 [Europe/London]') = GMT
ZonedDateTime('Sat 2000-07-01 00:00:00 +01:00 [Europe/London]') = BST
