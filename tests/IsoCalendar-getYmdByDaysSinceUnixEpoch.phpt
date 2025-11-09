--TEST--
IsoCalendar->getYmdByDaysSinceUnixEpoch()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = time\IsoCalendar::getInstance();

$list = [
    -719163,
    -719162,
    -141428,
    -141427,
    0,
    12345,
];
foreach ($list as $daysSinceEpoch) {
    echo "{$daysSinceEpoch}: " . stringify($cal->getYmdByDaysSinceUnixEpoch($daysSinceEpoch)) . "\n";
}

--EXPECT--
-719163: [0, 12, 31]
-719162: [1, 1, 1]
-141428: [1582, 10, 14]
-141427: [1582, 10, 15]
0: [1970, 1, 1]
12345: [2003, 10, 20]
