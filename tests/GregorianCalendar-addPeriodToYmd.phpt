--TEST--
GregorianCalendar::addPeriodToYmd()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\GregorianCalendar();

$ymdHisList = [
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

foreach ($ymdHisList as $ymdHis) {
    echo stringify($ymdHis) . "\n";
    foreach ($periods as $period) {
        echo " add " . stringify($period) . " = " . stringify($cal->addPeriodToYmd($period, ...$ymdHis)) . "\n";

        $invertedPeriod = $period->inverted();
        echo " add " . stringify($invertedPeriod) . " = " . stringify($cal->addPeriodToYmd($invertedPeriod, ...$ymdHis)) . "\n";
    }
}

--EXPECT--
[-1235, 1, 1]
 add Period('PT123S') = [-1235, 1, 1, 0, 2, 3, 0]
 add Period('-PT123S') = [-1236, 12, 31, 23, 57, 57, 0]
 add Period('PT123.456S') = [-1235, 1, 1, 0, 2, 3, 456000000]
 add Period('-PT123.456S') = [-1236, 12, 31, 23, 57, 56, 544000000]
 add Period('PT123.456789S') = [-1235, 1, 1, 0, 2, 3, 456789000]
 add Period('-PT123.456789S') = [-1236, 12, 31, 23, 57, 56, 543211000]
 add Period('PT123.456789876S') = [-1235, 1, 1, 0, 2, 3, 456789876]
 add Period('-PT123.456789876S') = [-1236, 12, 31, 23, 57, 56, 543210124]
 add Period('PT123H') = [-1235, 1, 6, 3, 0, 0, 0]
 add Period('-PT123H') = [-1236, 12, 26, 21, 0, 0, 0]
 add Period('PT123H345M') = [-1235, 1, 6, 8, 45, 0, 0]
 add Period('-PT123H345M') = [-1236, 12, 26, 15, 15, 0, 0]
 add Period('PT123H345M678S') = [-1235, 1, 6, 8, 56, 18, 0]
 add Period('-PT123H345M678S') = [-1236, 12, 26, 15, 3, 42, 0]
 add Period('P10Y') = [-1225, 1, 1, 0, 0, 0, 0]
 add Period('-P10Y') = [-1245, 1, 1, 0, 0, 0, 0]
 add Period('P123M') = [-1225, 4, 1, 0, 0, 0, 0]
 add Period('-P123M') = [-1246, 10, 1, 0, 0, 0, 0]
 add Period('P500D') = [-1234, 5, 16, 0, 0, 0, 0]
 add Period('-P500D') = [-1237, 8, 19, 0, 0, 0, 0]
 add Period('P55W') = [-1234, 1, 21, 0, 0, 0, 0]
 add Period('-P55W') = [-1237, 12, 12, 0, 0, 0, 0]
[-2, 12, 31, 23, 59, 59, 999999999]
 add Period('PT123S') = [-1, 1, 1, 0, 2, 2, 999999999]
 add Period('-PT123S') = [-2, 12, 31, 23, 57, 56, 999999999]
 add Period('PT123.456S') = [-1, 1, 1, 0, 2, 3, 455999999]
 add Period('-PT123.456S') = [-2, 12, 31, 23, 57, 56, 543999999]
 add Period('PT123.456789S') = [-1, 1, 1, 0, 2, 3, 456788999]
 add Period('-PT123.456789S') = [-2, 12, 31, 23, 57, 56, 543210999]
 add Period('PT123.456789876S') = [-1, 1, 1, 0, 2, 3, 456789875]
 add Period('-PT123.456789876S') = [-2, 12, 31, 23, 57, 56, 543210123]
 add Period('PT123H') = [-1, 1, 6, 2, 59, 59, 999999999]
 add Period('-PT123H') = [-2, 12, 26, 20, 59, 59, 999999999]
 add Period('PT123H345M') = [-1, 1, 6, 8, 44, 59, 999999999]
 add Period('-PT123H345M') = [-2, 12, 26, 15, 14, 59, 999999999]
 add Period('PT123H345M678S') = [-1, 1, 6, 8, 56, 17, 999999999]
 add Period('-PT123H345M678S') = [-2, 12, 26, 15, 3, 41, 999999999]
 add Period('P10Y') = [8, 12, 31, 23, 59, 59, 999999999]
 add Period('-P10Y') = [-12, 12, 31, 23, 59, 59, 999999999]
 add Period('P123M') = [9, 3, 31, 23, 59, 59, 999999999]
 add Period('-P123M') = [-12, 10, 1, 23, 59, 59, 999999999]
 add Period('P500D') = [-1, 5, 14, 23, 59, 59, 999999999]
 add Period('-P500D') = [-3, 8, 18, 23, 59, 59, 999999999]
 add Period('P55W') = [-1, 1, 19, 23, 59, 59, 999999999]
 add Period('-P55W') = [-3, 12, 11, 23, 59, 59, 999999999]
[-1, 1, 1]
 add Period('PT123S') = [-1, 1, 1, 0, 2, 3, 0]
 add Period('-PT123S') = [-2, 12, 31, 23, 57, 57, 0]
 add Period('PT123.456S') = [-1, 1, 1, 0, 2, 3, 456000000]
 add Period('-PT123.456S') = [-2, 12, 31, 23, 57, 56, 544000000]
 add Period('PT123.456789S') = [-1, 1, 1, 0, 2, 3, 456789000]
 add Period('-PT123.456789S') = [-2, 12, 31, 23, 57, 56, 543211000]
 add Period('PT123.456789876S') = [-1, 1, 1, 0, 2, 3, 456789876]
 add Period('-PT123.456789876S') = [-2, 12, 31, 23, 57, 56, 543210124]
 add Period('PT123H') = [-1, 1, 6, 3, 0, 0, 0]
 add Period('-PT123H') = [-2, 12, 26, 21, 0, 0, 0]
 add Period('PT123H345M') = [-1, 1, 6, 8, 45, 0, 0]
 add Period('-PT123H345M') = [-2, 12, 26, 15, 15, 0, 0]
 add Period('PT123H345M678S') = [-1, 1, 6, 8, 56, 18, 0]
 add Period('-PT123H345M678S') = [-2, 12, 26, 15, 3, 42, 0]
 add Period('P10Y') = [9, 1, 1, 0, 0, 0, 0]
 add Period('-P10Y') = [-11, 1, 1, 0, 0, 0, 0]
 add Period('P123M') = [9, 4, 1, 0, 0, 0, 0]
 add Period('-P123M') = [-12, 10, 1, 0, 0, 0, 0]
 add Period('P500D') = [-1, 5, 15, 0, 0, 0, 0]
 add Period('-P500D') = [-3, 8, 19, 0, 0, 0, 0]
 add Period('P55W') = [-1, 1, 20, 0, 0, 0, 0]
 add Period('-P55W') = [-3, 12, 12, 0, 0, 0, 0]
[1970, 1, 1]
 add Period('PT123S') = [1970, 1, 1, 0, 2, 3, 0]
 add Period('-PT123S') = [1969, 12, 31, 23, 57, 57, 0]
 add Period('PT123.456S') = [1970, 1, 1, 0, 2, 3, 456000000]
 add Period('-PT123.456S') = [1969, 12, 31, 23, 57, 56, 544000000]
 add Period('PT123.456789S') = [1970, 1, 1, 0, 2, 3, 456789000]
 add Period('-PT123.456789S') = [1969, 12, 31, 23, 57, 56, 543211000]
 add Period('PT123.456789876S') = [1970, 1, 1, 0, 2, 3, 456789876]
 add Period('-PT123.456789876S') = [1969, 12, 31, 23, 57, 56, 543210124]
 add Period('PT123H') = [1970, 1, 6, 3, 0, 0, 0]
 add Period('-PT123H') = [1969, 12, 26, 21, 0, 0, 0]
 add Period('PT123H345M') = [1970, 1, 6, 8, 45, 0, 0]
 add Period('-PT123H345M') = [1969, 12, 26, 15, 15, 0, 0]
 add Period('PT123H345M678S') = [1970, 1, 6, 8, 56, 18, 0]
 add Period('-PT123H345M678S') = [1969, 12, 26, 15, 3, 42, 0]
 add Period('P10Y') = [1980, 1, 1, 0, 0, 0, 0]
 add Period('-P10Y') = [1960, 1, 1, 0, 0, 0, 0]
 add Period('P123M') = [1980, 4, 1, 0, 0, 0, 0]
 add Period('-P123M') = [1959, 10, 1, 0, 0, 0, 0]
 add Period('P500D') = [1971, 5, 16, 0, 0, 0, 0]
 add Period('-P500D') = [1968, 8, 19, 0, 0, 0, 0]
 add Period('P55W') = [1971, 1, 21, 0, 0, 0, 0]
 add Period('-P55W') = [1968, 12, 12, 0, 0, 0, 0]
[1970, 1, 1, 0, 2, 3, 456]
 add Period('PT123S') = [1970, 1, 1, 0, 4, 6, 456]
 add Period('-PT123S') = [1970, 1, 1, 0, 0, 0, 456]
 add Period('PT123.456S') = [1970, 1, 1, 0, 4, 6, 456000456]
 add Period('-PT123.456S') = [1969, 12, 31, 23, 59, 59, 544000456]
 add Period('PT123.456789S') = [1970, 1, 1, 0, 4, 6, 456789456]
 add Period('-PT123.456789S') = [1969, 12, 31, 23, 59, 59, 543211456]
 add Period('PT123.456789876S') = [1970, 1, 1, 0, 4, 6, 456790332]
 add Period('-PT123.456789876S') = [1969, 12, 31, 23, 59, 59, 543210580]
 add Period('PT123H') = [1970, 1, 6, 3, 2, 3, 456]
 add Period('-PT123H') = [1969, 12, 26, 21, 2, 3, 456]
 add Period('PT123H345M') = [1970, 1, 6, 8, 47, 3, 456]
 add Period('-PT123H345M') = [1969, 12, 26, 15, 17, 3, 456]
 add Period('PT123H345M678S') = [1970, 1, 6, 8, 58, 21, 456]
 add Period('-PT123H345M678S') = [1969, 12, 26, 15, 5, 45, 456]
 add Period('P10Y') = [1980, 1, 1, 0, 2, 3, 456]
 add Period('-P10Y') = [1960, 1, 1, 0, 2, 3, 456]
 add Period('P123M') = [1980, 4, 1, 0, 2, 3, 456]
 add Period('-P123M') = [1959, 10, 1, 0, 2, 3, 456]
 add Period('P500D') = [1971, 5, 16, 0, 2, 3, 456]
 add Period('-P500D') = [1968, 8, 19, 0, 2, 3, 456]
 add Period('P55W') = [1971, 1, 21, 0, 2, 3, 456]
 add Period('-P55W') = [1968, 12, 12, 0, 2, 3, 456]
[1969, 12, 31, 23, 57, 57, 456]
 add Period('PT123S') = [1970, 1, 1, 0, 0, 0, 456]
 add Period('-PT123S') = [1969, 12, 31, 23, 55, 54, 456]
 add Period('PT123.456S') = [1970, 1, 1, 0, 0, 0, 456000456]
 add Period('-PT123.456S') = [1969, 12, 31, 23, 55, 53, 544000456]
 add Period('PT123.456789S') = [1970, 1, 1, 0, 0, 0, 456789456]
 add Period('-PT123.456789S') = [1969, 12, 31, 23, 55, 53, 543211456]
 add Period('PT123.456789876S') = [1970, 1, 1, 0, 0, 0, 456790332]
 add Period('-PT123.456789876S') = [1969, 12, 31, 23, 55, 53, 543210580]
 add Period('PT123H') = [1970, 1, 6, 2, 57, 57, 456]
 add Period('-PT123H') = [1969, 12, 26, 20, 57, 57, 456]
 add Period('PT123H345M') = [1970, 1, 6, 8, 42, 57, 456]
 add Period('-PT123H345M') = [1969, 12, 26, 15, 12, 57, 456]
 add Period('PT123H345M678S') = [1970, 1, 6, 8, 54, 15, 456]
 add Period('-PT123H345M678S') = [1969, 12, 26, 15, 1, 39, 456]
 add Period('P10Y') = [1979, 12, 31, 23, 57, 57, 456]
 add Period('-P10Y') = [1959, 12, 31, 23, 57, 57, 456]
 add Period('P123M') = [1980, 3, 31, 23, 57, 57, 456]
 add Period('-P123M') = [1959, 10, 1, 23, 57, 57, 456]
 add Period('P500D') = [1971, 5, 15, 23, 57, 57, 456]
 add Period('-P500D') = [1968, 8, 18, 23, 57, 57, 456]
 add Period('P55W') = [1971, 1, 20, 23, 57, 57, 456]
 add Period('-P55W') = [1968, 12, 11, 23, 57, 57, 456]
