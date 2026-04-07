--TEST--
DateTimeFormatter weekday and local-day-of-week symbols compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.php';

$patterns = [
    'E',
    'EE',
    'EEE',
    'EEEE',
    'EEEEE',
    'e',
    'ee',
    'eee',
    'eeee',
    'eeeee',
    'c',
    'cc',
    'ccc',
    'cccc',
    'ccccc',
];

$gregorianUs = new time\GregorianCalendar(7, 1);

$samples = [
    'zdtUtc' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789),
        time\Zone::fromIdentifier('UTC'),
        $gregorianUs,
    ),
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

function timestampForIntl(time\Date $date): int|float
{
    if ($date instanceof time\Instanted) {
        try {
            return $date->toUnixTimestampTuple()[0];
        } catch (Throwable) {
            // Fallback to calendar-based conversion for non-32-bit-safe inputs.
        }
    }

    $days = $date->calendar->getDaysSinceUnixEpochByYmd($date->year, $date->month, $date->dayOfMonth);
    $timestamp = $days * 86400;

    if ($date instanceof time\Time) {
        $timestamp += ($date->hour * 3600) + ($date->minute * 60) + $date->second;
    }

    if ($date instanceof time\ZonedDateTime) {
        $timestamp -= $date->offset->totalSeconds;
    }

    return $timestamp;
}

function timezoneForIntl(time\Date $date): string
{
    return match (true) {
        $date instanceof time\ZonedDateTime => $date->zone->identifier,
        default => 'UTC',
    };
}

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
zdtUtc pattern E
	timelib=Fri	intl=Fri
zdtUtc pattern EE
	timelib=Fri	intl=Fri
zdtUtc pattern EEE
	timelib=Fri	intl=Fri
zdtUtc pattern EEEE
	timelib=Friday	intl=Friday
zdtUtc pattern EEEEE
	timelib=F	intl=F
zdtUtc pattern e
	timelib=6	intl=6
zdtUtc pattern ee
	timelib=06	intl=06
zdtUtc pattern eee
	timelib=Fri	intl=Fri
zdtUtc pattern eeee
	timelib=Friday	intl=Friday
zdtUtc pattern eeeee
	timelib=F	intl=F
zdtUtc pattern c
	timelib=6	intl=6
zdtUtc pattern cc
	timelib=6	intl=6
zdtUtc pattern ccc
	timelib=Fri	intl=Fri
zdtUtc pattern cccc
	timelib=Friday	intl=Friday
zdtUtc pattern ccccc
	timelib=F	intl=F
zdtNewYork pattern E
	timelib=Fri	intl=Fri
zdtNewYork pattern EE
	timelib=Fri	intl=Fri
zdtNewYork pattern EEE
	timelib=Fri	intl=Fri
zdtNewYork pattern EEEE
	timelib=Friday	intl=Friday
zdtNewYork pattern EEEEE
	timelib=F	intl=F
zdtNewYork pattern e
	timelib=6	intl=6
zdtNewYork pattern ee
	timelib=06	intl=06
zdtNewYork pattern eee
	timelib=Fri	intl=Fri
zdtNewYork pattern eeee
	timelib=Friday	intl=Friday
zdtNewYork pattern eeeee
	timelib=F	intl=F
zdtNewYork pattern c
	timelib=6	intl=6
zdtNewYork pattern cc
	timelib=6	intl=6
zdtNewYork pattern ccc
	timelib=Fri	intl=Fri
zdtNewYork pattern cccc
	timelib=Friday	intl=Friday
zdtNewYork pattern ccccc
	timelib=F	intl=F
zdtKolkata pattern E
	timelib=Fri	intl=Fri
zdtKolkata pattern EE
	timelib=Fri	intl=Fri
zdtKolkata pattern EEE
	timelib=Fri	intl=Fri
zdtKolkata pattern EEEE
	timelib=Friday	intl=Friday
