--TEST--
JulianCalendar->getYmdByDaysSinceUnixEpoch()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = new time\JulianCalendar();

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
-719163: [1, 1, 2]
-719162: [1, 1, 3]
-141428: [1582, 10, 4]
-141427: [1582, 10, 5]
0: [1969, 12, 19]
12345: [2003, 10, 7]
