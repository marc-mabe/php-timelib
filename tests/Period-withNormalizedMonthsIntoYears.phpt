--TEST--
Period->withNormalizedMonthsIntoYears
--FILE--
<?php

include __DIR__ . '/include.php';

$periods = [
    new time\Period(years: 123, months: 11, days: 789),
    new time\Period(years: 123, months: -11, days: 789),
    new time\Period(years: -123, months: -11, days: 789),
    new time\Period(isNegative: true, years: 123, months: 11, days: 789),
    new time\Period(years: 123, months: 456, days: 789),
    new time\Period(years: 321, months: 654, days: 987)
];

foreach ($periods as $period) {
    echo stringify($period) . ' -> ' . stringify($period->withNormalizedMonthsIntoYears()) . "\n";
}

--EXPECTF--
Period('P123Y11M789D') -> Period('P123Y11M789D')
Period('P123Y-11M789D') -> Period('P123Y-11M789D')
Period('P-123Y-11M789D') -> Period('P-123Y-11M789D')
Period('-P123Y11M789D') -> Period('-P123Y11M789D')
Period('P123Y456M789D') -> Period('P161Y789D')
Period('P321Y654M987D') -> Period('P375Y6M987D')
