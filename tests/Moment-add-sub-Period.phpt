--TEST--
Instant->add() and Instant->sub() with Period
--FILE--
<?php

include __DIR__ . '/include.php';

$instants = [
    time\Instant::fromUnixTimestampTuple([0, 0]),
    time\Instant::fromUnixTimestampTuple([123, 456]),
    time\Instant::fromUnixTimestampTuple([-123, 456]),
];

$periods = [
    new time\Period(seconds: 123),
    new time\Period(seconds: 123, milliseconds: 456),
    new time\Period(seconds: 123, milliseconds: 456, microseconds: 789),
    new time\Period(seconds: 123, milliseconds: 456, microseconds: 789, nanoseconds: 876),
    new time\Period(hours: 123),
    new time\Period(hours: 123, minutes: 345),
    new time\Period(hours: 123, minutes: 345, seconds: 678),
    new time\Period(years: 10),
    new time\Period(months: 123),
    new time\Period(days: 500),
    new time\Period(weeks: 55),
];

foreach ($instants as $instant) {
    echo stringify($instant) . "\n";
    foreach ($periods as $period) {
        echo " add " . stringify($period) . " = " . stringify($instant->add($period)) . "\n";
        echo " sub " . stringify($period) . " = " . stringify($instant->sub($period)) . "\n";
    }
}

--EXPECT--
Instant('Thu 1970-01-01 00:00:00', 0, 0)
 add Period('PT123S') = Instant('Thu 1970-01-01 00:02:03', 123, 0)
 sub Period('PT123S') = Instant('Wed 1969-12-31 23:57:57', -123, 0)
 add Period('PT123.456S') = Instant('Thu 1970-01-01 00:02:03.456', 123, 456000000)
 sub Period('PT123.456S') = Instant('Wed 1969-12-31 23:57:56.544', -124, 544000000)
 add Period('PT123.456789S') = Instant('Thu 1970-01-01 00:02:03.456789', 123, 456789000)
 sub Period('PT123.456789S') = Instant('Wed 1969-12-31 23:57:56.543211', -124, 543211000)
 add Period('PT123.456789876S') = Instant('Thu 1970-01-01 00:02:03.456789876', 123, 456789876)
 sub Period('PT123.456789876S') = Instant('Wed 1969-12-31 23:57:56.543210124', -124, 543210124)
 add Period('PT123H') = Instant('Tue 1970-01-06 03:00:00', 442800, 0)
 sub Period('PT123H') = Instant('Fri 1969-12-26 21:00:00', -442800, 0)
 add Period('PT123H345M') = Instant('Tue 1970-01-06 08:45:00', 463500, 0)
 sub Period('PT123H345M') = Instant('Fri 1969-12-26 15:15:00', -463500, 0)
 add Period('PT123H345M678S') = Instant('Tue 1970-01-06 08:56:18', 464178, 0)
 sub Period('PT123H345M678S') = Instant('Fri 1969-12-26 15:03:42', -464178, 0)
 add Period('P10Y') = Instant('Tue 1980-01-01 00:00:00', 315532800, 0)
 sub Period('P10Y') = Instant('Fri 1960-01-01 00:00:00', -315619200, 0)
 add Period('P123M') = Instant('Tue 1980-04-01 00:00:00', 323395200, 0)
 sub Period('P123M') = Instant('Thu 1959-10-01 00:00:00', -323568000, 0)
 add Period('P500D') = Instant('Sun 1971-05-16 00:00:00', 43200000, 0)
 sub Period('P500D') = Instant('Mon 1968-08-19 00:00:00', -43200000, 0)
 add Period('P55W') = Instant('Thu 1971-01-21 00:00:00', 33264000, 0)
 sub Period('P55W') = Instant('Thu 1968-12-12 00:00:00', -33264000, 0)
