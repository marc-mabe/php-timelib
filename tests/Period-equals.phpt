--TEST--
Period->diff
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Period();

$period1 = new time\Period(
    years: 123, months: 456, days: 789,
    hours: 123, minutes: 456, seconds: 789,
    milliseconds: 123, microseconds: 456, nanoseconds: 789,
);

$period2 = new time\Period(
    years: 321, months: 654, days: 987,
    hours: 321, minutes: 654, seconds: 987,
    milliseconds: 321, microseconds: 654, nanoseconds: 876,
);

echo stringify($zero) . ' ' . ($zero->equals($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero->inverted()) . ' ' . ($zero->inverted()->equals($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero) . ' ' . ($zero->equals($period1) ? '==' : '!=') . ' ' . stringify($period1) . "\n";
echo stringify($period1) . ' ' . ($period1->equals($period2) ? '==' : '!=') . ' ' . stringify($period2) . "\n";
echo stringify($period2) . ' ' . ($period2->equals($period1) ? '==' : '!=') . ' ' . stringify($period1) . "\n";
echo stringify($period1->inverted()) . ' ' . ($period1->inverted()->equals($period2) ? '==' : '!=') . ' ' . stringify($period2) . "\n";
echo stringify($period2->inverted()) . ' ' . ($period2->inverted()->equals($period1) ? '==' : '!=') . ' ' . stringify($period1) . "\n";
echo stringify($period1) . ' ' . ($period1->equals($period1->allInverted()) ? '==' : '!=') . ' ' . stringify($period1->allInverted()) . "\n";

--EXPECTF--
Period('P0D') == Period('P0D')
Period('-P0D') == Period('P0D')
Period('P0D') != Period('P123Y456M789DT123H456M789.123456789S')
Period('P123Y456M789DT123H456M789.123456789S') != Period('P321Y654M987DT321H654M987.321654876S')
Period('P321Y654M987DT321H654M987.321654876S') != Period('P123Y456M789DT123H456M789.123456789S')
Period('-P123Y456M789DT123H456M789.123456789S') != Period('P321Y654M987DT321H654M987.321654876S')
Period('-P321Y654M987DT321H654M987.321654876S') != Period('P123Y456M789DT123H456M789.123456789S')
Period('P123Y456M789DT123H456M789.123456789S') == Period('-P-123Y-456M-789DT-123H-456M-789.123456789S')
