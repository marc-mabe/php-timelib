--TEST--
DateTimeFormatter->format month
--FILE--
<?php

include __DIR__ . '/include.php';

$dates = [
    time\LocalDate::fromYmd(2000, 1, 1),
    time\LocalDate::fromYmd(2000, 2, 28),
    time\LocalDate::fromYmd(2000, 3, 1),
    time\LocalDate::fromYmd(2000, 3, 31),
    time\LocalDate::fromYmd(2000, 4, 30),
    time\LocalDate::fromYmd(2000, 5, 31),
    time\LocalDate::fromYmd(2000, 6, 30),
    time\LocalDate::fromYmd(2000, 7, 31),
    time\LocalDate::fromYmd(2000, 8, 31),
    time\LocalDate::fromYmd(2000, 9, 30),
    time\LocalDate::fromYmd(2000, 10, 31),
    time\LocalDate::fromYmd(2000, 11, 30),
    time\LocalDate::fromYmd(2000, 12, 31),
    time\LocalDate::fromYmd(2004, 1, 1),
    time\LocalDate::fromYmd(2004, 2, 28),
    time\LocalDate::fromYmd(2004, 2, 29),
    time\LocalDate::fromYmd(2004, 3, 1),
    time\LocalDate::fromYmd(2004, 12, 31),
    time\LocalDate::fromYmd(0, 1, 1),
    time\LocalDate::fromYmd(-1, 1, 1),
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
LocalDate('Sat 2000-01-01')	1	01	January	Jan
LocalDate('Mon 2000-02-28')	2	02	February	Feb
LocalDate('Wed 2000-03-01')	3	03	March	Mar
LocalDate('Fri 2000-03-31')	3	03	March	Mar
LocalDate('Sun 2000-04-30')	4	04	April	Apr
LocalDate('Wed 2000-05-31')	5	05	May	May
LocalDate('Fri 2000-06-30')	6	06	June	Jun
LocalDate('Mon 2000-07-31')	7	07	July	Jul
LocalDate('Thu 2000-08-31')	8	08	August	Aug
LocalDate('Sat 2000-09-30')	9	09	September	Sep
LocalDate('Tue 2000-10-31')	10	10	October	Oct
LocalDate('Thu 2000-11-30')	11	11	November	Nov
LocalDate('Sun 2000-12-31')	12	12	December	Dec
LocalDate('Thu 2004-01-01')	1	01	January	Jan
LocalDate('Sat 2004-02-28')	2	02	February	Feb
LocalDate('Sun 2004-02-29')	2	02	February	Feb
LocalDate('Mon 2004-03-01')	3	03	March	Mar
LocalDate('Fri 2004-12-31')	12	12	December	Dec
LocalDate('Sat 0-01-01')	1	01	January	Jan
LocalDate('Fri -1-01-01')	1	01	January	Jan
