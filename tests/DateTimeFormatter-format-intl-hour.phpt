--TEST--
DateTimeFormatter hour symbols compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.php';
include __DIR__ . '/include-intl.php';

$patterns = [
    'H',
    'HH',
    'h',
    'hh',
    'K',
    'KK',
    'k',
    'kk',
];

$samples = [
    'instantMidnightUtc' => time\Instant::fromYmd(2024, 3, 15, 0, 0, 0, 0),
    'instantNoonUtc' => time\Instant::fromYmd(2024, 3, 15, 12, 0, 0, 0),
    'instant23h59Utc' => time\Instant::fromYmd(2024, 3, 15, 23, 59, 59, 999999999),
    'plainDateTimeNewYearEve' => time\PlainDateTime::fromYmd(2024, 12, 31, 23, 59, 59, 999999999),
    'plainDateTimeNoon' => time\PlainDateTime::fromYmd(2024, 3, 15, 12, 0, 0, 0),
    'plainTimeMidnight' => time\PlainTime::fromHms(0, 0, 0),
    'plainTimeNoon' => time\PlainTime::fromHms(12, 0, 0),
    'plainTimeEdgeEndOfDay' => time\PlainTime::fromHms(23, 59, 59, 999999999),
    'plainTimeEarlyMorning' => time\PlainTime::fromHms(5, 7, 8),
    'zdtKolkataNoon' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 12, 0, 0, 0),
        time\Zone::fromIdentifier('Asia/Kolkata'),
    ),
    'zdtKiritimatiNoon' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 12, 0, 0, 0),
        time\Zone::fromIdentifier('Pacific/Kiritimati'),
    ),
    'zdtNewYorkBeforeDstStart' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 10, 6, 30, 0, 0),
        time\Zone::fromIdentifier('America/New_York'),
    ),
    'zdtNewYorkAfterDstStart' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 10, 7, 30, 0, 0),
        time\Zone::fromIdentifier('America/New_York'),
    ),
    'zdtNewYorkBeforeDstEnd' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 11, 3, 5, 30, 0, 0),
        time\Zone::fromIdentifier('America/New_York'),
    ),
    'zdtNewYorkAfterDstEnd' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 11, 3, 6, 30, 0, 0),
        time\Zone::fromIdentifier('America/New_York'),
    ),
    'zdtFixedOffset' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 1, 15, 0, 0),
        time\Zone::fromIdentifier('+03:30'),
    ),
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
instantMidnightUtc pattern H
	timelib=0	intl=0
instantMidnightUtc pattern HH
	timelib=00	intl=00
instantMidnightUtc pattern h
	timelib=12	intl=12
instantMidnightUtc pattern hh
	timelib=12	intl=12
instantMidnightUtc pattern K
	timelib=0	intl=0
instantMidnightUtc pattern KK
	timelib=00	intl=00
instantMidnightUtc pattern k
	timelib=24	intl=24
instantMidnightUtc pattern kk
	timelib=24	intl=24
instantNoonUtc pattern H
	timelib=12	intl=12
instantNoonUtc pattern HH
	timelib=12	intl=12
instantNoonUtc pattern h
	timelib=12	intl=12
instantNoonUtc pattern hh
	timelib=12	intl=12
instantNoonUtc pattern K
	timelib=0	intl=0
instantNoonUtc pattern KK
	timelib=00	intl=00
instantNoonUtc pattern k
	timelib=12	intl=12
instantNoonUtc pattern kk
	timelib=12	intl=12
instant23h59Utc pattern H
	timelib=23	intl=23
instant23h59Utc pattern HH
	timelib=23	intl=23
instant23h59Utc pattern h
	timelib=11	intl=11
instant23h59Utc pattern hh
	timelib=11	intl=11
instant23h59Utc pattern K
	timelib=11	intl=11
instant23h59Utc pattern KK
	timelib=11	intl=11
instant23h59Utc pattern k
	timelib=23	intl=23
