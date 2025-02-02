--TEST--
DateTime.fromDateTime
--FILE--
<?php

include __DIR__ . '/../vendor/autoload.php';

$date = dt\LocalDate::fromYmd(2000, dt\Month::January, 1);
$time = dt\LocalTime::fromHms(1, 2, 3, 123456);
$ldt  = dt\LocalDateTime::fromDateTime($date, $time);
$zone = dt\ZoneOffset::fromIdentifier('UTC');

echo "ZonedDateTime::fromDateTime(ZoneOffset({$zone->format('e')}), LocalDate({$date->format('Y-m-d')}), LocalTime({$time->format('H:i:s.u')}))\n";
$dt   = dt\ZonedDateTime::fromDateTime($zone, $date, $time);
var_dump($dt->format('Y-m-d H:i:s.uP[e]'));

echo "ZonedDateTime::fromDateTime(ZoneOffset({$zone->format('e')}), LocalDateTime({$ldt->format('Y-m-d H:i:s.u')}), LocalDateTime({$ldt->format('Y-m-d H:i:s.u')}))\n";
$dt   = dt\ZonedDateTime::fromDateTime($zone, $ldt, $ldt);
var_dump($dt->format('Y-m-d H:i:s.uP[e]'));

echo "ZonedDateTime::fromDateTime(ZoneOffset({$zone->format('e')}), LocalDateTime({$ldt->format('Y-m-d H:i:s.u')}))\n";
$dt   = dt\ZonedDateTime::fromDateTime($zone, $ldt);
var_dump($dt->format('Y-m-d H:i:s.uP[e]'));

--EXPECT--
ZonedDateTime::fromDateTime(ZoneOffset(UTC), LocalDate(2000-01-01), LocalTime(01:02:03.000123))
string(37) "2000-01-01 01:02:03.000123+00:00[UTC]"
ZonedDateTime::fromDateTime(ZoneOffset(UTC), LocalDateTime(2000-01-01 01:02:03.000123), LocalDateTime(2000-01-01 01:02:03.000123))
string(37) "2000-01-01 01:02:03.000123+00:00[UTC]"
ZonedDateTime::fromDateTime(ZoneOffset(UTC), LocalDateTime(2000-01-01 01:02:03.000123))
string(37) "2000-01-01 00:00:00.000000+00:00[UTC]"