Instant('Thu 1970-01-01 00:02:03.000000456', 123, 456)
 add Period('PT123S') = Instant('Thu 1970-01-01 00:04:06.000000456', 246, 456)
 sub Period('PT123S') = Instant('Thu 1970-01-01 00:00:00.000000456', 0, 456)
 add Period('PT123.456S') = Instant('Thu 1970-01-01 00:04:06.456000456', 246, 456000456)
 sub Period('PT123.456S') = Instant('Wed 1969-12-31 23:59:59.544000456', -1, 544000456)
 add Period('PT123.456789S') = Instant('Thu 1970-01-01 00:04:06.456789456', 246, 456789456)
 sub Period('PT123.456789S') = Instant('Wed 1969-12-31 23:59:59.543211456', -1, 543211456)
 add Period('PT123.456789876S') = Instant('Thu 1970-01-01 00:04:06.456790332', 246, 456790332)
 sub Period('PT123.456789876S') = Instant('Wed 1969-12-31 23:59:59.54321058', -1, 543210580)
 add Period('PT123H') = Instant('Tue 1970-01-06 03:02:03.000000456', 442923, 456)
 sub Period('PT123H') = Instant('Fri 1969-12-26 21:02:03.000000456', -442677, 456)
 add Period('PT123H345M') = Instant('Tue 1970-01-06 08:47:03.000000456', 463623, 456)
 sub Period('PT123H345M') = Instant('Fri 1969-12-26 15:17:03.000000456', -463377, 456)
 add Period('PT123H345M678S') = Instant('Tue 1970-01-06 08:58:21.000000456', 464301, 456)
 sub Period('PT123H345M678S') = Instant('Fri 1969-12-26 15:05:45.000000456', -464055, 456)
 add Period('P10Y') = Instant('Tue 1980-01-01 00:02:03.000000456', 315532923, 456)
 sub Period('P10Y') = Instant('Fri 1960-01-01 00:02:03.000000456', -315619077, 456)
 add Period('P123M') = Instant('Tue 1980-04-01 00:02:03.000000456', 323395323, 456)
 sub Period('P123M') = Instant('Thu 1959-10-01 00:02:03.000000456', -323567877, 456)
 add Period('P500D') = Instant('Sun 1971-05-16 00:02:03.000000456', 43200123, 456)
 sub Period('P500D') = Instant('Mon 1968-08-19 00:02:03.000000456', -43199877, 456)
 add Period('P55W') = Instant('Thu 1971-01-21 00:02:03.000000456', 33264123, 456)
 sub Period('P55W') = Instant('Thu 1968-12-12 00:02:03.000000456', -33263877, 456)
Instant('Wed 1969-12-31 23:57:57.000000456', -123, 456)
 add Period('PT123S') = Instant('Thu 1970-01-01 00:00:00.000000456', 0, 456)
 sub Period('PT123S') = Instant('Wed 1969-12-31 23:55:54.000000456', -246, 456)
 add Period('PT123.456S') = Instant('Thu 1970-01-01 00:00:00.456000456', 0, 456000456)
 sub Period('PT123.456S') = Instant('Wed 1969-12-31 23:55:53.544000456', -247, 544000456)
 add Period('PT123.456789S') = Instant('Thu 1970-01-01 00:00:00.456789456', 0, 456789456)
 sub Period('PT123.456789S') = Instant('Wed 1969-12-31 23:55:53.543211456', -247, 543211456)
 add Period('PT123.456789876S') = Instant('Thu 1970-01-01 00:00:00.456790332', 0, 456790332)
 sub Period('PT123.456789876S') = Instant('Wed 1969-12-31 23:55:53.54321058', -247, 543210580)
 add Period('PT123H') = Instant('Tue 1970-01-06 02:57:57.000000456', 442677, 456)
 sub Period('PT123H') = Instant('Fri 1969-12-26 20:57:57.000000456', -442923, 456)
 add Period('PT123H345M') = Instant('Tue 1970-01-06 08:42:57.000000456', 463377, 456)
 sub Period('PT123H345M') = Instant('Fri 1969-12-26 15:12:57.000000456', -463623, 456)
 add Period('PT123H345M678S') = Instant('Tue 1970-01-06 08:54:15.000000456', 464055, 456)
 sub Period('PT123H345M678S') = Instant('Fri 1969-12-26 15:01:39.000000456', -464301, 456)
 add Period('P10Y') = Instant('Mon 1979-12-31 23:57:57.000000456', 315532677, 456)
 sub Period('P10Y') = Instant('Thu 1959-12-31 23:57:57.000000456', -315619323, 456)
 add Period('P123M') = Instant('Mon 1980-03-31 23:57:57.000000456', 323395077, 456)
 sub Period('P123M') = Instant('Thu 1959-10-01 23:57:57.000000456', -323481723, 456)
 add Period('P500D') = Instant('Sat 1971-05-15 23:57:57.000000456', 43199877, 456)
 sub Period('P500D') = Instant('Sun 1968-08-18 23:57:57.000000456', -43200123, 456)
 add Period('P55W') = Instant('Wed 1971-01-20 23:57:57.000000456', 33263877, 456)
 sub Period('P55W') = Instant('Wed 1968-12-11 23:57:57.000000456', -33264123, 456)
