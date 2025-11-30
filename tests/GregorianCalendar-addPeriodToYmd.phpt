--TEST--
GregorianCalendar::addPeriodToYmd()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\GregorianCalendar();

$ymdList = [
    [-1235, 1, 1],
    [-2, 12, 31, 23, 59, 59, 999999999],
    [-1, 1, 1],
    [1970, 1, 1],
    [1970, 1, 1, 0, 2, 3, 456],
    [1969, 12, 31, 23, 57, 57, 456],
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
    new time\Period(years: 10, months: 123, days: 500, weeks: 55),
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
[-1235, 1, 1]
 add Period('P10Y') = [-1225, 1, 1]
 add Period('-P10Y') = [-1245, 1, 1]
 add Period('P123M') = [-1225, 4, 1]
 add Period('-P123M') = [-1246, 10, 1]
 add Period('P55W') = [-1234, 1, 21]
 add Period('-P55W') = [-1237, 12, 12]
 add Period('P500D') = [-1234, 5, 16]
 add Period('-P500D') = [-1237, 8, 19]
 add Period('P10Y123M55W500D') = [-1213, 9, 2]
 add Period('-P10Y123M55W500D') = [-1258, 4, 30]
[-2, 12, 31, 23, 59, 59, 999999999]
 add Period('P10Y') = [8, 12, 31]
 add Period('-P10Y') = [-12, 12, 31]
 add Period('P123M') = [9, 3, 31]
 add Period('-P123M') = [-12, 10, 1]
 add Period('P55W') = [-1, 1, 19]
 add Period('-P55W') = [-3, 12, 11]
 add Period('P500D') = [-1, 5, 14]
 add Period('-P500D') = [-3, 8, 18]
 add Period('P10Y123M55W500D') = [21, 9, 2]
 add Period('-P10Y123M55W500D') = [-24, 4, 29]
[-1, 1, 1]
 add Period('P10Y') = [9, 1, 1]
 add Period('-P10Y') = [-11, 1, 1]
 add Period('P123M') = [9, 4, 1]
 add Period('-P123M') = [-12, 10, 1]
 add Period('P55W') = [-1, 1, 20]
 add Period('-P55W') = [-3, 12, 12]
 add Period('P500D') = [-1, 5, 15]
 add Period('-P500D') = [-3, 8, 19]
 add Period('P10Y123M55W500D') = [21, 9, 3]
 add Period('-P10Y123M55W500D') = [-24, 4, 29]
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
[1970, 1, 1, 0, 2, 3, 456]
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
[1969, 12, 31, 23, 57, 57, 456]
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
