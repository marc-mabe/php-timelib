--TEST--
ZonedDateTime->add() and Moment->sub() with Duration
--FILE--
<?php

include __DIR__ . '/include.php';

$tzBln = time\Zone::fromIdentifier('Europe/Berlin');

$zonedDateTimes = [
    time\ZonedDateTime::fromUnixTimestampTuple([0, 0]),
    time\ZonedDateTime::fromUnixTimestampTuple([123, 456]),
    time\ZonedDateTime::fromUnixTimestampTuple([-123, 456]),
    time\ZonedDateTime::fromYmd($tzBln, 2000, 3, 26, 1, 59, 59, 987654321),
];

$durations = [
    new time\Duration(seconds: 123),
    new time\Duration(seconds: 123, milliseconds: 456),
    new time\Duration(seconds: 123, milliseconds: 456, microseconds: 789),
    new time\Duration(seconds: 123, milliseconds: 456, microseconds: 789, nanoseconds: 876),
    new time\Duration(hours: 123),
    new time\Duration(hours: 123, minutes: 345),
    new time\Duration(hours: 123, minutes: 345, seconds: 678),
];

foreach ($zonedDateTimes as $zonedDateTime) {
    echo stringify($zonedDateTime) . "\n";
    foreach ($durations as $duration) {
        echo " add " . stringify($duration) . " = " . stringify($zonedDateTime->add($duration)) . "\n";
        echo " sub " . stringify($duration) . " = " . stringify($zonedDateTime->sub($duration)) . "\n";
    }
}

--EXPECT--
ZonedDateTime('Thu 1970-01-01 00:00:00 +00:00 [+00:00]')
 add Duration('PT2M3S') = ZonedDateTime('Thu 1970-01-01 00:02:03 +00:00 [+00:00]')
 sub Duration('PT2M3S') = ZonedDateTime('Wed 1969-12-31 23:57:57 +00:00 [+00:00]')
 add Duration('PT2M3.456S') = ZonedDateTime('Thu 1970-01-01 00:02:03.456 +00:00 [+00:00]')
 sub Duration('PT2M3.456S') = ZonedDateTime('Wed 1969-12-31 23:57:56.544 +00:00 [+00:00]')
 add Duration('PT2M3.456789S') = ZonedDateTime('Thu 1970-01-01 00:02:03.456789 +00:00 [+00:00]')
 sub Duration('PT2M3.456789S') = ZonedDateTime('Wed 1969-12-31 23:57:56.543211 +00:00 [+00:00]')
 add Duration('PT2M3.456789876S') = ZonedDateTime('Thu 1970-01-01 00:02:03.456789876 +00:00 [+00:00]')
 sub Duration('PT2M3.456789876S') = ZonedDateTime('Wed 1969-12-31 23:57:56.543210124 +00:00 [+00:00]')
 add Duration('PT123H') = ZonedDateTime('Tue 1970-01-06 03:00:00 +00:00 [+00:00]')
 sub Duration('PT123H') = ZonedDateTime('Fri 1969-12-26 21:00:00 +00:00 [+00:00]')
 add Duration('PT128H45M') = ZonedDateTime('Tue 1970-01-06 08:45:00 +00:00 [+00:00]')
 sub Duration('PT128H45M') = ZonedDateTime('Fri 1969-12-26 15:15:00 +00:00 [+00:00]')
 add Duration('PT128H56M18S') = ZonedDateTime('Tue 1970-01-06 08:56:18 +00:00 [+00:00]')
 sub Duration('PT128H56M18S') = ZonedDateTime('Fri 1969-12-26 15:03:42 +00:00 [+00:00]')
ZonedDateTime('Thu 1970-01-01 00:02:03.000000456 +00:00 [+00:00]')
 add Duration('PT2M3S') = ZonedDateTime('Thu 1970-01-01 00:04:06.000000456 +00:00 [+00:00]')
 sub Duration('PT2M3S') = ZonedDateTime('Thu 1970-01-01 00:00:00.000000456 +00:00 [+00:00]')
 add Duration('PT2M3.456S') = ZonedDateTime('Thu 1970-01-01 00:04:06.456000456 +00:00 [+00:00]')
 sub Duration('PT2M3.456S') = ZonedDateTime('Wed 1969-12-31 23:59:59.544000456 +00:00 [+00:00]')
 add Duration('PT2M3.456789S') = ZonedDateTime('Thu 1970-01-01 00:04:06.456789456 +00:00 [+00:00]')
 sub Duration('PT2M3.456789S') = ZonedDateTime('Wed 1969-12-31 23:59:59.543211456 +00:00 [+00:00]')
 add Duration('PT2M3.456789876S') = ZonedDateTime('Thu 1970-01-01 00:04:06.456790332 +00:00 [+00:00]')
 sub Duration('PT2M3.456789876S') = ZonedDateTime('Wed 1969-12-31 23:59:59.54321058 +00:00 [+00:00]')
 add Duration('PT123H') = ZonedDateTime('Tue 1970-01-06 03:02:03.000000456 +00:00 [+00:00]')
 sub Duration('PT123H') = ZonedDateTime('Fri 1969-12-26 21:02:03.000000456 +00:00 [+00:00]')
 add Duration('PT128H45M') = ZonedDateTime('Tue 1970-01-06 08:47:03.000000456 +00:00 [+00:00]')
 sub Duration('PT128H45M') = ZonedDateTime('Fri 1969-12-26 15:17:03.000000456 +00:00 [+00:00]')
 add Duration('PT128H56M18S') = ZonedDateTime('Tue 1970-01-06 08:58:21.000000456 +00:00 [+00:00]')
 sub Duration('PT128H56M18S') = ZonedDateTime('Fri 1969-12-26 15:05:45.000000456 +00:00 [+00:00]')