zdtKolkata pattern EEEEE
	timelib=F	intl=F
zdtKolkata pattern e
	timelib=6	intl=6
zdtKolkata pattern ee
	timelib=06	intl=06
zdtKolkata pattern eee
	timelib=Fri	intl=Fri
zdtKolkata pattern eeee
	timelib=Friday	intl=Friday
zdtKolkata pattern eeeee
	timelib=F	intl=F
zdtKolkata pattern c
	timelib=6	intl=6
zdtKolkata pattern cc
	timelib=6	intl=6
zdtKolkata pattern ccc
	timelib=Fri	intl=Fri
zdtKolkata pattern cccc
	timelib=Friday	intl=Friday
zdtKolkata pattern ccccc
	timelib=F	intl=F
zdtKiritimati pattern E
	timelib=Sat	intl=Sat
zdtKiritimati pattern EE
	timelib=Sat	intl=Sat
zdtKiritimati pattern EEE
	timelib=Sat	intl=Sat
zdtKiritimati pattern EEEE
	timelib=Saturday	intl=Saturday
zdtKiritimati pattern EEEEE
	timelib=S	intl=S
zdtKiritimati pattern e
	timelib=7	intl=7
zdtKiritimati pattern ee
	timelib=07	intl=07
zdtKiritimati pattern eee
	timelib=Sat	intl=Sat
zdtKiritimati pattern eeee
	timelib=Saturday	intl=Saturday
zdtKiritimati pattern eeeee
	timelib=S	intl=S
zdtKiritimati pattern c
	timelib=7	intl=7
zdtKiritimati pattern cc
	timelib=7	intl=7
zdtKiritimati pattern ccc
	timelib=Sat	intl=Sat
zdtKiritimati pattern cccc
	timelib=Saturday	intl=Saturday
zdtKiritimati pattern ccccc
	timelib=S	intl=S
zdtBoundaryNewYork pattern E
	timelib=Sun	intl=Sun
zdtBoundaryNewYork pattern EE
	timelib=Sun	intl=Sun
zdtBoundaryNewYork pattern EEE
	timelib=Sun	intl=Sun
zdtBoundaryNewYork pattern EEEE
	timelib=Sunday	intl=Sunday
zdtBoundaryNewYork pattern EEEEE
	timelib=S	intl=S
zdtBoundaryNewYork pattern e
	timelib=1	intl=1
zdtBoundaryNewYork pattern ee
	timelib=01	intl=01
zdtBoundaryNewYork pattern eee
	timelib=Sun	intl=Sun
zdtBoundaryNewYork pattern eeee
	timelib=Sunday	intl=Sunday
zdtBoundaryNewYork pattern eeeee
	timelib=S	intl=S
zdtBoundaryNewYork pattern c
	timelib=1	intl=1
zdtBoundaryNewYork pattern cc
	timelib=1	intl=1
zdtBoundaryNewYork pattern ccc
	timelib=Sun	intl=Sun
zdtBoundaryNewYork pattern cccc
	timelib=Sunday	intl=Sunday
zdtBoundaryNewYork pattern ccccc
	timelib=S	intl=S
zdtBoundaryKiritimati pattern E
	timelib=Thu	intl=Thu
zdtBoundaryKiritimati pattern EE
	timelib=Thu	intl=Thu
zdtBoundaryKiritimati pattern EEE
	timelib=Thu	intl=Thu
zdtBoundaryKiritimati pattern EEEE
	timelib=Thursday	intl=Thursday
zdtBoundaryKiritimati pattern EEEEE
	timelib=T	intl=T
zdtBoundaryKiritimati pattern e
	timelib=5	intl=5
zdtBoundaryKiritimati pattern ee
	timelib=05	intl=05
zdtBoundaryKiritimati pattern eee
	timelib=Thu	intl=Thu
zdtBoundaryKiritimati pattern eeee
	timelib=Thursday	intl=Thursday
