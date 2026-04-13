--TEST--
DateTimeFormatter month patterns compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.inc';
include __DIR__ . '/include-intl.inc';

$patterns = [
    'M',
    'MM',
    'MMM',
    'MMMM',
    'MMMMM',
    'L',
    'LL',
    'LLL',
    'LLLL',
    'LLLLL',
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
];

$intl = new IntlDateFormatter(
    'en_US',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    'UTC',
    IntlDateFormatter::GREGORIAN,
);

foreach ($samples as $label => $sample) {
    $intlTimeZone = timezoneForIntl($sample);
    $intlTimestamp = timestampForIntl($sample);

    foreach ($patterns as $pattern) {
        $formatter = new time\DateTimeFormatter($pattern);
        $timeValue = $formatter->format($sample);

        $intl->setPattern($pattern);
        $intl->setTimeZone($intlTimeZone);
        $intlValue = $intl->format($intlTimestamp);

        echo "{$label} pattern {$pattern}\n";
        echo "\ttimelib={$timeValue}\tintl={$intlValue}\n";
    }
}

--EXPECT--
instant pattern M
	timelib=3	intl=3
instant pattern MM
	timelib=03	intl=03
instant pattern MMM
	timelib=Mar	intl=Mar
instant pattern MMMM
	timelib=March	intl=March
instant pattern MMMMM
	timelib=M	intl=M
instant pattern L
	timelib=3	intl=3
instant pattern LL
	timelib=03	intl=03
instant pattern LLL
	timelib=Mar	intl=Mar
instant pattern LLLL
	timelib=March	intl=March
instant pattern LLLLL
	timelib=M	intl=M
zdtNewYork pattern M
	timelib=3	intl=3
zdtNewYork pattern MM
	timelib=03	intl=03
zdtNewYork pattern MMM
	timelib=Mar	intl=Mar
zdtNewYork pattern MMMM
	timelib=March	intl=March
zdtNewYork pattern MMMMM
	timelib=M	intl=M
zdtNewYork pattern L
	timelib=3	intl=3
zdtNewYork pattern LL
	timelib=03	intl=03
zdtNewYork pattern LLL
	timelib=Mar	intl=Mar
zdtNewYork pattern LLLL
	timelib=March	intl=March
zdtNewYork pattern LLLLL
	timelib=M	intl=M
zdtKolkata pattern M
	timelib=3	intl=3
zdtKolkata pattern MM
	timelib=03	intl=03
zdtKolkata pattern MMM
	timelib=Mar	intl=Mar
zdtKolkata pattern MMMM
	timelib=March	intl=March
zdtKolkata pattern MMMMM
	timelib=M	intl=M
zdtKolkata pattern L
	timelib=3	intl=3
zdtKolkata pattern LL
	timelib=03	intl=03
zdtKolkata pattern LLL
	timelib=Mar	intl=Mar
zdtKolkata pattern LLLL
	timelib=March	intl=March
zdtKolkata pattern LLLLL
	timelib=M	intl=M
zdtKiritimati pattern M
	timelib=3	intl=3
zdtKiritimati pattern MM
	timelib=03	intl=03
zdtKiritimati pattern MMM
	timelib=Mar	intl=Mar
zdtKiritimati pattern MMMM
	timelib=March	intl=March
zdtKiritimati pattern MMMMM
	timelib=M	intl=M
zdtKiritimati pattern L
	timelib=3	intl=3
zdtKiritimati pattern LL
	timelib=03	intl=03
zdtKiritimati pattern LLL
	timelib=Mar	intl=Mar
zdtKiritimati pattern LLLL
	timelib=March	intl=March
zdtKiritimati pattern LLLLL
	timelib=M	intl=M
zdtBoundaryNewYork pattern M
	timelib=12	intl=12
zdtBoundaryNewYork pattern MM
	timelib=12	intl=12
zdtBoundaryNewYork pattern MMM
	timelib=Dec	intl=Dec
zdtBoundaryNewYork pattern MMMM
	timelib=December	intl=December
zdtBoundaryNewYork pattern MMMMM
	timelib=D	intl=D
zdtBoundaryNewYork pattern L
	timelib=12	intl=12
zdtBoundaryNewYork pattern LL
	timelib=12	intl=12
zdtBoundaryNewYork pattern LLL
	timelib=Dec	intl=Dec
zdtBoundaryNewYork pattern LLLL
	timelib=December	intl=December
zdtBoundaryNewYork pattern LLLLL
	timelib=D	intl=D
zdtBoundaryKiritimati pattern M
	timelib=2	intl=2
zdtBoundaryKiritimati pattern MM
	timelib=02	intl=02
zdtBoundaryKiritimati pattern MMM
	timelib=Feb	intl=Feb
zdtBoundaryKiritimati pattern MMMM
	timelib=February	intl=February
zdtBoundaryKiritimati pattern MMMMM
	timelib=F	intl=F
zdtBoundaryKiritimati pattern L
	timelib=2	intl=2
zdtBoundaryKiritimati pattern LL
	timelib=02	intl=02
zdtBoundaryKiritimati pattern LLL
	timelib=Feb	intl=Feb
zdtBoundaryKiritimati pattern LLLL
	timelib=February	intl=February
zdtBoundaryKiritimati pattern LLLLL
	timelib=F	intl=F
plainDateFuture pattern M
	timelib=12	intl=12
plainDateFuture pattern MM
	timelib=12	intl=12
plainDateFuture pattern MMM
	timelib=Dec	intl=Dec
plainDateFuture pattern MMMM
	timelib=December	intl=December
plainDateFuture pattern MMMMM
	timelib=D	intl=D
plainDateFuture pattern L
	timelib=12	intl=12
plainDateFuture pattern LL
	timelib=12	intl=12
plainDateFuture pattern LLL
	timelib=Dec	intl=Dec
plainDateFuture pattern LLLL
	timelib=December	intl=December
plainDateFuture pattern LLLLL
	timelib=D	intl=D
