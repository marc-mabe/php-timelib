--TEST--
DateTimeFormatter quarter symbols compared with IntlDateFormatter (en-US)
--SKIPIF--
<?php if (!class_exists('IntlDateFormatter')) die('skip, Intl extension required');
--FILE--
<?php

include __DIR__ . '/include.inc';
include __DIR__ . '/include-intl.inc';

$patterns = [
    'Q',
    'QQ',
    'QQQ',
    'QQQQ',
    'q',
    'qq',
    'qqq',
    'qqqq',
];

$gregorian = new time\GregorianCalendar(time\IsoDayOfWeek::Sunday, 1);
$julian = new time\JulianCalendar();
$hebrew = new time\HebrewCalendar();

$samples = [
    'instantLeap' => time\Instant::fromYmd(2024, 2, 29, 14, 30, 45, 123456789),
    'zdtGregorianQ4' => time\ZonedDateTime::fromInstant(
        time\Instant::fromYmd(2024, 12, 31, 14, 30, 45, 123456789),
        zone: time\Zone::fromIdentifier('America/New_York'),
        calendar: $gregorian,
    ),
    'zdtJulianQ2' => time\ZonedDateTime::fromYmd(2024, 5, 1, 10, 15,
        zone: time\Zone::fromIdentifier('Asia/Jerusalem'),
        calendar: $julian,
    ),
    'zdtHebrewQ4' => time\ZonedDateTime::fromYmd(3, 11, 1, 9,
        zone: time\Zone::fromIdentifier('Asia/Jerusalem'),
        calendar: $hebrew,
    ),
    'plainDateGregorianYear1' => time\PlainDate::fromYmd(1, 1, 1, $gregorian),
    'plainDateGregorianQ4' => time\PlainDate::fromYmd(2021, 12, 31, $gregorian),
    'plainDateJulianLeap' => time\PlainDate::fromYmd(2020, 2, 29, $julian),
    'plainDateHebrewLeap' => time\PlainDate::fromYmd(3, 6, 1, $hebrew),
];

$intl = [
    time\IsoCalendar::class => new IntlDateFormatter(
            'en_US',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            'UTC',
            IntlDateFormatter::TRADITIONAL,
    ),
    time\GregorianCalendar::class => new IntlDateFormatter(
        'en_US',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        'UTC',
        IntlDateFormatter::TRADITIONAL,
    ),
    time\JulianCalendar::class => new IntlDateFormatter(
        'en_US@calendar=julian',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        'UTC',
        IntlDateFormatter::TRADITIONAL,
    ),
    time\HebrewCalendar::class => new IntlDateFormatter(
        'en_US@calendar=hebrew',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        'UTC',
        IntlDateFormatter::TRADITIONAL,
    ),
];

foreach ($samples as $label => $sample) {
    $formatter = $intl[$sample->calendar::class];
    $intlTimeZone = timezoneForIntl($sample);
    $intlTimestamp = timestampForIntl($sample);
    echo "{$label} " . stringify($sample) . "\n";

    foreach ($patterns as $pattern) {
        $timelibFormatter = new time\DateTimeFormatter($pattern);
        $timeValue = $timelibFormatter->format($sample);

        $formatter->setPattern($pattern);
        $formatter->setTimeZone($intlTimeZone);
        $intlValue = $formatter->format($intlTimestamp);

        echo "\tpattern {$pattern}\ttimelib={$timeValue}\tintl={$intlValue}\n";
    }
}

--EXPECT--
instantLeap Instant('Thu 2024-02-29 14:30:45.123456789', 1709217045, 123456789)
	pattern Q	timelib=1	intl=1
	pattern QQ	timelib=01	intl=01
	pattern QQQ	timelib=Q1	intl=Q1
	pattern QQQQ	timelib=1st quarter	intl=1st quarter
	pattern q	timelib=1	intl=1
	pattern qq	timelib=01	intl=01
	pattern qqq	timelib=Q1	intl=Q1
	pattern qqqq	timelib=1st quarter	intl=1st quarter
