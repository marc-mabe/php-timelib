--TEST--
HebrewCalendar metadata
--FILE--
<?php

include __DIR__ . '/include.inc';

$cal = new time\HebrewCalendar();

$leapYears = [3, 6, 8, 11, 14, 17, 19];
$cycleYears = range(1, 20);
$cycleOk = true;
foreach ($cycleYears as $year) {
    $isLeap = $cal->isLeapYear($year);
    if ($isLeap !== \in_array($year, $leapYears, strict: true)) {
        $cycleOk = false;
        break;
    }
}

echo "cycle: " . ($cycleOk ? 'ok' : 'fail') . "\n";
echo "months-2: {$cal->getMonthsInYear(2)}\n";
echo "months-8: {$cal->getMonthsInYear(8)}\n";
echo "day-boundary: {$cal->getDayBoundary()->name}\n";
echo "firstDayOfWeek: " . stringify($cal->firstDayOfWeek) . ", minDaysInFirstWeek: {$cal->minDaysInFirstWeek}\n";

--EXPECT--
cycle: ok
months-2: 12
months-8: 13
day-boundary: Sunset
firstDayOfWeek: time\IsoDayOfWeek::Sunday, minDaysInFirstWeek: 1
