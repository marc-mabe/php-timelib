--TEST--
ZonedDateTime->fromDateTime
--FILE--
<?php

include __DIR__ . '/include.php';

$utc  = time\Zone::fromIdentifier('UTC');
$bln  = time\Zone::fromIdentifier('Europe/Berlin');

$date = time\LocalDate::fromYmd(2000, time\Month::October, 29);
$time = time\LocalTime::fromHms(2, 30, 3, 123456);

echo "ZonedDateTime::fromDateTime(" . stringify($utc) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($utc, $date, $time)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time)) . "\n";

$date = time\LocalDate::fromYmd(2000, time\Month::October, 29);
$time = time\LocalTime::fromHms(1, 59, 59);

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, time\LocalTime::fromHms(1, 59, 59))) . "\n";

$date = time\LocalDate::fromYmd(2000, time\Month::October, 29);
$time = time\LocalTime::fromHms(2, 30, 3, 123456);
$date = $time = time\LocalDateTime::fromDateTime($date, $time);

echo "ZonedDateTime::fromDateTime(" . stringify($utc) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($utc, $date, $time)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($utc) . ", " . stringify($date) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($utc, $date)) . "\n";

--EXPECT--
ZonedDateTime::fromDateTime(time\Zone('UTC'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('01:59:59'))
ZonedDateTime('Sun 2000-10-29 01:59:59 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('UTC'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(time\Zone('UTC'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 00:00:00 +00:00 [UTC]')
