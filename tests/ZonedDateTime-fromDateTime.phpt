--TEST--
ZonedDateTime->fromDateTime
--FILE--
<?php

include __DIR__ . '/include.php';

$utc  = time\Zone::fromIdentifier('UTC');
$bln  = time\Zone::fromIdentifier('Europe/Berlin');

$date = time\LocalDate::fromYmd(2000, 10, 29);
$time = time\LocalTime::fromHms(2, 30, 3, 123456);

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($utc) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $utc)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", " . time\Disambiguation::EARLIER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, time\Disambiguation::EARLIER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", " . time\Disambiguation::LATER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, time\Disambiguation::LATER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", " . time\Disambiguation::COMPATIBLE->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, time\Disambiguation::COMPATIBLE)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", disambiguation: " . time\Disambiguation::REJECT->name . ")\n";
try {
    echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, disambiguation: time\Disambiguation::REJECT)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

$date = time\LocalDate::fromYmd(2000, 10, 29);
$time = time\LocalTime::fromHms(1, 59, 59);

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln)) . "\n";

$date = time\LocalDate::fromYmd(2000, 10, 29);
$time = time\LocalTime::fromHms(3, 0, 0);

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln)) . "\n";

$date = time\LocalDate::fromYmd(2000, 10, 29);
$time = time\LocalTime::fromHms(2, 30, 3, 123456);
$date = $time = time\LocalDateTime::fromDateTime($date, $time);

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", ". stringify($utc) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $utc)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", zone: " . stringify($utc) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, zone: $utc)) . "\n";

$date = time\LocalDate::fromYmd(2000, 3, 26);
$time = time\LocalTime::fromHms(1, 59, 59, 999999999);

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln)) . "\n";

$date = time\LocalDate::fromYmd(2000, 3, 26);
$time = time\LocalTime::fromHms(2, 0, 0, 0);

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", " . time\Disambiguation::EARLIER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, time\Disambiguation::EARLIER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", " . time\Disambiguation::LATER->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, time\Disambiguation::LATER)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", " . stringify($bln) . ", " . time\Disambiguation::COMPATIBLE->name . ")\n";
echo stringify(time\ZonedDateTime::fromDateTime($date, $time, $bln, time\Disambiguation::COMPATIBLE)) . "\n";

echo "ZonedDateTime::fromDateTime(" . stringify($date) . ", " . stringify($time) . ", zone: " . stringify($bln) . ", disambiguation: " . time\Disambiguation::REJECT->name . ")\n";
try {
    echo stringify(time\ZonedDateTime::fromDateTime($date, $time, zone: $bln, disambiguation: time\Disambiguation::REJECT)) . "\n";
} catch (Throwable $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECT--
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), time\Zone('UTC'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), time\Zone('Europe/Berlin'), EARLIER)
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), time\Zone('Europe/Berlin'), LATER)
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), time\Zone('Europe/Berlin'), COMPATIBLE)
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('02:30:03.000123456'), time\Zone('Europe/Berlin'), disambiguation: REJECT)
RuntimeException: Ambiguous date-time '2000-10-29 02:30:03' for zone 'Europe/Berlin'
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('01:59:59'), time\Zone('Europe/Berlin'))
ZonedDateTime('Sun 2000-10-29 01:59:59 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-10-29'), LocalTime('03:00:00'), time\Zone('Europe/Berlin'))
ZonedDateTime('Sun 2000-10-29 03:00:00 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDateTime('Sun 2000-10-29 02:30:03.000123456'), LocalDateTime('Sun 2000-10-29 02:30:03.000123456'), time\Zone('UTC'))
ZonedDateTime('Sun 2000-10-29 02:30:03.000123456 +00:00 [UTC]')
ZonedDateTime::fromDateTime(LocalDateTime('Sun 2000-10-29 02:30:03.000123456'), zone: time\Zone('UTC'))
ZonedDateTime('Sun 2000-10-29 00:00:00 +00:00 [UTC]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-03-26'), LocalTime('01:59:59.999999999'), time\Zone('Europe/Berlin'))
ZonedDateTime('Sun 2000-03-26 01:59:59.999999999 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), time\Zone('Europe/Berlin'), EARLIER)
ZonedDateTime('Sun 2000-03-26 01:00:00 +01:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), time\Zone('Europe/Berlin'), LATER)
ZonedDateTime('Sun 2000-03-26 03:00:00 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), time\Zone('Europe/Berlin'), COMPATIBLE)
ZonedDateTime('Sun 2000-03-26 03:00:00 +02:00 [Europe/Berlin]')
ZonedDateTime::fromDateTime(LocalDate('Sun 2000-03-26'), LocalTime('02:00:00'), zone: time\Zone('Europe/Berlin'), disambiguation: REJECT)
RuntimeException: Invalid date-time '2000-03-26 02:00:00' for zone 'Europe/Berlin'
