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
    new time\Period(years: 10),
    new time\Period(months: 123),
    new time\Period(weeks: 55),
    new time\Period(days: 500),
    new time\Period(years: 10, months: 123, weeks: 55, days: 500),
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
 add Period('P10Y') = Instant('Tue 1980-01-01 00:00:00', 315532800, 0)
 sub Period('P10Y') = Instant('Fri 1960-01-01 00:00:00', -315619200, 0)
 add Period('P123M') = Instant('Tue 1980-04-01 00:00:00', 323395200, 0)
 sub Period('P123M') = Instant('Thu 1959-10-01 00:00:00', -323568000, 0)
 add Period('P55W') = Instant('Thu 1971-01-21 00:00:00', 33264000, 0)
 sub Period('P55W') = Instant('Thu 1968-12-12 00:00:00', -33264000, 0)
 add Period('P500D') = Instant('Sun 1971-05-16 00:00:00', 43200000, 0)
 sub Period('P500D') = Instant('Mon 1968-08-19 00:00:00', -43200000, 0)
 add Period('P10Y123M55W500D') = Instant('Wed 1992-09-02 00:00:00', 715392000, 0)
 sub Period('P10Y123M55W500D') = Instant('Wed 1947-04-30 00:00:00', -715564800, 0)
Instant('Thu 1970-01-01 00:02:03.000000456', 123, 456)
 add Period('P10Y') = Instant('Tue 1980-01-01 00:02:03.000000456', 315532923, 456)
 sub Period('P10Y') = Instant('Fri 1960-01-01 00:02:03.000000456', -315619077, 456)
 add Period('P123M') = Instant('Tue 1980-04-01 00:02:03.000000456', 323395323, 456)
 sub Period('P123M') = Instant('Thu 1959-10-01 00:02:03.000000456', -323567877, 456)
 add Period('P55W') = Instant('Thu 1971-01-21 00:02:03.000000456', 33264123, 456)
 sub Period('P55W') = Instant('Thu 1968-12-12 00:02:03.000000456', -33263877, 456)
 add Period('P500D') = Instant('Sun 1971-05-16 00:02:03.000000456', 43200123, 456)
 sub Period('P500D') = Instant('Mon 1968-08-19 00:02:03.000000456', -43199877, 456)
 add Period('P10Y123M55W500D') = Instant('Wed 1992-09-02 00:02:03.000000456', 715392123, 456)
 sub Period('P10Y123M55W500D') = Instant('Wed 1947-04-30 00:02:03.000000456', -715564677, 456)
Instant('Wed 1969-12-31 23:57:57.000000456', -123, 456)
 add Period('P10Y') = Instant('Mon 1979-12-31 23:57:57.000000456', 315532677, 456)
 sub Period('P10Y') = Instant('Thu 1959-12-31 23:57:57.000000456', -315619323, 456)
 add Period('P123M') = Instant('Mon 1980-03-31 23:57:57.000000456', 323395077, 456)
 sub Period('P123M') = Instant('Thu 1959-10-01 23:57:57.000000456', -323481723, 456)
 add Period('P55W') = Instant('Wed 1971-01-20 23:57:57.000000456', 33263877, 456)
 sub Period('P55W') = Instant('Wed 1968-12-11 23:57:57.000000456', -33264123, 456)
 add Period('P500D') = Instant('Sat 1971-05-15 23:57:57.000000456', 43199877, 456)
 sub Period('P500D') = Instant('Sun 1968-08-18 23:57:57.000000456', -43200123, 456)
 add Period('P10Y123M55W500D') = Instant('Tue 1992-09-01 23:57:57.000000456', 715391877, 456)
 sub Period('P10Y123M55W500D') = Instant('Wed 1947-04-30 23:57:57.000000456', -715478523, 456)