zdtGregorianQ4 ZonedDateTime('Tue 2024-12-31 09:30:45.123456789 -05:00 [America/New_York]')
	pattern Q	timelib=4	intl=4
	pattern QQ	timelib=04	intl=04
	pattern QQQ	timelib=Q4	intl=Q4
	pattern QQQQ	timelib=4th quarter	intl=4th quarter
	pattern q	timelib=4	intl=4
	pattern qq	timelib=04	intl=04
	pattern qqq	timelib=Q4	intl=Q4
	pattern qqqq	timelib=4th quarter	intl=4th quarter
zdtJulianQ2 ZonedDateTime('Mon 2024-05-01 10:15:00 +03:00 [Asia/Jerusalem]')
	pattern Q	timelib=2	intl=2
	pattern QQ	timelib=02	intl=02
	pattern QQQ	timelib=Q2	intl=Q2
	pattern QQQQ	timelib=2nd quarter	intl=2nd quarter
	pattern q	timelib=2	intl=2
	pattern qq	timelib=02	intl=02
	pattern qqq	timelib=Q2	intl=Q2
	pattern qqqq	timelib=2nd quarter	intl=2nd quarter
zdtHebrewQ4 ZonedDateTime('Sat 3-11-01 09:00:00 +02:20:54 [Asia/Jerusalem]')
	pattern Q	timelib=4	intl=4
	pattern QQ	timelib=04	intl=04
	pattern QQQ	timelib=Q4	intl=Q4
	pattern QQQQ	timelib=4th quarter	intl=4th quarter
	pattern q	timelib=4	intl=4
	pattern qq	timelib=04	intl=04
	pattern qqq	timelib=Q4	intl=Q4
	pattern qqqq	timelib=4th quarter	intl=4th quarter
plainDateGregorianYear1 PlainDate('Mon 1-01-01')
	pattern Q	timelib=1	intl=1
	pattern QQ	timelib=01	intl=01
	pattern QQQ	timelib=Q1	intl=Q1
	pattern QQQQ	timelib=1st quarter	intl=1st quarter
	pattern q	timelib=1	intl=1
	pattern qq	timelib=01	intl=01
	pattern qqq	timelib=Q1	intl=Q1
	pattern qqqq	timelib=1st quarter	intl=1st quarter
plainDateGregorianQ4 PlainDate('Fri 2021-12-31')
	pattern Q	timelib=4	intl=4
	pattern QQ	timelib=04	intl=04
	pattern QQQ	timelib=Q4	intl=Q4
	pattern QQQQ	timelib=4th quarter	intl=4th quarter
	pattern q	timelib=4	intl=4
	pattern qq	timelib=04	intl=04
	pattern qqq	timelib=Q4	intl=Q4
	pattern qqqq	timelib=4th quarter	intl=4th quarter
plainDateJulianLeap PlainDate('Thu 2020-02-29')
	pattern Q	timelib=1	intl=1
	pattern QQ	timelib=01	intl=01
	pattern QQQ	timelib=Q1	intl=Q1
	pattern QQQQ	timelib=1st quarter	intl=1st quarter
	pattern q	timelib=1	intl=1
	pattern qq	timelib=01	intl=01
	pattern qqq	timelib=Q1	intl=Q1
	pattern qqqq	timelib=1st quarter	intl=1st quarter
plainDateHebrewLeap PlainDate('Fri 3-06-01')
	pattern Q	timelib=2	intl=2
	pattern QQ	timelib=02	intl=02
	pattern QQQ	timelib=Q2	intl=Q2
	pattern QQQQ	timelib=2nd quarter	intl=2nd quarter
	pattern q	timelib=2	intl=2
	pattern qq	timelib=02	intl=02
	pattern qqq	timelib=Q2	intl=Q2
	pattern qqqq	timelib=2nd quarter	intl=2nd quarter
