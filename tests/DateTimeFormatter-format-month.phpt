--TEST--
DateTimeFormatter->format month
--FILE--
<?php

include __DIR__ . '/include.php';

$dates = [
    time\PlainDate::fromYmd(2000, 1, 1),
    time\PlainDate::fromYmd(2000, 2, 28),
    time\PlainDate::fromYmd(2000, 3, 1),
    time\PlainDate::fromYmd(2000, 3, 31),
    time\PlainDate::fromYmd(2000, 4, 30),
    time\PlainDate::fromYmd(2000, 5, 31),
    time\PlainDate::fromYmd(2000, 6, 30),
    time\PlainDate::fromYmd(2000, 7, 31),
    time\PlainDate::fromYmd(2000, 8, 31),
    time\PlainDate::fromYmd(2000, 9, 30),
    time\PlainDate::fromYmd(2000, 10, 31),
    time\PlainDate::fromYmd(2000, 11, 30),
    time\PlainDate::fromYmd(2000, 12, 31),
    time\PlainDate::fromYmd(2004, 1, 1),
    time\PlainDate::fromYmd(2004, 2, 28),
    time\PlainDate::fromYmd(2004, 2, 29),
    time\PlainDate::fromYmd(2004, 3, 1),
    time\PlainDate::fromYmd(2004, 12, 31),
    time\PlainDate::fromYmd(0, 1, 1),
    time\PlainDate::fromYmd(-1, 1, 1),
];

$formatters = [
    new \time\DateTimeFormatter(\time\FormatToken::Month->value),
    new \time\DateTimeFormatter(\time\FormatToken::MonthWithLeadingZeros->value),
    new \time\DateTimeFormatter(\time\FormatToken::MonthName->value),
    new \time\DateTimeFormatter(\time\FormatToken::MonthAbbreviation->value),
];

echo "Date\t\t\t\tNum\tZero\tName\tAbbr\n";
foreach ($dates as $date) {
    echo stringify($date);
    foreach ($formatters as $formatter) {
        echo "\t" . $formatter->format($date);
    }
    echo "\n";
}

--EXPECT--
Date				Num	Zero	Name	Abbr
PlainDate('Sat 2000-01-01')	1	01	January	Jan
PlainDate('Mon 2000-02-28')	2	02	February	Feb
PlainDate('Wed 2000-03-01')	3	03	March	Mar
PlainDate('Fri 2000-03-31')	3	03	March	Mar
PlainDate('Sun 2000-04-30')	4	04	April	Apr
PlainDate('Wed 2000-05-31')	5	05	May	May
PlainDate('Fri 2000-06-30')	6	06	June	Jun
PlainDate('Mon 2000-07-31')	7	07	July	Jul
PlainDate('Thu 2000-08-31')	8	08	August	Aug
PlainDate('Sat 2000-09-30')	9	09	September	Sep
PlainDate('Tue 2000-10-31')	10	10	October	Oct
PlainDate('Thu 2000-11-30')	11	11	November	Nov
PlainDate('Sun 2000-12-31')	12	12	December	Dec
PlainDate('Thu 2004-01-01')	1	01	January	Jan
PlainDate('Sat 2004-02-28')	2	02	February	Feb
PlainDate('Sun 2004-02-29')	2	02	February	Feb
PlainDate('Mon 2004-03-01')	3	03	March	Mar
PlainDate('Fri 2004-12-31')	12	12	December	Dec
PlainDate('Sat 0-01-01')	1	01	January	Jan
PlainDate('Fri -1-01-01')	1	01	January	Jan
