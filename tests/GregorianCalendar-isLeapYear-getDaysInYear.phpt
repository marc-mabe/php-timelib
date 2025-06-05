--TEST--
GregorianCalendar->isLeapYear() & getDaysInYear()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = time\GregorianCalendar::getInstance();

$years = [-400, -100, -4, 0, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: " . var_export($cal->isLeapYear($year), true) . ", {$cal->getDaysInYear($year)}\n";
}

--EXPECT--
-400: true, 366
-100: false, 365
-4: true, 366
0: true, 366
4: true, 366
100: false, 365
400: true, 366
1970: false, 365
