--TEST--
Instant->add() and Instant->sub() with Duration
--FILE--
<?php

include __DIR__ . '/include.php';

$instants = [
    time\Instant::fromUnixTimestampTuple([0, 0]),
    time\Instant::fromUnixTimestampTuple([123, 456]),
    time\Instant::fromUnixTimestampTuple([-123, 456]),
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

foreach ($instants as $instant) {
    echo stringify($instant) . "\n";
    foreach ($durations as $duration) {
        echo " add " . stringify($duration) . " = " . stringify($instant->add($duration)) . "\n";
        echo " sub " . stringify($duration) . " = " . stringify($instant->sub($duration)) . "\n";
    }
}

--EXPECT--
Instant('Thu 1970-01-01 00:00:00', 0, 0)
 add Duration('PT2M3S') = Instant('Thu 1970-01-01 00:02:03', 123, 0)
 sub Duration('PT2M3S') = Instant('Wed 1969-12-31 23:57:57', -123, 0)
 add Duration('PT2M3.456S') = Instant('Thu 1970-01-01 00:02:03.456', 123, 456000000)
 sub Duration('PT2M3.456S') = Instant('Wed 1969-12-31 23:57:56.544', -124, 544000000)
 add Duration('PT2M3.456789S') = Instant('Thu 1970-01-01 00:02:03.456789', 123, 456789000)
 sub Duration('PT2M3.456789S') = Instant('Wed 1969-12-31 23:57:56.543211', -124, 543211000)
 add Duration('PT2M3.456789876S') = Instant('Thu 1970-01-01 00:02:03.456789876', 123, 456789876)
 sub Duration('PT2M3.456789876S') = Instant('Wed 1969-12-31 23:57:56.543210124', -124, 543210124)
 add Duration('PT123H') = Instant('Tue 1970-01-06 03:00:00', 442800, 0)
 sub Duration('PT123H') = Instant('Fri 1969-12-26 21:00:00', -442800, 0)
 add Duration('PT128H45M') = Instant('Tue 1970-01-06 08:45:00', 463500, 0)
 sub Duration('PT128H45M') = Instant('Fri 1969-12-26 15:15:00', -463500, 0)
 add Duration('PT128H56M18S') = Instant('Tue 1970-01-06 08:56:18', 464178, 0)
 sub Duration('PT128H56M18S') = Instant('Fri 1969-12-26 15:03:42', -464178, 0)
Instant('Thu 1970-01-01 00:02:03.000000456', 123, 456)
 add Duration('PT2M3S') = Instant('Thu 1970-01-01 00:04:06.000000456', 246, 456)
 sub Duration('PT2M3S') = Instant('Thu 1970-01-01 00:00:00.000000456', 0, 456)
 add Duration('PT2M3.456S') = Instant('Thu 1970-01-01 00:04:06.456000456', 246, 456000456)
 sub Duration('PT2M3.456S') = Instant('Wed 1969-12-31 23:59:59.544000456', -1, 544000456)
 add Duration('PT2M3.456789S') = Instant('Thu 1970-01-01 00:04:06.456789456', 246, 456789456)
 sub Duration('PT2M3.456789S') = Instant('Wed 1969-12-31 23:59:59.543211456', -1, 543211456)
 add Duration('PT2M3.456789876S') = Instant('Thu 1970-01-01 00:04:06.456790332', 246, 456790332)
 sub Duration('PT2M3.456789876S') = Instant('Wed 1969-12-31 23:59:59.54321058', -1, 543210580)
 add Duration('PT123H') = Instant('Tue 1970-01-06 03:02:03.000000456', 442923, 456)
 sub Duration('PT123H') = Instant('Fri 1969-12-26 21:02:03.000000456', -442677, 456)
 add Duration('PT128H45M') = Instant('Tue 1970-01-06 08:47:03.000000456', 463623, 456)
 sub Duration('PT128H45M') = Instant('Fri 1969-12-26 15:17:03.000000456', -463377, 456)
 add Duration('PT128H56M18S') = Instant('Tue 1970-01-06 08:58:21.000000456', 464301, 456)
 sub Duration('PT128H56M18S') = Instant('Fri 1969-12-26 15:05:45.000000456', -464055, 456)
Instant('Wed 1969-12-31 23:57:57.000000456', -123, 456)
 add Duration('PT2M3S') = Instant('Thu 1970-01-01 00:00:00.000000456', 0, 456)
 sub Duration('PT2M3S') = Instant('Wed 1969-12-31 23:55:54.000000456', -246, 456)
 add Duration('PT2M3.456S') = Instant('Thu 1970-01-01 00:00:00.456000456', 0, 456000456)
 sub Duration('PT2M3.456S') = Instant('Wed 1969-12-31 23:55:53.544000456', -247, 544000456)
 add Duration('PT2M3.456789S') = Instant('Thu 1970-01-01 00:00:00.456789456', 0, 456789456)
 sub Duration('PT2M3.456789S') = Instant('Wed 1969-12-31 23:55:53.543211456', -247, 543211456)
 add Duration('PT2M3.456789876S') = Instant('Thu 1970-01-01 00:00:00.456790332', 0, 456790332)
 sub Duration('PT2M3.456789876S') = Instant('Wed 1969-12-31 23:55:53.54321058', -247, 543210580)
 add Duration('PT123H') = Instant('Tue 1970-01-06 02:57:57.000000456', 442677, 456)
 sub Duration('PT123H') = Instant('Fri 1969-12-26 20:57:57.000000456', -442923, 456)
 add Duration('PT128H45M') = Instant('Tue 1970-01-06 08:42:57.000000456', 463377, 456)
 sub Duration('PT128H45M') = Instant('Fri 1969-12-26 15:12:57.000000456', -463623, 456)
 add Duration('PT128H56M18S') = Instant('Tue 1970-01-06 08:54:15.000000456', 464055, 456)
 sub Duration('PT128H56M18S') = Instant('Fri 1969-12-26 15:01:39.000000456', -464301, 456)
