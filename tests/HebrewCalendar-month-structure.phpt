--TEST--
HebrewCalendar month structure
--FILE--
<?php

include __DIR__ . '/include.inc';

$cal = new time\HebrewCalendar();
$leap = $cal->getMonthNameMap(3);
$common = $cal->getMonthNameMap(4);

echo "leap-6: {$leap[6]}, leap-7: {$leap[7]}, leap-13: {$leap[13]}\n";
echo "common-6: {$common[6]}, common-7: {$common[7]}, common-12: {$common[12]}\n";

$years = [1, 2, 3, 4, 6, 8, 11, 12, 14, 17, 19];
$ok = true;
foreach ($years as $year) {
    $monthCount = $cal->getMonthsInYear($year);
    $days = 0;
    for ($month = 1; $month <= $monthCount; $month++) {
        $days += $cal->getDaysInMonth($year, $month);
    }
    if ($days !== $cal->getDaysInYear($year)) {
        $ok = false;
        break;
    }
}
echo "month-sums: " . ($ok ? 'ok' : 'fail') . "\n";

--EXPECT--
leap-6: Adar I, leap-7: Adar II, leap-13: Elul
common-6: Adar, common-7: Nisan, common-12: Elul
month-sums: ok
