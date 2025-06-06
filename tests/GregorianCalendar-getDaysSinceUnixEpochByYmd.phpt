--TEST--
GregorianCalendar->getDaysSinceUnixEpochByYmd()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\GregorianCalendar();

$list = [
    [0, 12, 31],
    [1, 1, 1],
    [1582, 10, 14],
    [1582, 10, 15],
    [1970, 1, 1],
    [2003, 10, 20],
];
foreach ($list as $ymd) {
    echo stringify($ymd) . ": {$cal->getDaysSinceUnixEpochByYmd(...$ymd)}\n";
}

--EXPECT--
[0, 12, 31]: -719163
[1, 1, 1]: -719162
[1582, 10, 14]: -141428
[1582, 10, 15]: -141427
[1970, 1, 1]: 0
[2003, 10, 20]: 12345
