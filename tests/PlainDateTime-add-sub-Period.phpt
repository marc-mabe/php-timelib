--TEST--
PlainDateTime->add() and PlainDateTime->sub() with Period
--FILE--
<?php

include __DIR__ . '/include.php';

$plainDateTimes = [
    time\PlainDateTime::fromYmd(1970, 1, 1),
    time\PlainDateTime::fromYmd(1970, 1, 1, 0, 2, 3, 456),
    time\PlainDateTime::fromYmd(1969, 12, 31, 23, 57, 57, 456),
];

$periods = [
    new time\Period(years: 10),
    new time\Period(months: 123),
    new time\Period(weeks: 55),
    new time\Period(days: 500),
    new time\Period(years: 10, months: 123, weeks: 55, days: 500),
];

foreach ($plainDateTimes as $plainDateTime) {
    echo stringify($plainDateTime) . "\n";
    foreach ($periods as $period) {
        echo " add " . stringify($period) . " = " . stringify($plainDateTime->add($period)) . "\n";
        echo " sub " . stringify($period) . " = " . stringify($plainDateTime->sub($period)) . "\n";
    }
}

--EXPECT--
PlainDateTime('Thu 1970-01-01 00:00:00')
 add Period('P10Y') = PlainDateTime('Tue 1980-01-01 00:00:00')
 sub Period('P10Y') = PlainDateTime('Fri 1960-01-01 00:00:00')
 add Period('P123M') = PlainDateTime('Tue 1980-04-01 00:00:00')
 sub Period('P123M') = PlainDateTime('Thu 1959-10-01 00:00:00')
 add Period('P55W') = PlainDateTime('Thu 1971-01-21 00:00:00')
 sub Period('P55W') = PlainDateTime('Thu 1968-12-12 00:00:00')
 add Period('P500D') = PlainDateTime('Sun 1971-05-16 00:00:00')
 sub Period('P500D') = PlainDateTime('Mon 1968-08-19 00:00:00')
 add Period('P10Y123M55W500D') = PlainDateTime('Wed 1992-09-02 00:00:00')
 sub Period('P10Y123M55W500D') = PlainDateTime('Wed 1947-04-30 00:00:00')
PlainDateTime('Thu 1970-01-01 00:02:03.000000456')
 add Period('P10Y') = PlainDateTime('Tue 1980-01-01 00:00:00')
 sub Period('P10Y') = PlainDateTime('Fri 1960-01-01 00:00:00')
 add Period('P123M') = PlainDateTime('Tue 1980-04-01 00:00:00')
 sub Period('P123M') = PlainDateTime('Thu 1959-10-01 00:00:00')
 add Period('P55W') = PlainDateTime('Thu 1971-01-21 00:00:00')
 sub Period('P55W') = PlainDateTime('Thu 1968-12-12 00:00:00')
 add Period('P500D') = PlainDateTime('Sun 1971-05-16 00:00:00')
 sub Period('P500D') = PlainDateTime('Mon 1968-08-19 00:00:00')
 add Period('P10Y123M55W500D') = PlainDateTime('Wed 1992-09-02 00:00:00')
 sub Period('P10Y123M55W500D') = PlainDateTime('Wed 1947-04-30 00:00:00')
PlainDateTime('Wed 1969-12-31 23:57:57.000000456')
 add Period('P10Y') = PlainDateTime('Mon 1979-12-31 00:00:00')
 sub Period('P10Y') = PlainDateTime('Thu 1959-12-31 00:00:00')
 add Period('P123M') = PlainDateTime('Mon 1980-03-31 00:00:00')
 sub Period('P123M') = PlainDateTime('Thu 1959-10-01 00:00:00')
 add Period('P55W') = PlainDateTime('Wed 1971-01-20 00:00:00')
 sub Period('P55W') = PlainDateTime('Wed 1968-12-11 00:00:00')
 add Period('P500D') = PlainDateTime('Sat 1971-05-15 00:00:00')
 sub Period('P500D') = PlainDateTime('Sun 1968-08-18 00:00:00')
 add Period('P10Y123M55W500D') = PlainDateTime('Tue 1992-09-01 00:00:00')
 sub Period('P10Y123M55W500D') = PlainDateTime('Wed 1947-04-30 00:00:00')
