--TEST--
Period->withNormalizedMonthsIntoYears
--FILE--
<?php

include __DIR__ . '/include.php';

$periods = [
    new time\Period(
        years: 123, months: 11, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 123, microseconds: 456, nanoseconds: 789,
    ),
    new time\Period(
        years: 123, months: -11, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 123, microseconds: 456, nanoseconds: 789,
    ),
    new time\Period(
        years: -123, months: -11, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 123, microseconds: 456, nanoseconds: 789,
    ),
    new time\Period(
        isNegative: true,
        years: 123, months: 11, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 123, microseconds: 456, nanoseconds: 789,
    ),
    new time\Period(
        years: 123, months: 456, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 123, microseconds: 456, nanoseconds: 789,
    ),
    new time\Period(
        years: 321, months: 654, days: 987,
        hours: 321, minutes: 654, seconds: 987,
        milliseconds: 321, microseconds: 654, nanoseconds: 876,
    )
];

foreach ($periods as $period) {
    echo stringify($period) . ' -> ' . stringify($period->withNormalizedMonthsIntoYears()) . "\n";
}

--EXPECTF--
Period('P123Y11M789DT123H456M789.123456789S') -> Period('P123Y11M789DT123H456M789.123456789S')
Period('P123Y-11M789DT123H456M789.123456789S') -> Period('P123Y-11M789DT123H456M789.123456789S')
Period('P-123Y-11M789DT123H456M789.123456789S') -> Period('P-123Y-11M789DT123H456M789.123456789S')
Period('-P123Y11M789DT123H456M789.123456789S') -> Period('-P123Y11M789DT123H456M789.123456789S')
Period('P123Y456M789DT123H456M789.123456789S') -> Period('P161Y789DT123H456M789.123456789S')
Period('P321Y654M987DT321H654M987.321654876S') -> Period('P375Y6M987DT321H654M987.321654876S')
