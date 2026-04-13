--TEST--
DateTimeFormatter day symbols compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.php';
include __DIR__ . '/include-intl.php';

$patterns = [
    'd',
    'dd',
    'D',
    'DD',
    'F',
];

$gregorianUs = new time\GregorianCalendar(7, 1);

$samples = [
    'instant' => time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
    'zdtNewYork' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('America/New_York'),
        $gregorianUs,
    ),
    'zdtKolkata' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('Asia/Kolkata'),
        $gregorianUs,
    ),
    'zdtKiritimati' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('Pacific/Kiritimati'),
        $gregorianUs,
    ),
    'zdtBoundaryNewYork' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2023, 12, 31, 23, 30, 0, 0),
        time\Zone::fromIdentifier('America/New_York'),
        $gregorianUs,
    ),
    'zdtBoundaryKiritimati' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 1, 31, 23, 30, 0, 0),
        time\Zone::fromIdentifier('Pacific/Kiritimati'),
        $gregorianUs,
    ),
    'plainDateFuture' => time\PlainDate::fromYmd(10000, 12, 31, $gregorianUs),
    'plainDateLeapDay' => time\PlainDate::fromYmd(2024, 2, 29, $gregorianUs),
    'plainDateMonthStart' => time\PlainDate::fromYmd(2024, 3, 1, $gregorianUs),
];

$intl = new IntlDateFormatter(
    'en_US',
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
instant pattern d
	timelib=15	intl=15
instant pattern dd
	timelib=15	intl=15
instant pattern D
	timelib=75	intl=75
instant pattern DD
	timelib=75	intl=75
instant pattern F
	timelib=3	intl=3
zdtNewYork pattern d
	timelib=15	intl=15
zdtNewYork pattern dd
	timelib=15	intl=15
zdtNewYork pattern D
	timelib=75	intl=75
zdtNewYork pattern DD
	timelib=75	intl=75
zdtNewYork pattern F
	timelib=3	intl=3
zdtKolkata pattern d
	timelib=15	intl=15
zdtKolkata pattern dd
	timelib=15	intl=15
zdtKolkata pattern D
	timelib=75	intl=75
zdtKolkata pattern DD
	timelib=75	intl=75
zdtKolkata pattern F
	timelib=3	intl=3
zdtKiritimati pattern d
	timelib=16	intl=16
zdtKiritimati pattern dd
	timelib=16	intl=16
zdtKiritimati pattern D
	timelib=76	intl=76
zdtKiritimati pattern DD
	timelib=76	intl=76
zdtKiritimati pattern F
	timelib=3	intl=3
zdtBoundaryNewYork pattern d
	timelib=31	intl=31
zdtBoundaryNewYork pattern dd
	timelib=31	intl=31
zdtBoundaryNewYork pattern D
	timelib=365	intl=365
zdtBoundaryNewYork pattern DD
	timelib=365	intl=365
zdtBoundaryNewYork pattern F
	timelib=5	intl=5
zdtBoundaryKiritimati pattern d
	timelib=1	intl=1
zdtBoundaryKiritimati pattern dd
	timelib=01	intl=01
zdtBoundaryKiritimati pattern D
	timelib=32	intl=32
zdtBoundaryKiritimati pattern DD
	timelib=32	intl=32
zdtBoundaryKiritimati pattern F
	timelib=1	intl=1
plainDateFuture pattern d
	timelib=31	intl=31
plainDateFuture pattern dd
	timelib=31	intl=31
plainDateFuture pattern D
	timelib=366	intl=366
plainDateFuture pattern DD
	timelib=366	intl=366
plainDateFuture pattern F
	timelib=5	intl=5
plainDateLeapDay pattern d
	timelib=29	intl=29
plainDateLeapDay pattern dd
	timelib=29	intl=29
plainDateLeapDay pattern D
	timelib=60	intl=60
plainDateLeapDay pattern DD
	timelib=60	intl=60
plainDateLeapDay pattern F
	timelib=5	intl=5
plainDateMonthStart pattern d
	timelib=1	intl=1
plainDateMonthStart pattern dd
	timelib=01	intl=01
plainDateMonthStart pattern D
	timelib=61	intl=61
plainDateMonthStart pattern DD
	timelib=61	intl=61
plainDateMonthStart pattern F
	timelib=1	intl=1
