--TEST--
DateTimeFormatter year patterns compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.php';
include __DIR__ . '/include-intl.php';

$patterns = [
    'u',
    'uu',
    'uuuu',
    'uuuuu',
    'y',
    'yy',
    'yyyy',
    'Y',
    'YY',
    'YYYY',
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
        time\Instant::fromYmd(2023, 12, 31, 23, 30, 0, 0),
        time\Zone::fromIdentifier('Pacific/Kiritimati'),
        $gregorianUs,
    ),
    'plainDateFuture' => time\PlainDate::fromYmd(10000, 1, 1, $gregorianUs),
    'plainDate1ACE' => time\PlainDate::fromYmd(1, 1, 1, $gregorianUs),
    'plainDate1BCE' => time\PlainDate::fromYmd(-1, 1, 1, $gregorianUs),
    'plainDatePast' => time\PlainDate::fromYmd(-10000, 1, 1, $gregorianUs),
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
instant pattern u
	timelib=2024	intl=2024
instant pattern uu
	timelib=2024	intl=2024
instant pattern uuuu
	timelib=2024	intl=2024
instant pattern uuuuu
	timelib=02024	intl=02024
instant pattern y
	timelib=2024	intl=2024
instant pattern yy
	timelib=24	intl=24
instant pattern yyyy
	timelib=2024	intl=2024
instant pattern Y
	timelib=2024	intl=2024
instant pattern YY
	timelib=24	intl=24
instant pattern YYYY
	timelib=2024	intl=2024
zdtNewYork pattern u
	timelib=2024	intl=2024
zdtNewYork pattern uu
	timelib=2024	intl=2024
zdtNewYork pattern uuuu
	timelib=2024	intl=2024
zdtNewYork pattern uuuuu
	timelib=02024	intl=02024
zdtNewYork pattern y
	timelib=2024	intl=2024
zdtNewYork pattern yy
	timelib=24	intl=24
zdtNewYork pattern yyyy
	timelib=2024	intl=2024
zdtNewYork pattern Y
	timelib=2024	intl=2024
zdtNewYork pattern YY
	timelib=24	intl=24
zdtNewYork pattern YYYY
	timelib=2024	intl=2024
zdtKolkata pattern u
	timelib=2024	intl=2024
zdtKolkata pattern uu
	timelib=2024	intl=2024
zdtKolkata pattern uuuu
	timelib=2024	intl=2024
zdtKolkata pattern uuuuu
	timelib=02024	intl=02024
zdtKolkata pattern y
	timelib=2024	intl=2024
zdtKolkata pattern yy
	timelib=24	intl=24
zdtKolkata pattern yyyy
	timelib=2024	intl=2024
zdtKolkata pattern Y
	timelib=2024	intl=2024
zdtKolkata pattern YY
	timelib=24	intl=24
zdtKolkata pattern YYYY
	timelib=2024	intl=2024
zdtKiritimati pattern u
	timelib=2024	intl=2024
zdtKiritimati pattern uu
	timelib=2024	intl=2024
zdtKiritimati pattern uuuu
	timelib=2024	intl=2024
zdtKiritimati pattern uuuuu
	timelib=02024	intl=02024
zdtKiritimati pattern y
	timelib=2024	intl=2024
zdtKiritimati pattern yy
	timelib=24	intl=24
zdtKiritimati pattern yyyy
	timelib=2024	intl=2024
zdtKiritimati pattern Y
	timelib=2024	intl=2024
zdtKiritimati pattern YY
	timelib=24	intl=24
zdtKiritimati pattern YYYY
	timelib=2024	intl=2024
zdtBoundaryNewYork pattern u
	timelib=2023	intl=2023
zdtBoundaryNewYork pattern uu
	timelib=2023	intl=2023
zdtBoundaryNewYork pattern uuuu
	timelib=2023	intl=2023
zdtBoundaryNewYork pattern uuuuu
	timelib=02023	intl=02023
zdtBoundaryNewYork pattern y
	timelib=2023	intl=2023
zdtBoundaryNewYork pattern yy
	timelib=23	intl=23
zdtBoundaryNewYork pattern yyyy
	timelib=2023	intl=2023
zdtBoundaryNewYork pattern Y
	timelib=2024	intl=2024
zdtBoundaryNewYork pattern YY
	timelib=24	intl=24
zdtBoundaryNewYork pattern YYYY
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern u
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern uu
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern uuuu
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern uuuuu
	timelib=02024	intl=02024
zdtBoundaryKiritimati pattern y
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern yy
	timelib=24	intl=24
zdtBoundaryKiritimati pattern yyyy
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern Y
	timelib=2024	intl=2024
zdtBoundaryKiritimati pattern YY
	timelib=24	intl=24
zdtBoundaryKiritimati pattern YYYY
	timelib=2024	intl=2024
plainDateFuture pattern u
	timelib=10000	intl=10000
plainDateFuture pattern uu
	timelib=10000	intl=10000
plainDateFuture pattern uuuu
	timelib=10000	intl=10000
plainDateFuture pattern uuuuu
	timelib=10000	intl=10000
plainDateFuture pattern y
	timelib=10000	intl=10000
plainDateFuture pattern yy
	timelib=00	intl=00
plainDateFuture pattern yyyy
	timelib=10000	intl=10000
plainDateFuture pattern Y
	timelib=10000	intl=10000
plainDateFuture pattern YY
	timelib=00	intl=00
plainDateFuture pattern YYYY
	timelib=10000	intl=10000
plainDate1ACE pattern u
	timelib=1	intl=1
plainDate1ACE pattern uu
	timelib=01	intl=01
plainDate1ACE pattern uuuu
	timelib=0001	intl=0001
plainDate1ACE pattern uuuuu
	timelib=00001	intl=00001
plainDate1ACE pattern y
	timelib=1	intl=1
plainDate1ACE pattern yy
	timelib=01	intl=01
plainDate1ACE pattern yyyy
	timelib=0001	intl=0001
plainDate1ACE pattern Y
	timelib=1	intl=1
plainDate1ACE pattern YY
	timelib=01	intl=01
plainDate1ACE pattern YYYY
	timelib=0001	intl=0001
plainDate1BCE pattern u
	timelib=0	intl=0
plainDate1BCE pattern uu
	timelib=00	intl=00
plainDate1BCE pattern uuuu
	timelib=0000	intl=0000
plainDate1BCE pattern uuuuu
	timelib=00000	intl=00000
plainDate1BCE pattern y
	timelib=1	intl=1
plainDate1BCE pattern yy
	timelib=01	intl=01
plainDate1BCE pattern yyyy
	timelib=0001	intl=0001
plainDate1BCE pattern Y
	timelib=0	intl=0
plainDate1BCE pattern YY
	timelib=00	intl=00
plainDate1BCE pattern YYYY
	timelib=0000	intl=0000
plainDatePast pattern u
	timelib=-9999	intl=-9999
plainDatePast pattern uu
	timelib=-9999	intl=-9999
plainDatePast pattern uuuu
	timelib=-9999	intl=-9999
plainDatePast pattern uuuuu
	timelib=-09999	intl=-09999
plainDatePast pattern y
	timelib=10000	intl=10000
plainDatePast pattern yy
	timelib=00	intl=00
plainDatePast pattern yyyy
	timelib=10000	intl=10000
plainDatePast pattern Y
	timelib=-9999	intl=-9999
plainDatePast pattern YY
	timelib=-99	intl=-99
plainDatePast pattern YYYY
	timelib=-9999	intl=-9999
