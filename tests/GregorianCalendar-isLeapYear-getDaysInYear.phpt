--TEST--
GregorianCalendar->isLeapYear() & getDaysInYear()
--FILE--
<?php

include __DIR__ . '/include.inc';

$cal = new time\GregorianCalendar();

$years = [-401, -101, -5, -1, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: " . var_export($cal->isLeapYear($year), true) . ", {$cal->getDaysInYear($year)}\n";
}

--EXPECT--
-401: true, 366
-101: false, 365
-5: true, 366
-1: true, 366
4: true, 366
100: false, 365
400: true, 366
1970: false, 365
