--TEST--
DateTimeFormatter week symbols compared with IntlDateFormatter (en-GB)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.php';
include __DIR__ . '/include-intl.php';

$patterns = [
    "'w:'w 'YYYY:'YYYY",
    "'ww:'ww 'YYYY:'YYYY",
    "'W:'W 'YYYY:'Y",
];

$gregorianGb = new time\GregorianCalendar(1, 4);

$samples = [
    'instant' => time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
    'zdtNewYork' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('America/New_York'),
        $gregorianGb,
    ),
    'zdtKolkata' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('Asia/Kolkata'),
        $gregorianGb,
    ),
    'zdtKiritimati' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('Pacific/Kiritimati'),
        $gregorianGb,
    ),
    'week1' => time\Instant::fromYmd(2024, 1, 1, 12, 0, 0, 0),
    'yearBoundaryStart' => time\Instant::fromYmd(2019, 12, 30, 12, 0, 0, 0),
    'plainDateBoundaryStart' => time\PlainDate::fromYmd(2019, 12, 30, $gregorianGb),
    'plainDateBoundaryEnd' => time\PlainDate::fromYmd(2021, 1, 1, $gregorianGb),
    'plainDateMonthBoundary' => time\PlainDate::fromYmd(2024, 1, 31, $gregorianGb),
    'yearEndToNextWeek1' => time\Instant::fromYmd(2024, 12, 30, 12, 0, 0, 0),
    'yearEndToNextWeek2' => time\Instant::fromYmd(2024, 12, 31, 12, 0, 0, 0),
    'yearStartLastWeekPrev1' => time\Instant::fromYmd(2022, 1, 1, 12, 0, 0, 0),
    'yearStartLastWeekPrev2' => time\Instant::fromYmd(2010, 1, 1, 12, 0, 0, 0),
    'plainDateYearEndToNextWeek' => time\PlainDate::fromYmd(2024, 12, 30, $gregorianGb),
    'plainDateYearStartLastWeekPrev' => time\PlainDate::fromYmd(2022, 1, 1, $gregorianGb),
];

$intl = new IntlDateFormatter(
    'en_GB',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    'UTC',
    IntlDateFormatter::GREGORIAN,
);

foreach ($samples as $label => $sample) {
    $timezone = timezoneForIntl($sample);
    $timestamp = timestampForIntl($sample);

    foreach ($patterns as $pattern) {
        $formatter = new time\DateTimeFormatter($pattern);
        $timeValue = $formatter->format($sample);

        $intl->setPattern($pattern);
        $intl->setTimeZone($timezone);
        $intlValue = $intl->format($timestamp);

        echo "{$label} pattern {$pattern}\n";
        echo "\ttimelib={$timeValue}\tintl={$intlValue}\n";
    }
}

--EXPECT--
instant pattern 'w:'w 'YYYY:'YYYY
	timelib=w:11 YYYY:2024	intl=w:11 YYYY:2024
instant pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:11 YYYY:2024	intl=ww:11 YYYY:2024
instant pattern 'W:'W 'YYYY:'Y
	timelib=W:2 YYYY:2024	intl=W:2 YYYY:2024
zdtNewYork pattern 'w:'w 'YYYY:'YYYY
	timelib=w:11 YYYY:2024	intl=w:11 YYYY:2024
zdtNewYork pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:11 YYYY:2024	intl=ww:11 YYYY:2024
zdtNewYork pattern 'W:'W 'YYYY:'Y
	timelib=W:2 YYYY:2024	intl=W:2 YYYY:2024
zdtKolkata pattern 'w:'w 'YYYY:'YYYY
	timelib=w:11 YYYY:2024	intl=w:11 YYYY:2024
zdtKolkata pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:11 YYYY:2024	intl=ww:11 YYYY:2024
zdtKolkata pattern 'W:'W 'YYYY:'Y
	timelib=W:2 YYYY:2024	intl=W:2 YYYY:2024
zdtKiritimati pattern 'w:'w 'YYYY:'YYYY
	timelib=w:11 YYYY:2024	intl=w:11 YYYY:2024
zdtKiritimati pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:11 YYYY:2024	intl=ww:11 YYYY:2024
zdtKiritimati pattern 'W:'W 'YYYY:'Y
	timelib=W:2 YYYY:2024	intl=W:2 YYYY:2024
week1 pattern 'w:'w 'YYYY:'YYYY
	timelib=w:1 YYYY:2024	intl=w:1 YYYY:2024
