--TEST--
Interval->contains()
--FILE--
<?php

include __DIR__ . '/include.php';

$interval = new time\Interval(
    time\Instant::fromYmd(2000, 1, 1),
    time\Instant::fromYmd(2000, 1, 2),
    time\Boundary::InclusiveToExclusive,
);
echo stringify($interval) . "\n";
echo '  contains(' . stringify($interval->start) . ') = ' . stringify($interval->contains($interval->start)) . "\n";
echo '  contains(' . stringify($interval->end) . ') = ' . stringify($interval->contains($interval->end)) . "\n";

$other = time\Instant::fromYmd(1999, 12, 31, 23, 59, 59, 999999999);
echo '  contains(' . stringify($other) . ') = ' . stringify($interval->contains($other)) . "\n";

$other = time\Instant::fromYmd(2000, 1, 1, 23, 59, 59, 999999999);
echo '  contains(' . stringify($other) . ') = ' . stringify($interval->contains($other)) . "\n";

$other = time\Instant::fromYmd(2000, 1, 2, 0, 0, 0, 1);
echo '  contains(' . stringify($other) . ') = ' . stringify($interval->contains($other)) . "\n";

$other = $interval;
echo '  contains(' . stringify($other) . ') = ' . stringify($interval->contains($other)) . "\n";

$other = $interval->withBoundaryAdjustedInstant(time\Boundary::ExclusiveToInclusive);
echo '  contains(' . stringify($other) . ') = ' . stringify($interval->contains($other)) . "\n";

$other = $interval->withBoundarySameInstant(time\Boundary::ExclusiveToInclusive);
echo '  contains(' . stringify($other) . ') = ' . stringify($interval->contains($other)) . "\n";

--EXPECT--
Interval('[2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z)')
  contains(Instant('Sat 2000-01-01 00:00:00', 946684800, 0)) = true
  contains(Instant('Sun 2000-01-02 00:00:00', 946771200, 0)) = false
  contains(Instant('Fri 1999-12-31 23:59:59.999999999', 946684799, 999999999)) = false
  contains(Instant('Sat 2000-01-01 23:59:59.999999999', 946771199, 999999999)) = true
  contains(Instant('Sun 2000-01-02 00:00:00.000000001', 946771200, 1)) = false
  contains(Interval('[2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z)')) = true
  contains(Interval('(1999-12-31T23:59:59.999999999Z, 2000-01-01T23:59:59.999999999Z]')) = true
  contains(Interval('(2000-01-01T00:00:00Z, 2000-01-02T00:00:00Z]')) = false
