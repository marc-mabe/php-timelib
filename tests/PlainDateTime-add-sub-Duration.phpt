--TEST--
Instant->add() and Instant->sub() with Duration
--FILE--
<?php

include __DIR__ . '/include.php';

$plainDateTimes = [
    time\PlainDateTime::fromYmd(1970, 1, 1),
    time\PlainDateTime::fromYmd(1970, 1, 1, 0, 2, 3, 456),
    time\PlainDateTime::fromYmd(1969, 12, 31, 23, 57, 57, 456),
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

foreach ($plainDateTimes as $plainDateTime) {
    echo stringify($plainDateTime) . "\n";
    foreach ($durations as $duration) {
        echo " add " . stringify($duration) . " = " . stringify($plainDateTime->add($duration)) . "\n";
        echo " sub " . stringify($duration) . " = " . stringify($plainDateTime->sub($duration)) . "\n";
    }
}

--EXPECT--
PlainDateTime('Thu 1970-01-01 00:00:00')
 add Duration('PT2M3S') = PlainDateTime('Thu 1970-01-01 00:02:03')
 sub Duration('PT2M3S') = PlainDateTime('Wed 1969-12-31 23:57:57')
 add Duration('PT2M3.456S') = PlainDateTime('Thu 1970-01-01 00:02:03.456')
 sub Duration('PT2M3.456S') = PlainDateTime('Wed 1969-12-31 23:57:56.544')
 add Duration('PT2M3.456789S') = PlainDateTime('Thu 1970-01-01 00:02:03.456789')
 sub Duration('PT2M3.456789S') = PlainDateTime('Wed 1969-12-31 23:57:56.543211')
 add Duration('PT2M3.456789876S') = PlainDateTime('Thu 1970-01-01 00:02:03.456789876')
 sub Duration('PT2M3.456789876S') = PlainDateTime('Wed 1969-12-31 23:57:56.543210124')
 add Duration('PT123H') = PlainDateTime('Tue 1970-01-06 03:00:00')
 sub Duration('PT123H') = PlainDateTime('Fri 1969-12-26 21:00:00')
 add Duration('PT128H45M') = PlainDateTime('Tue 1970-01-06 08:45:00')
 sub Duration('PT128H45M') = PlainDateTime('Fri 1969-12-26 15:15:00')
 add Duration('PT128H56M18S') = PlainDateTime('Tue 1970-01-06 08:56:18')
 sub Duration('PT128H56M18S') = PlainDateTime('Fri 1969-12-26 15:03:42')
PlainDateTime('Thu 1970-01-01 00:02:03.000000456')
 add Duration('PT2M3S') = PlainDateTime('Thu 1970-01-01 00:04:06.000000456')
 sub Duration('PT2M3S') = PlainDateTime('Thu 1970-01-01 00:00:00.000000456')
 add Duration('PT2M3.456S') = PlainDateTime('Thu 1970-01-01 00:04:06.456000456')
 sub Duration('PT2M3.456S') = PlainDateTime('Wed 1969-12-31 23:59:59.544000456')
 add Duration('PT2M3.456789S') = PlainDateTime('Thu 1970-01-01 00:04:06.456789456')
 sub Duration('PT2M3.456789S') = PlainDateTime('Wed 1969-12-31 23:59:59.543211456')
 add Duration('PT2M3.456789876S') = PlainDateTime('Thu 1970-01-01 00:04:06.456790332')
 sub Duration('PT2M3.456789876S') = PlainDateTime('Wed 1969-12-31 23:59:59.54321058')
 add Duration('PT123H') = PlainDateTime('Tue 1970-01-06 03:02:03.000000456')
 sub Duration('PT123H') = PlainDateTime('Fri 1969-12-26 21:02:03.000000456')
 add Duration('PT128H45M') = PlainDateTime('Tue 1970-01-06 08:47:03.000000456')
 sub Duration('PT128H45M') = PlainDateTime('Fri 1969-12-26 15:17:03.000000456')
 add Duration('PT128H56M18S') = PlainDateTime('Tue 1970-01-06 08:58:21.000000456')
 sub Duration('PT128H56M18S') = PlainDateTime('Fri 1969-12-26 15:05:45.000000456')
PlainDateTime('Wed 1969-12-31 23:57:57.000000456')
 add Duration('PT2M3S') = PlainDateTime('Thu 1970-01-01 00:00:00.000000456')
 sub Duration('PT2M3S') = PlainDateTime('Wed 1969-12-31 23:55:54.000000456')
 add Duration('PT2M3.456S') = PlainDateTime('Thu 1970-01-01 00:00:00.456000456')
 sub Duration('PT2M3.456S') = PlainDateTime('Wed 1969-12-31 23:55:53.544000456')
 add Duration('PT2M3.456789S') = PlainDateTime('Thu 1970-01-01 00:00:00.456789456')
 sub Duration('PT2M3.456789S') = PlainDateTime('Wed 1969-12-31 23:55:53.543211456')
 add Duration('PT2M3.456789876S') = PlainDateTime('Thu 1970-01-01 00:00:00.456790332')
 sub Duration('PT2M3.456789876S') = PlainDateTime('Wed 1969-12-31 23:55:53.54321058')
 add Duration('PT123H') = PlainDateTime('Tue 1970-01-06 02:57:57.000000456')
 sub Duration('PT123H') = PlainDateTime('Fri 1969-12-26 20:57:57.000000456')
 add Duration('PT128H45M') = PlainDateTime('Tue 1970-01-06 08:42:57.000000456')
 sub Duration('PT128H45M') = PlainDateTime('Fri 1969-12-26 15:12:57.000000456')
 add Duration('PT128H56M18S') = PlainDateTime('Tue 1970-01-06 08:54:15.000000456')
 sub Duration('PT128H56M18S') = PlainDateTime('Fri 1969-12-26 15:01:39.000000456')