ZonedDateTime('Wed 1969-12-31 23:57:57.000000456 +00:00 [+00:00]')
 add Duration('PT2M3S') = ZonedDateTime('Thu 1970-01-01 00:00:00.000000456 +00:00 [+00:00]')
 sub Duration('PT2M3S') = ZonedDateTime('Wed 1969-12-31 23:55:54.000000456 +00:00 [+00:00]')
 add Duration('PT2M3.456S') = ZonedDateTime('Thu 1970-01-01 00:00:00.456000456 +00:00 [+00:00]')
 sub Duration('PT2M3.456S') = ZonedDateTime('Wed 1969-12-31 23:55:53.544000456 +00:00 [+00:00]')
 add Duration('PT2M3.456789S') = ZonedDateTime('Thu 1970-01-01 00:00:00.456789456 +00:00 [+00:00]')
 sub Duration('PT2M3.456789S') = ZonedDateTime('Wed 1969-12-31 23:55:53.543211456 +00:00 [+00:00]')
 add Duration('PT2M3.456789876S') = ZonedDateTime('Thu 1970-01-01 00:00:00.456790332 +00:00 [+00:00]')
 sub Duration('PT2M3.456789876S') = ZonedDateTime('Wed 1969-12-31 23:55:53.54321058 +00:00 [+00:00]')
 add Duration('PT123H') = ZonedDateTime('Tue 1970-01-06 02:57:57.000000456 +00:00 [+00:00]')
 sub Duration('PT123H') = ZonedDateTime('Fri 1969-12-26 20:57:57.000000456 +00:00 [+00:00]')
 add Duration('PT128H45M') = ZonedDateTime('Tue 1970-01-06 08:42:57.000000456 +00:00 [+00:00]')
 sub Duration('PT128H45M') = ZonedDateTime('Fri 1969-12-26 15:12:57.000000456 +00:00 [+00:00]')
 add Duration('PT128H56M18S') = ZonedDateTime('Tue 1970-01-06 08:54:15.000000456 +00:00 [+00:00]')
 sub Duration('PT128H56M18S') = ZonedDateTime('Fri 1969-12-26 15:01:39.000000456 +00:00 [+00:00]')
ZonedDateTime('Sun 2000-03-26 01:59:59.987654321 +01:00 [Europe/Berlin]')
 add Duration('PT2M3S') = ZonedDateTime('Sun 2000-03-26 03:02:02.987654321 +02:00 [Europe/Berlin]')
 sub Duration('PT2M3S') = ZonedDateTime('Sun 2000-03-26 01:57:56.987654321 +01:00 [Europe/Berlin]')
 add Duration('PT2M3.456S') = ZonedDateTime('Sun 2000-03-26 03:02:03.443654321 +02:00 [Europe/Berlin]')
 sub Duration('PT2M3.456S') = ZonedDateTime('Sun 2000-03-26 01:57:56.531654321 +01:00 [Europe/Berlin]')
 add Duration('PT2M3.456789S') = ZonedDateTime('Sun 2000-03-26 03:02:03.444443321 +02:00 [Europe/Berlin]')
 sub Duration('PT2M3.456789S') = ZonedDateTime('Sun 2000-03-26 01:57:56.530865321 +01:00 [Europe/Berlin]')
 add Duration('PT2M3.456789876S') = ZonedDateTime('Sun 2000-03-26 03:02:03.444444197 +02:00 [Europe/Berlin]')
 sub Duration('PT2M3.456789876S') = ZonedDateTime('Sun 2000-03-26 01:57:56.530864445 +01:00 [Europe/Berlin]')
 add Duration('PT123H') = ZonedDateTime('Fri 2000-03-31 05:59:59.987654321 +02:00 [Europe/Berlin]')
 sub Duration('PT123H') = ZonedDateTime('Mon 2000-03-20 22:59:59.987654321 +01:00 [Europe/Berlin]')
 add Duration('PT128H45M') = ZonedDateTime('Fri 2000-03-31 11:44:59.987654321 +02:00 [Europe/Berlin]')
 sub Duration('PT128H45M') = ZonedDateTime('Mon 2000-03-20 17:14:59.987654321 +01:00 [Europe/Berlin]')
 add Duration('PT128H56M18S') = ZonedDateTime('Fri 2000-03-31 11:56:17.987654321 +02:00 [Europe/Berlin]')
 sub Duration('PT128H56M18S') = ZonedDateTime('Mon 2000-03-20 17:03:41.987654321 +01:00 [Europe/Berlin]')