week1 pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:01 YYYY:2024	intl=ww:01 YYYY:2024
week1 pattern 'W:'W 'YYYY:'Y
	timelib=W:1 YYYY:2024	intl=W:1 YYYY:2024
yearBoundaryStart pattern 'w:'w 'YYYY:'YYYY
	timelib=w:1 YYYY:2020	intl=w:1 YYYY:2020
yearBoundaryStart pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:01 YYYY:2020	intl=ww:01 YYYY:2020
yearBoundaryStart pattern 'W:'W 'YYYY:'Y
	timelib=W:5 YYYY:2020	intl=W:5 YYYY:2020
plainDateBoundaryStart pattern 'w:'w 'YYYY:'YYYY
	timelib=w:1 YYYY:2020	intl=w:1 YYYY:2020
plainDateBoundaryStart pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:01 YYYY:2020	intl=ww:01 YYYY:2020
plainDateBoundaryStart pattern 'W:'W 'YYYY:'Y
	timelib=W:5 YYYY:2020	intl=W:5 YYYY:2020
plainDateBoundaryEnd pattern 'w:'w 'YYYY:'YYYY
	timelib=w:53 YYYY:2020	intl=w:53 YYYY:2020
plainDateBoundaryEnd pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:53 YYYY:2020	intl=ww:53 YYYY:2020
plainDateBoundaryEnd pattern 'W:'W 'YYYY:'Y
	timelib=W:0 YYYY:2020	intl=W:0 YYYY:2020
plainDateMonthBoundary pattern 'w:'w 'YYYY:'YYYY
	timelib=w:5 YYYY:2024	intl=w:5 YYYY:2024
plainDateMonthBoundary pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:05 YYYY:2024	intl=ww:05 YYYY:2024
plainDateMonthBoundary pattern 'W:'W 'YYYY:'Y
	timelib=W:5 YYYY:2024	intl=W:5 YYYY:2024
yearEndToNextWeek1 pattern 'w:'w 'YYYY:'YYYY
	timelib=w:1 YYYY:2025	intl=w:1 YYYY:2025
yearEndToNextWeek1 pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:01 YYYY:2025	intl=ww:01 YYYY:2025
yearEndToNextWeek1 pattern 'W:'W 'YYYY:'Y
	timelib=W:5 YYYY:2025	intl=W:5 YYYY:2025
yearEndToNextWeek2 pattern 'w:'w 'YYYY:'YYYY
	timelib=w:1 YYYY:2025	intl=w:1 YYYY:2025
yearEndToNextWeek2 pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:01 YYYY:2025	intl=ww:01 YYYY:2025
yearEndToNextWeek2 pattern 'W:'W 'YYYY:'Y
	timelib=W:5 YYYY:2025	intl=W:5 YYYY:2025
yearStartLastWeekPrev1 pattern 'w:'w 'YYYY:'YYYY
	timelib=w:52 YYYY:2021	intl=w:52 YYYY:2021
yearStartLastWeekPrev1 pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:52 YYYY:2021	intl=ww:52 YYYY:2021
yearStartLastWeekPrev1 pattern 'W:'W 'YYYY:'Y
	timelib=W:0 YYYY:2021	intl=W:0 YYYY:2021
yearStartLastWeekPrev2 pattern 'w:'w 'YYYY:'YYYY
	timelib=w:53 YYYY:2009	intl=w:53 YYYY:2009
yearStartLastWeekPrev2 pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:53 YYYY:2009	intl=ww:53 YYYY:2009
yearStartLastWeekPrev2 pattern 'W:'W 'YYYY:'Y
	timelib=W:0 YYYY:2009	intl=W:0 YYYY:2009
plainDateYearEndToNextWeek pattern 'w:'w 'YYYY:'YYYY
	timelib=w:1 YYYY:2025	intl=w:1 YYYY:2025
plainDateYearEndToNextWeek pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:01 YYYY:2025	intl=ww:01 YYYY:2025
plainDateYearEndToNextWeek pattern 'W:'W 'YYYY:'Y
	timelib=W:5 YYYY:2025	intl=W:5 YYYY:2025
plainDateYearStartLastWeekPrev pattern 'w:'w 'YYYY:'YYYY
	timelib=w:52 YYYY:2021	intl=w:52 YYYY:2021
plainDateYearStartLastWeekPrev pattern 'ww:'ww 'YYYY:'YYYY
	timelib=ww:52 YYYY:2021	intl=ww:52 YYYY:2021
plainDateYearStartLastWeekPrev pattern 'W:'W 'YYYY:'Y
	timelib=W:0 YYYY:2021	intl=W:0 YYYY:2021
