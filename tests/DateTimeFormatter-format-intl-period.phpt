--TEST--
DateTimeFormatter period symbols compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.inc';
include __DIR__ . '/include-intl.inc';

$patterns = [
    'a',
    'aa',
    'aaa',
    'aaaa',
    'aaaaa',
    'b',
    'bb',
    'bbb',
    'bbbb',
    'bbbbb',
    'B',
    'BB',
    'BBB',
    'BBBB',
    'BBBBB',
];

$samples = [
    'instantMidnight' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 0, 0, 0),
        time\Zone::fromIdentifier('UTC'),
    ),
    'instantBoundaryMidnightPlusOne' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 0, 0, 1),
        time\Zone::fromIdentifier('UTC'),
    ),
    'instantBeforeNoon' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 11, 59, 59),
        time\Zone::fromIdentifier('UTC'),
    ),
    'instantNoonExact' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 12, 0, 0),
        time\Zone::fromIdentifier('UTC'),
    ),
    'instantEvening' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 18, 0, 0),
        time\Zone::fromIdentifier('UTC'),
    ),
    'instantNewYorkEveningFromUtcMidnight' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 0, 0, 1),
        time\Zone::fromIdentifier('America/New_York'),
    ),
    'instantKolkataMorningFromUtcMidnight' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 0, 0, 1),
        time\Zone::fromIdentifier('Asia/Kolkata'),
    ),
    'instantKiritimatiAfternoonFromUtcEvening' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 3, 15, 23, 30, 0),
        time\Zone::fromIdentifier('Pacific/Kiritimati'),
    ),
    'plainTimeFutureLate' => time\PlainTime::fromHms(23, 59, 0),
    'plainTimePastNoon' => time\PlainTime::fromHms(12, 0, 0),
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
instantMidnight pattern a
	timelib=AM	intl=AM
instantMidnight pattern aa
	timelib=AM	intl=AM
instantMidnight pattern aaa
	timelib=AM	intl=AM
instantMidnight pattern aaaa
	timelib=AM	intl=AM
instantMidnight pattern aaaaa
	timelib=a	intl=a
instantMidnight pattern b
	timelib=AM	intl=AM
instantMidnight pattern bb
	timelib=AM	intl=AM
instantMidnight pattern bbb
	timelib=AM	intl=AM
instantMidnight pattern bbbb
	timelib=AM	intl=AM
instantMidnight pattern bbbbb
	timelib=a	intl=a
instantMidnight pattern B
	timelib=at night	intl=at night
instantMidnight pattern BB
	timelib=at night	intl=at night
instantMidnight pattern BBB
	timelib=at night	intl=at night
instantMidnight pattern BBBB
	timelib=at night	intl=at night
instantMidnight pattern BBBBB
	timelib=at night	intl=at night
instantBoundaryMidnightPlusOne pattern a
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern aa
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern aaa
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern aaaa
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern aaaaa
	timelib=a	intl=a
instantBoundaryMidnightPlusOne pattern b
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern bb
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern bbb
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern bbbb
	timelib=AM	intl=AM
instantBoundaryMidnightPlusOne pattern bbbbb
	timelib=a	intl=a
instantBoundaryMidnightPlusOne pattern B
	timelib=at night	intl=at night
instantBoundaryMidnightPlusOne pattern BB
	timelib=at night	intl=at night
instantBoundaryMidnightPlusOne pattern BBB
	timelib=at night	intl=at night
instantBoundaryMidnightPlusOne pattern BBBB
	timelib=at night	intl=at night
instantBoundaryMidnightPlusOne pattern BBBBB
	timelib=at night	intl=at night
instantBeforeNoon pattern a
	timelib=AM	intl=AM
instantBeforeNoon pattern aa
	timelib=AM	intl=AM
instantBeforeNoon pattern aaa
	timelib=AM	intl=AM
instantBeforeNoon pattern aaaa
	timelib=AM	intl=AM
instantBeforeNoon pattern aaaaa
	timelib=a	intl=a
