--TEST--
RepeatingInterval->end
--FILE--
<?php

include __DIR__ . '/include.php';

$intervals = [
    new time\RepeatingInterval(
        repetitions: null,
        start: time\Instant::fromYmd(2000, 1, 1),
        durationOrPeriod: new \time\Duration(seconds: 123, milliseconds: 700),
    ),
    new time\RepeatingInterval(
        repetitions: 0,
        start: time\Instant::fromYmd(2000, 1, 1),
        durationOrPeriod: new \time\Duration(seconds: 123, milliseconds: 700),
    ),
    new time\RepeatingInterval(
        repetitions: 12,
        start: time\Instant::fromYmd(2000, 1, 1),
        durationOrPeriod: new \time\Duration(seconds: 123, milliseconds: 700),
    ),
    new time\RepeatingInterval(
        repetitions: 12,
        start: time\ZonedDateTime::fromYmd(2000, 1, 1, zone: time\Zone::fromIdentifier('Europe/Berlin')),
        durationOrPeriod: new \time\Period(years: 1, months: 2, days: 3, hours: 3, milliseconds: 700),
    ),
    new time\RepeatingInterval(
        repetitions: 12,
        start: time\ZonedDateTime::fromYmd(2000, 1, 1, zone: time\Zone::fromIdentifier('Europe/Berlin')),
        durationOrPeriod: new \time\Period(isNegative: true, years: 1, months: 2, days: 3, hours: 3, milliseconds: 700),
    ),
];

foreach ($intervals as $interval) {
    echo stringify($interval) . ', end:' . stringify($interval->end) . "\n";
}

--EXPECT--
RepeatingInterval('R/2000-01-01T00:00:00Z/PT2M3.7S'), end:NULL
RepeatingInterval('R0/2000-01-01T00:00:00Z/PT2M3.7S'), end:Instant('Sat 2000-01-01 00:00:00', 946684800, 0)
RepeatingInterval('R12/2000-01-01T00:00:00Z/PT2M3.7S'), end:Instant('Sat 2000-01-01 00:24:44.4', 946686284, 400000000)
RepeatingInterval('R12/2000-01-01T00:00:00+01:00/P1Y2M3DT3H0.7S'), end:ZonedDateTime('Sat 2014-02-08 12:00:08.4 +01:00 [Europe/Berlin]')
RepeatingInterval('R12/P1Y2M3DT3H0.7S/2000-01-01T00:00:00+01:00'), end:ZonedDateTime('Mon 1985-11-25 11:59:51.6 +01:00 [Europe/Berlin]')
