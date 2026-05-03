--TEST--
DateTimeFormatter modified julian day symbol compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.inc';
include __DIR__ . '/include-intl.inc';

$patterns = ['g'];

$gregorianUs = new time\GregorianCalendar(time\IsoDayOfWeek::Sunday, 1);

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
    'plainDatePast' => time\PlainDate::fromYmd(-1, 1, 1, $gregorianUs),
    'plainDateMonthStart' => time\PlainDate::fromYmd(2024, 3, 1, $gregorianUs),
    'plainDateCenturyEdge' => time\PlainDate::fromYmd(2000, 1, 1, $gregorianUs),
    'plainDateCenturyBoundary' => time\PlainDate::fromYmd(1999, 12, 31, $gregorianUs),
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
instant pattern g
	timelib=2460385	intl=2460385
zdtNewYork pattern g
	timelib=2460385	intl=2460385
zdtKolkata pattern g
	timelib=2460385	intl=2460385
zdtKiritimati pattern g
	timelib=2460386	intl=2460386
zdtBoundaryNewYork pattern g
	timelib=2460310	intl=2460310
zdtBoundaryKiritimati pattern g
	timelib=2460342	intl=2460342
plainDateFuture pattern g
	timelib=5373850	intl=5373850
plainDateLeapDay pattern g
	timelib=2460370	intl=2460370
plainDatePast pattern g
	timelib=1721060	intl=1721060
plainDateMonthStart pattern g
	timelib=2460371	intl=2460371
plainDateCenturyEdge pattern g
	timelib=2451545	intl=2451545
plainDateCenturyBoundary pattern g
	timelib=2451544	intl=2451544
