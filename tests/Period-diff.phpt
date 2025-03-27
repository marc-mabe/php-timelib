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

echo stringify($zero) . ' diff ' . stringify($period1) . ' = ' . stringify($zero->diff($period1)) . "\n";
echo stringify($zero->inverted()) . ' diff ' . stringify($period1) . ' = ' . stringify($zero->inverted()->diff($period1)) . "\n";
echo stringify($period1) . ' diff ' . stringify($period2) . ' = ' . stringify($period1->diff($period2)) . "\n";
echo stringify($period2) . ' diff ' . stringify($period1) . ' = ' . stringify($period2->diff($period1)) . "\n";
echo stringify($period1->inverted()) . ' diff ' . stringify($period2) . ' = ' . stringify($period1->inverted()->diff($period2)) . "\n";
echo stringify($period2->inverted()) . ' diff ' . stringify($period1) . ' = ' . stringify($period2->inverted()->diff($period1)) . "\n";

--EXPECTF--
Period('P0D') diff Period('P123Y456M789DT123H456M789.123456789S') = Period('P123Y456M789DT123H456M789.123456789S')
Period('-P0D') diff Period('P123Y456M789DT123H456M789.123456789S') = Period('P123Y456M789DT123H456M789.123456789S')
Period('P123Y456M789DT123H456M789.123456789S') diff Period('P321Y654M987DT321H654M987.321654876S') = Period('P198Y198M198DT198H198M198.198198087S')
Period('P321Y654M987DT321H654M987.321654876S') diff Period('P123Y456M789DT123H456M789.123456789S') = Period('P198Y198M198DT198H198M198.198198087S')
Period('-P123Y456M789DT123H456M789.123456789S') diff Period('P321Y654M987DT321H654M987.321654876S') = Period('P444Y1110M1776DT444H1110M1776.445111665S')
Period('-P321Y654M987DT321H654M987.321654876S') diff Period('P123Y456M789DT123H456M789.123456789S') = Period('P444Y1110M1776DT444H1110M1776.445111665S')