instant23h59Utc pattern kk
	timelib=23	intl=23
plainDateTimeNewYearEve pattern H
	timelib=23	intl=23
plainDateTimeNewYearEve pattern HH
	timelib=23	intl=23
plainDateTimeNewYearEve pattern h
	timelib=11	intl=11
plainDateTimeNewYearEve pattern hh
	timelib=11	intl=11
plainDateTimeNewYearEve pattern K
	timelib=11	intl=11
plainDateTimeNewYearEve pattern KK
	timelib=11	intl=11
plainDateTimeNewYearEve pattern k
	timelib=23	intl=23
plainDateTimeNewYearEve pattern kk
	timelib=23	intl=23
plainDateTimeNoon pattern H
	timelib=12	intl=12
plainDateTimeNoon pattern HH
	timelib=12	intl=12
plainDateTimeNoon pattern h
	timelib=12	intl=12
plainDateTimeNoon pattern hh
	timelib=12	intl=12
plainDateTimeNoon pattern K
	timelib=0	intl=0
plainDateTimeNoon pattern KK
	timelib=00	intl=00
plainDateTimeNoon pattern k
	timelib=12	intl=12
plainDateTimeNoon pattern kk
	timelib=12	intl=12
plainTimeMidnight pattern H
	timelib=0	intl=0
plainTimeMidnight pattern HH
	timelib=00	intl=00
plainTimeMidnight pattern h
	timelib=12	intl=12
plainTimeMidnight pattern hh
	timelib=12	intl=12
plainTimeMidnight pattern K
	timelib=0	intl=0
plainTimeMidnight pattern KK
	timelib=00	intl=00
plainTimeMidnight pattern k
	timelib=24	intl=24
plainTimeMidnight pattern kk
	timelib=24	intl=24
plainTimeNoon pattern H
	timelib=12	intl=12
plainTimeNoon pattern HH
	timelib=12	intl=12
plainTimeNoon pattern h
	timelib=12	intl=12
plainTimeNoon pattern hh
	timelib=12	intl=12
plainTimeNoon pattern K
	timelib=0	intl=0
plainTimeNoon pattern KK
	timelib=00	intl=00
plainTimeNoon pattern k
	timelib=12	intl=12
plainTimeNoon pattern kk
	timelib=12	intl=12
plainTimeEdgeEndOfDay pattern H
	timelib=23	intl=23
plainTimeEdgeEndOfDay pattern HH
	timelib=23	intl=23
plainTimeEdgeEndOfDay pattern h
	timelib=11	intl=11
plainTimeEdgeEndOfDay pattern hh
	timelib=11	intl=11
plainTimeEdgeEndOfDay pattern K
	timelib=11	intl=11
plainTimeEdgeEndOfDay pattern KK
	timelib=11	intl=11
plainTimeEdgeEndOfDay pattern k
	timelib=23	intl=23
plainTimeEdgeEndOfDay pattern kk
	timelib=23	intl=23
plainTimeEarlyMorning pattern H
	timelib=5	intl=5
plainTimeEarlyMorning pattern HH
	timelib=05	intl=05
plainTimeEarlyMorning pattern h
	timelib=5	intl=5
plainTimeEarlyMorning pattern hh
	timelib=05	intl=05
plainTimeEarlyMorning pattern K
	timelib=5	intl=5
plainTimeEarlyMorning pattern KK
	timelib=05	intl=05
plainTimeEarlyMorning pattern k
	timelib=5	intl=5
plainTimeEarlyMorning pattern kk
	timelib=05	intl=05
zdtKolkataNoon pattern H
	timelib=17	intl=17
zdtKolkataNoon pattern HH
	timelib=17	intl=17
zdtKolkataNoon pattern h
	timelib=5	intl=5
zdtKolkataNoon pattern hh
	timelib=05	intl=05
zdtKolkataNoon pattern K
	timelib=5	intl=5
zdtKolkataNoon pattern KK
	timelib=05	intl=05
