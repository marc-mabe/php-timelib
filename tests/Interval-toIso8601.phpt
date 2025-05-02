--TEST--
Interval->toIso8601()
--FILE--
<?php

include __DIR__ . '/include.php';

$intervals = [
    new time\Interval(
        time\Instant::fromYmd(2000, 1, 1),
        time\Instant::fromYmd(2000, 1, 2),
        time\Boundary::InclusiveToExclusive,
    ),
    new time\Interval(
        time\Instant::fromYmd(2000, 1, 1),
        time\Instant::fromYmd(2000, 1, 2),
        time\Boundary::InclusiveToInclusive,
    ),
    new time\Interval(
        time\Instant::fromYmd(2000, 1, 1),
        time\Instant::fromYmd(2000, 1, 2),
        time\Boundary::ExclusiveToExclusive,
    ),
    new time\Interval(
        time\Instant::fromYmd(2000, 1, 1),
        time\Instant::fromYmd(2000, 1, 2),
        time\Boundary::ExclusiveToInclusive,
    ),
];

foreach ($intervals as $interval) {
    echo stringify($interval) . "\n";
    echo '  ' . $interval->toIso8601(separator: '/') . "\n";
    echo '  ' . $interval->toIso8601(separator: '--') . "\n";
}

--EXPECT--
Interval('[2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z)')
  2000-01-01T00:00:00Z/2000-01-02T00:00:00Z
  2000-01-01T00:00:00Z--2000-01-02T00:00:00Z
Interval('[2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z]')
  2000-01-01T00:00:00Z/2000-01-02T00:00:00.000000001Z
  2000-01-01T00:00:00Z--2000-01-02T00:00:00.000000001Z
Interval('(2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z)')
  2000-01-01T00:00:00.000000001Z/2000-01-02T00:00:00Z
  2000-01-01T00:00:00.000000001Z--2000-01-02T00:00:00Z
Interval('(2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z]')
  2000-01-01T00:00:00.000000001Z/2000-01-02T00:00:00.000000001Z
  2000-01-01T00:00:00.000000001Z--2000-01-02T00:00:00.000000001Z
