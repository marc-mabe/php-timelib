--TEST--
HebrewCalendar->addPeriodToYmd()
--FILE--
<?php

include __DIR__ . '/include.inc';

$cal = new time\HebrewCalendar();

$cases = [
    [
        'period' => new time\Period(days: 1),
        'ymd' => [1, 1, 30],
    ], [
        'period' => new time\Period(months: 1),
        'ymd' => [3, 12, 30],
    ], [
        'period' => new time\Period(months: -2),
        'ymd' => [3, 1, 1],
    ], [
        'period' => new time\Period(years: 1),
        'ymd' => [10, 1, 1],
    ], [
        'period' => new time\Period(years: -1),
        'ymd' => [1, 1, 1],
    ]
];

foreach ($cases as $case) {
    echo stringify($case['ymd']) . ' + ' . stringify($case['period']) . ' = ';
    try {
        $result = $cal->addPeriodToYmd($case['period'], ...$case['ymd']);
        echo stringify($result) . "\n";
    } catch (Throwable $e) {
        echo $e::class . ': ' . $e->getMessage() . "\n";
    }
}


--EXPECT--
[1, 1, 30] + Period('P1D') = [1, 2, 1]
[3, 12, 30] + Period('P1M') = [4, 1, 1]
[3, 1, 1] + Period('P-2M') = [2, 11, 1]
[10, 1, 1] + Period('P1Y') = [11, 1, 1]
[1, 1, 1] + Period('P-1Y') = time\RangeError: Resulting Hebrew year must be within the supported range, got 0
