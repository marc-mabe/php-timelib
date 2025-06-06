--TEST--
JulianCalendar->isLeapYear() & getDaysInYear()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\JulianCalendar();

$years = [-400, -100, -4, 0, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: " . var_export($cal->isLeapYear($year), true) . ", {$cal->getDaysInYear($year)}\n";
}

--EXPECT--
-400: true, 366
-100: true, 366
-4: true, 366
0: true, 366
4: true, 366
100: true, 366
400: true, 366
1970: false, 365
