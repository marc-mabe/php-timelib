--TEST--
Period->diff
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Period();

$period1 = new time\Period(years: 123, months: 456, days: 789);
$period2 = new time\Period(years: 321, months: 654, days: 987);

echo stringify($zero) . ' diff ' . stringify($period1) . ' = ' . stringify($zero->diff($period1)) . "\n";
echo stringify($zero->inverted()) . ' diff ' . stringify($period1) . ' = ' . stringify($zero->inverted()->diff($period1)) . "\n";
echo stringify($period1) . ' diff ' . stringify($period2) . ' = ' . stringify($period1->diff($period2)) . "\n";
echo stringify($period2) . ' diff ' . stringify($period1) . ' = ' . stringify($period2->diff($period1)) . "\n";
echo stringify($period1->inverted()) . ' diff ' . stringify($period2) . ' = ' . stringify($period1->inverted()->diff($period2)) . "\n";
echo stringify($period2->inverted()) . ' diff ' . stringify($period1) . ' = ' . stringify($period2->inverted()->diff($period1)) . "\n";

--EXPECTF--
Period('P0D') diff Period('P123Y456M789D') = Period('P123Y456M789D')
Period('-P0D') diff Period('P123Y456M789D') = Period('P123Y456M789D')
Period('P123Y456M789D') diff Period('P321Y654M987D') = Period('P198Y198M198D')
Period('P321Y654M987D') diff Period('P123Y456M789D') = Period('P198Y198M198D')
Period('-P123Y456M789D') diff Period('P321Y654M987D') = Period('P444Y1110M1776D')
Period('-P321Y654M987D') diff Period('P123Y456M789D') = Period('P444Y1110M1776D')
