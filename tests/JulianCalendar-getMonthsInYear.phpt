--TEST--
JulianCalendar->getMonthsInYear()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\JulianCalendar();

$years = [-400, -100, -4, 0, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: {$cal->getMonthsInYear($year)}\n";
}

--EXPECT--
-400: 12
-100: 12
-4: 12
0: 12
4: 12
100: 12
400: 12
1970: 12
