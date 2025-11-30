--TEST--
IsoCalendar::addPeriodToYmd()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = time\IsoCalendar::getInstance();

$ymdList = [
    [-1234, 1, 1],
    [-1, 12, 31],
    [0, 1, 1],
    [1970, 1, 1],
    [1970, 1, 1],
    [1969, 12, 31],
];

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

foreach ($ymdList as $ymd) {
    echo stringify($ymd) . "\n";
    foreach ($periods as $period) {
        echo " add " . stringify($period) . " = " . stringify($cal->addPeriodToYmd($period, ...$ymd)) . "\n";

        $invertedPeriod = $period->inverted();
        echo " add " . stringify($invertedPeriod) . " = " . stringify($cal->addPeriodToYmd($invertedPeriod, ...$ymd)) . "\n";
    }
}

--EXPECT--
[-1234, 1, 1]
 add Period('P10Y') = [-1224, 1, 1]
 add Period('-P10Y') = [-1244, 1, 1]
 add Period('P123M') = [-1224, 4, 1]
 add Period('-P123M') = [-1245, 10, 1]
 add Period('P55W') = [-1233, 1, 21]
 add Period('-P55W') = [-1236, 12, 12]
 add Period('P500D') = [-1233, 5, 16]
 add Period('-P500D') = [-1236, 8, 19]
 add Period('P10Y123M55W500D') = [-1212, 9, 2]
 add Period('-P10Y123M55W500D') = [-1257, 4, 30]
[-1, 12, 31]
 add Period('P10Y') = [9, 12, 31]
 add Period('-P10Y') = [-11, 12, 31]
 add Period('P123M') = [10, 3, 31]
 add Period('-P123M') = [-11, 10, 1]
 add Period('P55W') = [1, 1, 19]
 add Period('-P55W') = [-2, 12, 11]
 add Period('P500D') = [1, 5, 14]
 add Period('-P500D') = [-2, 8, 18]
 add Period('P10Y123M55W500D') = [22, 9, 2]
 add Period('-P10Y123M55W500D') = [-23, 4, 29]
[0, 1, 1]
 add Period('P10Y') = [10, 1, 1]
 add Period('-P10Y') = [-10, 1, 1]
 add Period('P123M') = [10, 4, 1]
 add Period('-P123M') = [-11, 10, 1]
 add Period('P55W') = [1, 1, 20]
 add Period('-P55W') = [-2, 12, 12]
 add Period('P500D') = [1, 5, 15]
 add Period('-P500D') = [-2, 8, 19]
 add Period('P10Y123M55W500D') = [22, 9, 3]
 add Period('-P10Y123M55W500D') = [-23, 4, 29]
[1970, 1, 1]
 add Period('P10Y') = [1980, 1, 1]
 add Period('-P10Y') = [1960, 1, 1]
 add Period('P123M') = [1980, 4, 1]
 add Period('-P123M') = [1959, 10, 1]
 add Period('P55W') = [1971, 1, 21]
 add Period('-P55W') = [1968, 12, 12]
 add Period('P500D') = [1971, 5, 16]
 add Period('-P500D') = [1968, 8, 19]
 add Period('P10Y123M55W500D') = [1992, 9, 2]
 add Period('-P10Y123M55W500D') = [1947, 4, 30]
[1970, 1, 1]
 add Period('P10Y') = [1980, 1, 1]
 add Period('-P10Y') = [1960, 1, 1]
 add Period('P123M') = [1980, 4, 1]
 add Period('-P123M') = [1959, 10, 1]
 add Period('P55W') = [1971, 1, 21]
 add Period('-P55W') = [1968, 12, 12]
 add Period('P500D') = [1971, 5, 16]
 add Period('-P500D') = [1968, 8, 19]
 add Period('P10Y123M55W500D') = [1992, 9, 2]
 add Period('-P10Y123M55W500D') = [1947, 4, 30]
[1969, 12, 31]
 add Period('P10Y') = [1979, 12, 31]
 add Period('-P10Y') = [1959, 12, 31]
 add Period('P123M') = [1980, 3, 31]
 add Period('-P123M') = [1959, 10, 1]
 add Period('P55W') = [1971, 1, 20]
 add Period('-P55W') = [1968, 12, 11]
 add Period('P500D') = [1971, 5, 15]
 add Period('-P500D') = [1968, 8, 18]
 add Period('P10Y123M55W500D') = [1992, 9, 1]
 add Period('-P10Y123M55W500D') = [1947, 4, 30]