instantBeforeNoon pattern b
	timelib=AM	intl=AM
instantBeforeNoon pattern bb
	timelib=AM	intl=AM
instantBeforeNoon pattern bbb
	timelib=AM	intl=AM
instantBeforeNoon pattern bbbb
	timelib=AM	intl=AM
instantBeforeNoon pattern bbbbb
	timelib=a	intl=a
instantBeforeNoon pattern B
	timelib=in the morning	intl=in the morning
instantBeforeNoon pattern BB
	timelib=in the morning	intl=in the morning
instantBeforeNoon pattern BBB
	timelib=in the morning	intl=in the morning
instantBeforeNoon pattern BBBB
	timelib=in the morning	intl=in the morning
instantBeforeNoon pattern BBBBB
	timelib=in the morning	intl=in the morning
instantNoonExact pattern a
	timelib=PM	intl=PM
instantNoonExact pattern aa
	timelib=PM	intl=PM
instantNoonExact pattern aaa
	timelib=PM	intl=PM
instantNoonExact pattern aaaa
	timelib=PM	intl=PM
instantNoonExact pattern aaaaa
	timelib=p	intl=p
instantNoonExact pattern b
	timelib=noon	intl=noon
instantNoonExact pattern bb
	timelib=noon	intl=noon
instantNoonExact pattern bbb
	timelib=noon	intl=noon
instantNoonExact pattern bbbb
	timelib=noon	intl=noon
instantNoonExact pattern bbbbb
	timelib=n	intl=n
instantNoonExact pattern B
	timelib=noon	intl=noon
instantNoonExact pattern BB
	timelib=noon	intl=noon
instantNoonExact pattern BBB
	timelib=noon	intl=noon
instantNoonExact pattern BBBB
	timelib=noon	intl=noon
instantNoonExact pattern BBBBB
	timelib=n	intl=n
instantEvening pattern a
	timelib=PM	intl=PM
instantEvening pattern aa
	timelib=PM	intl=PM
instantEvening pattern aaa
	timelib=PM	intl=PM
instantEvening pattern aaaa
	timelib=PM	intl=PM
instantEvening pattern aaaaa
	timelib=p	intl=p
instantEvening pattern b
	timelib=PM	intl=PM
instantEvening pattern bb
	timelib=PM	intl=PM
instantEvening pattern bbb
	timelib=PM	intl=PM
instantEvening pattern bbbb
	timelib=PM	intl=PM
instantEvening pattern bbbbb
	timelib=p	intl=p
instantEvening pattern B
	timelib=in the evening	intl=in the evening
instantEvening pattern BB
	timelib=in the evening	intl=in the evening
instantEvening pattern BBB
	timelib=in the evening	intl=in the evening
instantEvening pattern BBBB
	timelib=in the evening	intl=in the evening
instantEvening pattern BBBBB
	timelib=in the evening	intl=in the evening
instantNewYorkEveningFromUtcMidnight pattern a
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern aa
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern aaa
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern aaaa
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern aaaaa
	timelib=p	intl=p
instantNewYorkEveningFromUtcMidnight pattern b
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern bb
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern bbb
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern bbbb
	timelib=PM	intl=PM
instantNewYorkEveningFromUtcMidnight pattern bbbbb
	timelib=p	intl=p
instantNewYorkEveningFromUtcMidnight pattern B
	timelib=in the evening	intl=in the evening
instantNewYorkEveningFromUtcMidnight pattern BB
	timelib=in the evening	intl=in the evening
instantNewYorkEveningFromUtcMidnight pattern BBB
	timelib=in the evening	intl=in the evening
instantNewYorkEveningFromUtcMidnight pattern BBBB
	timelib=in the evening	intl=in the evening
instantNewYorkEveningFromUtcMidnight pattern BBBBB
	timelib=in the evening	intl=in the evening
instantKolkataMorningFromUtcMidnight pattern a
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern aa
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern aaa
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern aaaa
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern aaaaa
	timelib=a	intl=a