zdtBoundaryKiritimati pattern eeeee
	timelib=T	intl=T
zdtBoundaryKiritimati pattern c
	timelib=5	intl=5
zdtBoundaryKiritimati pattern cc
	timelib=5	intl=5
zdtBoundaryKiritimati pattern ccc
	timelib=Thu	intl=Thu
zdtBoundaryKiritimati pattern cccc
	timelib=Thursday	intl=Thursday
zdtBoundaryKiritimati pattern ccccc
	timelib=T	intl=T
plainDateFuture pattern E
	timelib=Sun	intl=Sun
plainDateFuture pattern EE
	timelib=Sun	intl=Sun
plainDateFuture pattern EEE
	timelib=Sun	intl=Sun
plainDateFuture pattern EEEE
	timelib=Sunday	intl=Sunday
plainDateFuture pattern EEEEE
	timelib=S	intl=S
plainDateFuture pattern e
	timelib=1	intl=1
plainDateFuture pattern ee
	timelib=01	intl=01
plainDateFuture pattern eee
	timelib=Sun	intl=Sun
plainDateFuture pattern eeee
	timelib=Sunday	intl=Sunday
plainDateFuture pattern eeeee
	timelib=S	intl=S
plainDateFuture pattern c
	timelib=1	intl=1
plainDateFuture pattern cc
	timelib=1	intl=1
plainDateFuture pattern ccc
	timelib=Sun	intl=Sun
plainDateFuture pattern cccc
	timelib=Sunday	intl=Sunday
plainDateFuture pattern ccccc
	timelib=S	intl=S
plainDateLeapDay pattern E
	timelib=Thu	intl=Thu
plainDateLeapDay pattern EE
	timelib=Thu	intl=Thu
plainDateLeapDay pattern EEE
	timelib=Thu	intl=Thu
plainDateLeapDay pattern EEEE
	timelib=Thursday	intl=Thursday
plainDateLeapDay pattern EEEEE
	timelib=T	intl=T
plainDateLeapDay pattern e
	timelib=5	intl=5
plainDateLeapDay pattern ee
	timelib=05	intl=05
plainDateLeapDay pattern eee
	timelib=Thu	intl=Thu
plainDateLeapDay pattern eeee
	timelib=Thursday	intl=Thursday
plainDateLeapDay pattern eeeee
	timelib=T	intl=T
plainDateLeapDay pattern c
	timelib=5	intl=5
plainDateLeapDay pattern cc
	timelib=5	intl=5
plainDateLeapDay pattern ccc
	timelib=Thu	intl=Thu
plainDateLeapDay pattern cccc
	timelib=Thursday	intl=Thursday
plainDateLeapDay pattern ccccc
	timelib=T	intl=T
plainDateMonthStart pattern E
	timelib=Fri	intl=Fri
plainDateMonthStart pattern EE
	timelib=Fri	intl=Fri
plainDateMonthStart pattern EEE
	timelib=Fri	intl=Fri
plainDateMonthStart pattern EEEE
	timelib=Friday	intl=Friday
plainDateMonthStart pattern EEEEE
	timelib=F	intl=F
plainDateMonthStart pattern e
	timelib=6	intl=6
plainDateMonthStart pattern ee
	timelib=06	intl=06
plainDateMonthStart pattern eee
	timelib=Fri	intl=Fri
plainDateMonthStart pattern eeee
	timelib=Friday	intl=Friday
plainDateMonthStart pattern eeeee
	timelib=F	intl=F
plainDateMonthStart pattern c
	timelib=6	intl=6
plainDateMonthStart pattern cc
	timelib=6	intl=6
plainDateMonthStart pattern ccc
	timelib=Fri	intl=Fri
plainDateMonthStart pattern cccc
	timelib=Friday	intl=Friday
plainDateMonthStart pattern ccccc
	timelib=F	intl=F
