--TEST--
Moment->toUnixTimestamp() and Moment->toUnixTimestampTuple()
--FILE--
<?php

include __DIR__ . '/include.php';

$moments = [
    time\Moment::fromUnixTimestampTuple([0, 0]),
    time\Moment::fromUnixTimestampTuple([1, 987654321]),
    time\Moment::fromUnixTimestampTuple([-1, 987654321]),
    time\Moment::fromUnixTimestampTuple([PHP_INT_MAX, 987654321]),
    time\Moment::fromUnixTimestampTuple([PHP_INT_MIN, 987654321]),
    time\Moment::fromUnixTimestampTuple([PHP_INT_MIN, 0]),
];

foreach ($moments as $moment) {
    echo stringify($moment) . "\n";
    echo '  toUnixTimestampTuple(): ' . stringify($moment->toUnixTimestampTuple()) . "\n";
    echo '  toUnixTimestamp(): ' . stringify($moment->toUnixTimestamp()) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Second, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Minute, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Minute, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Hour, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Hour, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Millisecond, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Millisecond, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Microsecond, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Microsecond, fractions: true)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Nanosecond, fractions: false)) . "\n";
    echo '  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): ' . stringify($moment->toUnixTimestamp(unit: time\TimeUnit::Nanosecond, fractions: true)) . "\n";
}

--EXPECT--
Moment('Thu 1970-01-01 00:00:00', 0, 0)
  toUnixTimestampTuple(): [0, 0]
  toUnixTimestamp(): 0
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): 0
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): 0
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): 0
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): 0
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): 0
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): 0
Moment('Thu 1970-01-01 00:00:01.987654321', 1, 987654321)
  toUnixTimestampTuple(): [1, 987654321]
  toUnixTimestamp(): 1
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): 1.987654321
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): 0.03312757201666666
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): 0.0005521262002777778
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): 1987
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): 1987.654321
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): 1987654
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): 1987654.321
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): 1987654321
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): 1987654321
Moment('Wed 1969-12-31 23:59:59.987654321', -1, 987654321)
  toUnixTimestampTuple(): [-1, 987654321]
  toUnixTimestamp(): -1
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): -0.012345679000000054
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): -0.00020576131666666733
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 0
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): -3.429355277777789E-6
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): -13
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): -12.345679000000018
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): -12346
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): -12345.679000000004
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): -12345679
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): -12345679
Moment('Sun 292277026596-12-04 15:30:07.987654321', 9223372036854775807, 987654321)
  toUnixTimestampTuple(): [9223372036854775807, 987654321]
  toUnixTimestamp(): 9223372036854775807
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): 9.223372036854776E+18
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): 153722867280912930
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): 1.5372286728091293E+17
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): 2562047788015215
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): 2562047788015215.5
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): 9.223372036854776E+21
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): 9.223372036854776E+21
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): 9.223372036854776E+24
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): 9.223372036854776E+24
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): 9.223372036854776E+27
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): 9.223372036854776E+27
Moment('Sun -292277022657-01-27 08:29:52.987654321', -9223372036854775808, 987654321)
  toUnixTimestampTuple(): [-9223372036854775808, 987654321]
  toUnixTimestamp(): -9223372036854775808
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): -9.223372036854776E+18
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): -153722867280912930
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): -1.5372286728091293E+17
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): -2562047788015215
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): -2562047788015215.5
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): -9.223372036854776E+21
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): -9.223372036854776E+21
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): -9.223372036854776E+24
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): -9.223372036854776E+24
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): -9.223372036854776E+27
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): -9.223372036854776E+27
Moment('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)
  toUnixTimestampTuple(): [-9223372036854775808, 0]
  toUnixTimestamp(): -9223372036854775808
  toUnixTimestamp(unit: TimeUnit::Second, fractions: true): -9223372036854775808
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: false): -153722867280912930
  toUnixTimestamp(unit: TimeUnit::Minute, fractions: true): -1.5372286728091293E+17
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: false): -2562047788015215
  toUnixTimestamp(unit: TimeUnit::Hour, fractions: true): -2562047788015215.5
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: false): -9.223372036854776E+21
  toUnixTimestamp(unit: TimeUnit::Millisecond, fractions: true): -9.223372036854776E+21
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: false): -9.223372036854776E+24
  toUnixTimestamp(unit: TimeUnit::Microsecond, fractions: true): -9.223372036854776E+24
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: false): -9.223372036854776E+27
  toUnixTimestamp(unit: TimeUnit::Nanosecond, fractions: true): -9.223372036854776E+27
