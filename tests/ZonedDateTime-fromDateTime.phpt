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

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::EARLIER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, time\Disambiguation::EARLIER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::LATER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, time\Disambiguation::LATER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::COMPATIBLE->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, time\Disambiguation::COMPATIBLE)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::REJECT->name . ")\n";
try {
    echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, disambiguation: time\Disambiguation::REJECT)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

$date = time\LocalDate::fromYmd(2000, time\Month::October, 29);
$time = time\LocalTime::fromHms(1, 59, 59);

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time)) . "\n";

$date = time\LocalDate::fromYmd(2000, time\Month::October, 29);
$time = time\LocalTime::fromHms(3, 0, 0);

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time)) . "\n";

$date = time\LocalDate::fromYmd(2000, time\Month::October, 29);
$time = time\LocalTime::fromHms(2, 30, 3, 123456);
$date = $time = time\LocalDateTime::fromDateTime($date, $time);

echo "ZonedDateTime::fromDateTime(" . stringify($utc) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($utc, $date, $time)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($utc) . ", " . stringify($date) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($utc, $date)) . "\n";

$date = time\LocalDate::fromYmd(2000, 3, 26);
$time = time\LocalTime::fromHms(1, 59, 59, 999999999);

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time)) . "\n";

$date = time\LocalDate::fromYmd(2000, 3, 26);
$time = time\LocalTime::fromHms(2, 0, 0, 0);

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::EARLIER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, time\Disambiguation::EARLIER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::LATER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, time\Disambiguation::LATER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::COMPATIBLE->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, time\Disambiguation::COMPATIBLE)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($bln) . ", " . stringify($date) . ", " . stringify($time) . ", " . time\Disambiguation::REJECT->name . ")\n";
try {
    echo stringify(time\ZonedDateTime::fromDateTime($bln, $date, $time, disambiguation: time\Disambiguation::REJECT)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECT--
ZonedDateTime::fromDateTime(time\Zone('UTC'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), EARLIER)
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), LATER)
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), COMPATIBLE)
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), REJECT)
RuntimeException: Ambiguous date-time '2000-10-29 02:30:03' for zone 'Europe/Berlin'
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('01:59:59'))
ZonedDateTime('Sun 2000-10-29 01:59:59 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-10-29'), LocalTime('03:00:00'))
ZonedDateTime('Sun 2000-10-29 03:00:00 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('UTC'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(time\Zone('UTC'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'))
ZonedDateTime('Sun 2000-10-29 00:00:00 +00:00 [UTC]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-03-26'), LocalTime('01:59:59.999999999'))
ZonedDateTime('Sun 2000-03-26 01:59:59.999999999 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), EARLIER)
ZonedDateTime('Sun 2000-03-26 01:00:00 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), LATER)
ZonedDateTime('Sun 2000-03-26 03:00:00 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), COMPATIBLE)
ZonedDateTime('Sun 2000-03-26 03:00:00 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(time\Zone('Europe/Berlin'), LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), REJECT)
RuntimeException: Invalid date-time '2000-03-26 02:00:00' for zone 'Europe/Berlin'
