--TEST--
JulianCalendar->getDaysSinceUnixEpochByYmd()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = time\JulianCalendar::getInstance();

$list = [
    [1, 1, 2],
    [1, 1, 3],
    [1582, 10, 4],
    [1582, 10, 5],
    [1969, 12, 19],
    [2003, 10, 7],
];
foreach ($list as $ymd) {
    echo stringify($ymd) . ": {$cal->getDaysSinceUnixEpochByYmd(...$ymd)}\n";
}

--EXPECT--
[1, 1, 2]: -719163
[1, 1, 3]: -719162
[1582, 10, 4]: -141428
[1582, 10, 5]: -141427
[1969, 12, 19]: 0
[2003, 10, 7]: 12345
