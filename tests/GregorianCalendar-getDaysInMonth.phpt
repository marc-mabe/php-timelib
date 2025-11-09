--TEST--
GregorianCalendar->getDaysInMonth()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\GregorianCalendar();

$years = [-401, -101, -5, -1, 4, 100, 400, 1970];
foreach ($years as $year) {
    echo "{$year}: ";
    for ($month = 1; $month <= 12; $month++) {
        echo $cal->getDaysInMonth($year, $month);

        if ($month !== 12) {
            echo ", ";
        } else {
            echo "\n";
        }
    }
}

--EXPECT--
-401: 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
-101: 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
-5: 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
-1: 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
4: 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
100: 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
400: 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
1970: 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31
