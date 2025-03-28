--TEST--
GregorianCalendar->getYmdByUnixTimestamp()
--FILE--
<?php

include __DIR__ . '/include.php';

$calendar = time\GregorianCalendar::getInstance();
$utc = new DateTimeZone('UTC');
$unixTimestamps = [
    1,
    0,
    -1,
    86400,
    -86401,
    DateTime::createFromFormat('X-m-d H:i:s', '2000-04-06 00:00:00', $utc)->getTimestamp(),
    DateTime::createFromFormat('X-m-d H:i:s', '2000-04-06 23:59:59', $utc)->getTimestamp(),
    DateTime::createFromFormat('X-m-d H:i:s', '0000-03-01 00:00:00', $utc)->getTimestamp(),
    DateTime::createFromFormat('X-m-d H:i:s', '0000-01-01 00:00:00', $utc)->getTimestamp(),
    DateTime::createFromFormat('X-m-d H:i:s', '-1234-05-06 00:00:00', $utc)->getTimestamp(),
];

foreach ($unixTimestamps as $ts) {
    echo $ts . ': ' . stringify($calendar->getYmdByUnixTimestamp($ts)) . "\n";
}

--EXPECT--
1: [1970, time\Month::January, 1]
0: [1970, time\Month::January, 1]
-1: [1969, time\Month::December, 31]
86400: [1970, time\Month::January, 2]
-86401: [1969, time\Month::December, 30]
954979200: [2000, time\Month::April, 6]
955065599: [2000, time\Month::April, 6]
-62162035200: [0, time\Month::March, 1]
-62167219200: [0, time\Month::January, 1]
-101097676800: [-1234, time\Month::May, 6]