instantKolkataMorningFromUtcMidnight pattern b
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern bb
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern bbb
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern bbbb
	timelib=AM	intl=AM
instantKolkataMorningFromUtcMidnight pattern bbbbb
	timelib=a	intl=a
instantKolkataMorningFromUtcMidnight pattern B
	timelib=at night	intl=at night
instantKolkataMorningFromUtcMidnight pattern BB
	timelib=at night	intl=at night
instantKolkataMorningFromUtcMidnight pattern BBB
	timelib=at night	intl=at night
instantKolkataMorningFromUtcMidnight pattern BBBB
	timelib=at night	intl=at night
instantKolkataMorningFromUtcMidnight pattern BBBBB
	timelib=at night	intl=at night
instantKiritimatiAfternoonFromUtcEvening pattern a
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern aa
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern aaa
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern aaaa
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern aaaaa
	timelib=p	intl=p
instantKiritimatiAfternoonFromUtcEvening pattern b
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern bb
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern bbb
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern bbbb
	timelib=PM	intl=PM
instantKiritimatiAfternoonFromUtcEvening pattern bbbbb
	timelib=p	intl=p
instantKiritimatiAfternoonFromUtcEvening pattern B
	timelib=in the afternoon	intl=in the afternoon
instantKiritimatiAfternoonFromUtcEvening pattern BB
	timelib=in the afternoon	intl=in the afternoon
instantKiritimatiAfternoonFromUtcEvening pattern BBB
	timelib=in the afternoon	intl=in the afternoon
instantKiritimatiAfternoonFromUtcEvening pattern BBBB
	timelib=in the afternoon	intl=in the afternoon
instantKiritimatiAfternoonFromUtcEvening pattern BBBBB
	timelib=in the afternoon	intl=in the afternoon
plainTimeFutureLate pattern a
	timelib=PM	intl=PM
plainTimeFutureLate pattern aa
	timelib=PM	intl=PM
plainTimeFutureLate pattern aaa
	timelib=PM	intl=PM
plainTimeFutureLate pattern aaaa
	timelib=PM	intl=PM
plainTimeFutureLate pattern aaaaa
	timelib=p	intl=p
plainTimeFutureLate pattern b
	timelib=PM	intl=PM
plainTimeFutureLate pattern bb
	timelib=PM	intl=PM
plainTimeFutureLate pattern bbb
	timelib=PM	intl=PM
plainTimeFutureLate pattern bbbb
	timelib=PM	intl=PM
plainTimeFutureLate pattern bbbbb
	timelib=p	intl=p
plainTimeFutureLate pattern B
	timelib=at night	intl=at night
plainTimeFutureLate pattern BB
	timelib=at night	intl=at night
plainTimeFutureLate pattern BBB
	timelib=at night	intl=at night
plainTimeFutureLate pattern BBBB
	timelib=at night	intl=at night
plainTimeFutureLate pattern BBBBB
	timelib=at night	intl=at night
plainTimePastNoon pattern a
	timelib=PM	intl=PM
plainTimePastNoon pattern aa
	timelib=PM	intl=PM
plainTimePastNoon pattern aaa
	timelib=PM	intl=PM
plainTimePastNoon pattern aaaa
	timelib=PM	intl=PM
plainTimePastNoon pattern aaaaa
	timelib=p	intl=p
plainTimePastNoon pattern b
	timelib=noon	intl=noon
plainTimePastNoon pattern bb
	timelib=noon	intl=noon
plainTimePastNoon pattern bbb
	timelib=noon	intl=noon
plainTimePastNoon pattern bbbb
	timelib=noon	intl=noon
plainTimePastNoon pattern bbbbb
	timelib=n	intl=n
plainTimePastNoon pattern B
	timelib=noon	intl=noon
plainTimePastNoon pattern BB
	timelib=noon	intl=noon
plainTimePastNoon pattern BBB
	timelib=noon	intl=noon
plainTimePastNoon pattern BBBB
	timelib=noon	intl=noon
plainTimePastNoon pattern BBBBB
	timelib=n	intl=n
