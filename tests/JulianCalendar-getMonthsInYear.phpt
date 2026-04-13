--TEST--
JulianCalendar->getMonthsInYear()
--FILE--
<?php

include __DIR__ . '/include.inc';

$cal = new time\JulianCalendar();

$years = [-401, -101, -5, -1, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: {$cal->getMonthsInYear($year)}\n";
}

--EXPECT--
-401: 12
-101: 12
-5: 12
-1: 12
4: 12
100: 12
400: 12
1970: 12
