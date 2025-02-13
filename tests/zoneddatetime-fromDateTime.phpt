--TEST--
DateTime.fromDateTime
--FILE--
<?php

include __DIR__ . '/include.php';

$date = time\LocalDate::fromYmd(2000, time\Month::January, 1);
$time = time\LocalTime::fromHms(1, 2, 3, 123456);
$ldt  = time\LocalDateTime::fromDateTime($date, $time);
$zone = time\Zone::fromIdentifier('UTC');

echo "ZonedDateTime::fromDateTime(" . stringify($zone) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($zone, $date, $time)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($zone) . ", " . stringify($ldt) . ", " . stringify($ldt) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($zone, $ldt, $ldt)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($zone) . ", " . stringify($ldt) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($zone, $ldt)) . "\n";

--EXPECT--
ZonedDateTime::fromDateTime(Zone('UTC'), LocalDate('Sat 2000-01-01'), LocalTime('01:02:03.000123456'))
ZonedDateTime('Sat 2000-01-01 01:02:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(Zone('UTC'), LocalDateTime('Sat 2000-01-01 01:02:03.000123456'), LocalDateTime('Sat 2000-01-01 01:02:03.000123456'))
ZonedDateTime('Sat 2000-01-01 01:02:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(Zone('UTC'), LocalDateTime('Sat 2000-01-01 01:02:03.000123456'))
ZonedDateTime('Sat 2000-01-01 00:00:00 +00:00 [UTC]')
