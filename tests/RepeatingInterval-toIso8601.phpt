--TEST--
RepeatingInterval->toIso8601()
--FILE--
<?php

include __DIR__ . '/include.php';

$intervals = [
    'endless repetitions' => new time\RepeatingInterval(
        repetitions: null,
        start: time\Instant::fromYmd(2000, 1, 1),
        durationOrPeriod: new \time\Duration(seconds: 123),
    ),
    'no repetitions' => new time\RepeatingInterval(
        repetitions: 0,
        start: time\Instant::fromYmd(2000, 1, 1),
        durationOrPeriod: new \time\Duration(seconds: 123),
    ),
    'Instant + Duration' => new time\RepeatingInterval(
        repetitions: 12,
        start: time\Instant::fromYmd(2000, 1, 1),
        durationOrPeriod: new \time\Duration(seconds: 123),
    ),
    'ZonedDateTime + Period' => new time\RepeatingInterval(
        repetitions: 12,
        start: time\ZonedDateTime::fromYmd(2000, 1, 1, zone: time\Zone::fromIdentifier('Europe/Berlin')),
        durationOrPeriod: new \time\Period(years: 1, months: 2, days: 3, hours: 4),
    ),
    'ZonedDateTime + negative Period' => new time\RepeatingInterval(
        repetitions: 12,
        start: time\ZonedDateTime::fromYmd(2000, 1, 1, zone: time\Zone::fromIdentifier('Europe/Berlin')),
        durationOrPeriod: new \time\Period(isNegative: true, years: 1, months: 2, days: 3, hours: 4),
    ),
];

foreach ($intervals as $label => $interval) {
    echo $label . "\n";
    echo '  ' . $interval->toIso8601(separator: '/') . "\n";
    echo '  ' . $interval->toIso8601(separator: '--') . "\n";
}

--EXPECT--
endless repetitions
  R/2000-01-01T00:00:00Z/PT2M3S
  R--2000-01-01T00:00:00Z--PT2M3S
no repetitions
  R0/2000-01-01T00:00:00Z/PT2M3S
  R0--2000-01-01T00:00:00Z--PT2M3S
Instant + Duration
  R12/2000-01-01T00:00:00Z/PT2M3S
  R12--2000-01-01T00:00:00Z--PT2M3S
ZonedDateTime + Period
  R12/2000-01-01T00:00:00+01:00/P1Y2M3DT4H
  R12--2000-01-01T00:00:00+01:00--P1Y2M3DT4H
ZonedDateTime + negative Period
  R12/P1Y2M3DT4H/2000-01-01T00:00:00+01:00
  R12--P1Y2M3DT4H--2000-01-01T00:00:00+01:00
