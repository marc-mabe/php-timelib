--TEST--
PlainDate->add() and PlainDate->sub() with Period
--FILE--
<?php

include __DIR__ . '/include.php';

$dates = [
    time\PlainDate::fromYmd(1970, 1, 1),
    time\PlainDate::fromYmd(1969, 12, 31),
    time\PlainDate::fromYmd(0, 1, 1),
];

$periods = [
    new time\Period(years: 10),
    new time\Period(months: 123),
    new time\Period(weeks: 55),
    new time\Period(days: 500),
    new time\Period(years: 10, months: 123, weeks: 55, days: 500),
];

foreach ($dates as $date) {
    echo stringify($date) . "\n";
    foreach ($periods as $period) {
        echo " add " . stringify($period) . " = " . stringify($date->add($period)) . "\n";
        echo " sub " . stringify($period) . " = " . stringify($date->sub($period)) . "\n";
    }
}

--EXPECT--
PlainDate('Thu 1970-01-01')
 add Period('P10Y') = PlainDate('Tue 1980-01-01')
 sub Period('P10Y') = PlainDate('Fri 1960-01-01')
 add Period('P123M') = PlainDate('Tue 1980-04-01')
 sub Period('P123M') = PlainDate('Thu 1959-10-01')
 add Period('P55W') = PlainDate('Thu 1971-01-21')
 sub Period('P55W') = PlainDate('Thu 1968-12-12')
 add Period('P500D') = PlainDate('Sun 1971-05-16')
 sub Period('P500D') = PlainDate('Mon 1968-08-19')
 add Period('P10Y123M55W500D') = PlainDate('Wed 1992-09-02')
 sub Period('P10Y123M55W500D') = PlainDate('Wed 1947-04-30')
PlainDate('Wed 1969-12-31')
 add Period('P10Y') = PlainDate('Mon 1979-12-31')
 sub Period('P10Y') = PlainDate('Thu 1959-12-31')
 add Period('P123M') = PlainDate('Mon 1980-03-31')
 sub Period('P123M') = PlainDate('Thu 1959-10-01')
 add Period('P55W') = PlainDate('Wed 1971-01-20')
 sub Period('P55W') = PlainDate('Wed 1968-12-11')
 add Period('P500D') = PlainDate('Sat 1971-05-15')
 sub Period('P500D') = PlainDate('Sun 1968-08-18')
 add Period('P10Y123M55W500D') = PlainDate('Tue 1992-09-01')
 sub Period('P10Y123M55W500D') = PlainDate('Wed 1947-04-30')
PlainDate('Sat 0-01-01')
 add Period('P10Y') = PlainDate('Fri 10-01-01')
 sub Period('P10Y') = PlainDate('Mon -10-01-01')
 add Period('P123M') = PlainDate('Thu 10-04-01')
 sub Period('P123M') = PlainDate('Sun -11-10-01')
 add Period('P55W') = PlainDate('Sat 1-01-20')
 sub Period('P55W') = PlainDate('Sat -2-12-12')
 add Period('P500D') = PlainDate('Tue 1-05-15')
 sub Period('P500D') = PlainDate('Wed -2-08-19')
 add Period('P10Y123M55W500D') = PlainDate('Sat 22-09-03')
 sub Period('P10Y123M55W500D') = PlainDate('Fri -23-04-29')
