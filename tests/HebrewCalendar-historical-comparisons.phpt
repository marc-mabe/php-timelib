--TEST--
HebrewCalendar historical date comparisons
--FILE--
<?php

include __DIR__ . '/include.inc';

$hebrew = new time\HebrewCalendar();
$gregorian = new time\GregorianCalendar();
$julian = new time\JulianCalendar();

$checks = [
    // Israeli Declaration of Independence:
    // https://en.wikipedia.org/wiki/Israeli_Declaration_of_Independence
    'declaration' => [
        'gregorian' => [1948, 5, 14],
        'hebrew' => [5708, 9, 5]
    ],

    // Yom Kippur War (start):
    // https://www.hadracha.org/en/historyView.asp?historyID=101&method=r
    'yom-kippur-war' => [
        'gregorian' => [1973, 10, 6],
        'hebrew' => [5734, 1, 10]
    ],

    // Edict of Expulsion (1290):
    // https://en.wikipedia.org/wiki/Edict_of_Expulsion
    'expulsion-england' => [
        'julian' => [1290, 7, 18],
        'hebrew' => [5050, 11, 9]
    ],

    // 9 Av as Tisha B'Av (mass deportation context):
    // https://en.wikipedia.org/wiki/9th_of_Av
    'tisha-av-5702' => [
        'gregorian' => [1942, 7, 23],
        'hebrew' => [5702, 11, 9]
    ],

    // February 29 in 1960 is 1 Adar (calendar mapping):
    // https://jewishholidaysonline.com/1960
    'leap-day-1960' => [
        'gregorian' => [1960, 2, 29],
        'hebrew' => [5720, 6, 1]
    ],

    // Jerusalem Day (capturing East Jerusalem during Six-Day War):
    // https://www.science.co.il/israel/holidays/jerusalem-day/
    'jerusalem-day-1967' => [
        'gregorian' => [1967, 6, 7],
        'hebrew' => [5727, 9, 28]
    ],

    // Day of Salvation and Liberation (Victory in Europe Day mapping):
    // https://en.wikipedia.org/wiki/Day_of_Salvation_and_Liberation
    'day-of-salvation-1945' => [
        'gregorian' => [1945, 5, 9],
        'hebrew' => [5705, 8, 26]
    ],
];

foreach ($checks as $name => $case) {
    echo $name . ': ' . stringify($case);

    $referenceTs = array_key_exists('gregorian', $case)
        ? $gregorian->getDaysSinceUnixEpochByYmd(...$case['gregorian'])
        : $julian->getDaysSinceUnixEpochByYmd(...$case['julian']);
    $hebrewTs = $hebrew->getDaysSinceUnixEpochByYmd(...$case['hebrew']);

    if ($referenceTs === $hebrewTs) {
        echo " ok\n";
    } else {
        echo " fail ({$referenceTs} !== {$hebrewTs})\n";
    }
}

--EXPECT--
declaration: ['gregorian' => [1948, 5, 14], 'hebrew' => [5708, 9, 5]] ok
yom-kippur-war: ['gregorian' => [1973, 10, 6], 'hebrew' => [5734, 1, 10]] ok
expulsion-england: ['julian' => [1290, 7, 18], 'hebrew' => [5050, 11, 9]] ok
tisha-av-5702: ['gregorian' => [1942, 7, 23], 'hebrew' => [5702, 11, 9]] ok
leap-day-1960: ['gregorian' => [1960, 2, 29], 'hebrew' => [5720, 6, 1]] ok
jerusalem-day-1967: ['gregorian' => [1967, 6, 7], 'hebrew' => [5727, 9, 28]] ok
day-of-salvation-1945: ['gregorian' => [1945, 5, 9], 'hebrew' => [5705, 8, 26]] ok