zdtKolkataNoon pattern k
	timelib=17	intl=17
zdtKolkataNoon pattern kk
	timelib=17	intl=17
zdtKiritimatiNoon pattern H
	timelib=2	intl=2
zdtKiritimatiNoon pattern HH
	timelib=02	intl=02
zdtKiritimatiNoon pattern h
	timelib=2	intl=2
zdtKiritimatiNoon pattern hh
	timelib=02	intl=02
zdtKiritimatiNoon pattern K
	timelib=2	intl=2
zdtKiritimatiNoon pattern KK
	timelib=02	intl=02
zdtKiritimatiNoon pattern k
	timelib=2	intl=2
zdtKiritimatiNoon pattern kk
	timelib=02	intl=02
zdtNewYorkBeforeDstStart pattern H
	timelib=1	intl=1
zdtNewYorkBeforeDstStart pattern HH
	timelib=01	intl=01
zdtNewYorkBeforeDstStart pattern h
	timelib=1	intl=1
zdtNewYorkBeforeDstStart pattern hh
	timelib=01	intl=01
zdtNewYorkBeforeDstStart pattern K
	timelib=1	intl=1
zdtNewYorkBeforeDstStart pattern KK
	timelib=01	intl=01
zdtNewYorkBeforeDstStart pattern k
	timelib=1	intl=1
zdtNewYorkBeforeDstStart pattern kk
	timelib=01	intl=01
zdtNewYorkAfterDstStart pattern H
	timelib=3	intl=3
zdtNewYorkAfterDstStart pattern HH
	timelib=03	intl=03
zdtNewYorkAfterDstStart pattern h
	timelib=3	intl=3
zdtNewYorkAfterDstStart pattern hh
	timelib=03	intl=03
zdtNewYorkAfterDstStart pattern K
	timelib=3	intl=3
zdtNewYorkAfterDstStart pattern KK
	timelib=03	intl=03
zdtNewYorkAfterDstStart pattern k
	timelib=3	intl=3
zdtNewYorkAfterDstStart pattern kk
	timelib=03	intl=03
zdtNewYorkBeforeDstEnd pattern H
	timelib=1	intl=1
zdtNewYorkBeforeDstEnd pattern HH
	timelib=01	intl=01
zdtNewYorkBeforeDstEnd pattern h
	timelib=1	intl=1
zdtNewYorkBeforeDstEnd pattern hh
	timelib=01	intl=01
zdtNewYorkBeforeDstEnd pattern K
	timelib=1	intl=1
zdtNewYorkBeforeDstEnd pattern KK
	timelib=01	intl=01
zdtNewYorkBeforeDstEnd pattern k
	timelib=1	intl=1
zdtNewYorkBeforeDstEnd pattern kk
	timelib=01	intl=01
zdtNewYorkAfterDstEnd pattern H
	timelib=1	intl=1
zdtNewYorkAfterDstEnd pattern HH
	timelib=01	intl=01
zdtNewYorkAfterDstEnd pattern h
	timelib=1	intl=1
zdtNewYorkAfterDstEnd pattern hh
	timelib=01	intl=01
zdtNewYorkAfterDstEnd pattern K
	timelib=1	intl=1
zdtNewYorkAfterDstEnd pattern KK
	timelib=01	intl=01
zdtNewYorkAfterDstEnd pattern k
	timelib=1	intl=1
zdtNewYorkAfterDstEnd pattern kk
	timelib=01	intl=01
zdtFixedOffset pattern H
	timelib=4	intl=4
zdtFixedOffset pattern HH
	timelib=04	intl=04
zdtFixedOffset pattern h
	timelib=4	intl=4
zdtFixedOffset pattern hh
	timelib=04	intl=04
zdtFixedOffset pattern K
	timelib=4	intl=4
zdtFixedOffset pattern KK
	timelib=04	intl=04
zdtFixedOffset pattern k
	timelib=4	intl=4
zdtFixedOffset pattern kk
	timelib=04	intl=04
