--TEST--
JulianCalendar->isLeapYear() & getDaysInYear()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\JulianCalendar();

$years = [-401, -101, -5, -1, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: " . var_export($cal->isLeapYear($year), true) . ", {$cal->getDaysInYear($year)}\n";
}

--EXPECT--
-401: true, 366
-101: true, 366
-5: true, 366
-1: true, 366
4: true, 366
100: true, 366
400: true, 366
1970: false, 365
