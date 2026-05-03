--TEST--
HebrewCalendar conversion methods
--FILE--
<?php

include __DIR__ . '/include.inc';

$cal = new time\HebrewCalendar();

$samples = [
    [1, 1, 1],
    [3, 1, 1],
    [3, 13, 29],
    [4, 7, 1],
    [6, 13, 1],
    [8, 1, 1],
    [19, 12, 1],
    [19, 13, 29],
];
foreach ($samples as $ymd) {
    $daysSinceEpoch = $cal->getDaysSinceUnixEpochByYmd(...$ymd);
    $rt = $cal->getYmdByDaysSinceUnixEpoch($daysSinceEpoch);
    echo implode('-', $ymd) . " => " . ($rt === $ymd ? 'ok' : 'bad') . "\n";
}

$years = [1, 3, 6, 8, 11, 19];
foreach ($years as $year) {
    $daysInYear = $cal->getDaysInYear($year);
    $dayOfYears = [1, intdiv($daysInYear, 2), $daysInYear];
    $ydOk = true;
    foreach ($dayOfYears as $dayOfYear) {
        $daysSinceEpoch = $cal->getDaysSinceUnixEpochByYd($year, $dayOfYear);
        $ymd = $cal->getYmdByDaysSinceUnixEpoch($daysSinceEpoch);
        if ($cal->getDayOfYearByYmd(...$ymd) !== $dayOfYear) {
            $ydOk = false;
            break;
        }
    }
    echo "yd-{$year}: " . ($ydOk ? 'ok' : 'bad') . "\n";
}

$first = $cal->getDaysSinceUnixEpochByYmd(1, 1, 1);
echo "start: " . (($cal->getYmdByDaysSinceUnixEpoch($first) === [1, 1, 1]) ? 'ok' : 'bad') . "\n";

try {
    $cal->getYmdByDaysSinceUnixEpoch($first - 1);
    echo "below-start: no\n";
} catch (Throwable $e) {
    echo 'below-start: ' . $e::class . "\n";
}

try {
    $cal->getDaysSinceUnixEpochByYmd(0, 1, 1);
    echo "year-zero: no\n";
} catch (Throwable $e) {
    echo 'year-zero: ' . $e::class . "\n";
}

$jdn = $cal->getJdnByYmd(3, 1, 1);
$mjd = $cal->getMjdByYmd(3, 1, 1);
echo "jdn: " . ($cal->getYmdByJdn($jdn) === [3, 1, 1] ? 'ok' : 'bad') . "\n";
echo "mjd: " . ($cal->getYmdByMjd($mjd) === [3, 1, 1] ? 'ok' : 'bad') . "\n";

--EXPECT--
1-1-1 => ok
3-1-1 => ok
3-13-29 => ok
4-7-1 => ok
6-13-1 => ok
8-1-1 => ok
19-12-1 => ok
19-13-29 => ok
yd-1: ok
yd-3: ok
yd-6: ok
yd-8: ok
yd-11: ok
yd-19: ok
start: ok
below-start: time\RangeError
year-zero: time\InvalidValueException
jdn: ok
mjd: ok
