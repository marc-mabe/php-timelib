--TEST--
ZonedDateTime->add() and Instant->sub() with Period
--FILE--
<?php

include __DIR__ . '/include.php';

$bln = time\Zone::fromIdentifier('Europe/Berlin');
$p1m = new time\Period(minutes: 1);
$p1d = new time\Period(days: 1);
$p1ns = new time\Period(nanoseconds: 1);
$zdt = time\ZonedDateTime::fromYmd($bln, 2000, 3, 26, 1, 59, 59, 987654321);

echo stringify($zdt) . "\n"
    . ' add(' . stringify($p1m) . ') = ' . stringify($zdt->add($p1m)) . "\n";

echo stringify($zdt) . "\n"
    . ' add(' . stringify($p1d) . ') = ' . stringify($zdt->add($p1d)) . "\n";

$zdt = time\ZonedDateTime::fromYmd($bln, 2000, 3, 27, 1, 59, 59, 999999999);
echo stringify($zdt) . "\n"
    . ' sub(' . stringify($p1d) . ') = ' . stringify($zdt->sub($p1d)) . "\n";

$zdt = time\ZonedDateTime::fromYmd($bln, 2000, 3, 27, 2);
echo stringify($zdt) . "\n"
    . ' sub(' . stringify($p1d) . ') = ' . stringify($zdt->sub($p1d)) . "\n";

$zdt = time\ZonedDateTime::fromYmd($bln, 2000, 1, 1);
echo stringify($zdt) . "\n"
    . ' sub(' . stringify($p1ns) . ') = ' . stringify($zdt->sub($p1ns)) . "\n";

--EXPECT--
ZonedDateTime('Sun 2000-03-26 01:59:59.987654321 +01:00 [Europe/Berlin]')
 add(Period('PT1M')) = ZonedDateTime('Sun 2000-03-26 03:00:59.987654321 +02:00 [Europe/Berlin]')
ZonedDateTime('Sun 2000-03-26 01:59:59.987654321 +01:00 [Europe/Berlin]')
 add(Period('P1D')) = ZonedDateTime('Mon 2000-03-27 01:59:59.987654321 +02:00 [Europe/Berlin]')
ZonedDateTime('Mon 2000-03-27 01:59:59.999999999 +02:00 [Europe/Berlin]')
 sub(Period('P1D')) = ZonedDateTime('Sun 2000-03-26 01:59:59.999999999 +01:00 [Europe/Berlin]')
ZonedDateTime('Mon 2000-03-27 02:00:00 +02:00 [Europe/Berlin]')
 sub(Period('P1D')) = ZonedDateTime('Sun 2000-03-26 03:00:00 +02:00 [Europe/Berlin]')
ZonedDateTime('Sat 2000-01-01 00:00:00 +01:00 [Europe/Berlin]')
 sub(Period('PT0.000000001S')) = ZonedDateTime('Fri 1999-12-31 23:59:59.999999999 +01:00 [Europe/Berlin]')
