--TEST--
Instant->toUnixTimestamp() and Instant->toUnixTimestampTuple()
--FILE--
<?php

include __DIR__ . '/include.php';

const INT32_MAX = 2147483647;
const INT32_MIN = -2147483647 - 1;

$instants = [
    time\Instant::fromUnixTimestampTuple([0, 0]),
    time\Instant::fromUnixTimestampTuple([1, 987654321]),
    time\Instant::fromUnixTimestampTuple([-1, 987654321]),
    time\Instant::fromUnixTimestampTuple([INT32_MAX, 987654321]),
    time\Instant::fromUnixTimestampTuple([INT32_MIN, 987654321]),
    time\Instant::fromUnixTimestampTuple([INT32_MIN, 0]),
];

foreach ($instants as $instant) {
    echo stringify($instant) . "\n";
    echo '  toUnixTimestampTuple(): ' . stringify($instant->toUnixTimestampTuple()) . "\n";
    echo '  toUnixTimestamp(): ' . sprintf('%.15F', $instant->toUnixTimestamp()) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Second, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Minute, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Minute, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Hour, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Hour, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Millisecond, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Millisecond, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Microsecond, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Microsecond, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Nanosecond, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): ' . sprintf('%.15F', $instant->toUnixTimestamp(unit: time\TimeUnit::Nanosecond, fractions: true)) . "\n";
}

--EXPECT--
Instant('Thu 1970-01-01 00:00:00', 0, 0)
  toUnixTimestampTuple(): [0, 0]
  toUnixTimestamp(): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): 0.000000000000000
Instant('Thu 1970-01-01 00:00:01.987654321', 1, 987654321)
  toUnixTimestampTuple(): [1, 987654321]
  toUnixTimestamp(): 1.000000000000000
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): 1.987654321000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): 0.033127572016667
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): 0.000552126200278
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): 1987.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): 1987.654320999999982
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): 1987654.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): 1987654.320999999996275
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): 1987654321.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): 1987654321.000000000000000
Instant('Wed 1969-12-31 23:59:59.987654321', -1, 987654321)
  toUnixTimestampTuple(): [-1, 987654321]
  toUnixTimestamp(): -1.000000000000000
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): -0.012345679000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): -0.000205761316667
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 0.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): -0.000003429355278
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): -13.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): -12.345679000000018
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): -12346.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): -12345.679000000003725
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): -12345679.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): -12345679.000000000000000
Instant('Tue 2038-01-19 03:14:07.987654321', 2147483647, 987654321)
  toUnixTimestampTuple(): [2147483647, 987654321]
  toUnixTimestamp(): 2147483647.000000000000000
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): 2147483647.987654209136963
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 35791394.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): 35791394.133127570152283
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 596523.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): 596523.235552126192488
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): 2147483647987.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): 2147483647987.654296875000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): 2147483647987654.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): 2147483647987654.250000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): 2147483647987654400.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): 2147483647987654400.000000000000000
Instant('Fri 1901-12-13 20:45:52.987654321', -2147483648, 987654321)
  toUnixTimestampTuple(): [-2147483648, 987654321]
  toUnixTimestamp(): -2147483648.000000000000000
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): -2147483647.012345790863037
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): -35791394.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): -35791394.116872429847717
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): -596523.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): -596523.235281207133085
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): -2147483647013.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): -2147483647012.345703125000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): -2147483647012346.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): -2147483647012345.750000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): -2147483647012345600.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): -2147483647012345600.000000000000000
Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)
  toUnixTimestampTuple(): [-2147483648, 0]
  toUnixTimestamp(): -2147483648.000000000000000
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): -2147483648.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): -35791394.000000000000000
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): -35791394.133333332836628
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): -596523.000000000000000
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): -596523.235555555555038
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): -2147483648000.000000000000000
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): -2147483648000.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): -2147483648000000.000000000000000
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): -2147483648000000.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): -2147483648000000000.000000000000000
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): -2147483648000000000.000000000000000
