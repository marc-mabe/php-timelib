--TEST--
Period->isEqual()
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Period();

$period1 = new time\Period(years: 123, months: 456, days: 789);
$period2 = new time\Period(years: 321, months: 654, days: 987);

echo stringify($zero) . ' ' . ($zero->isEqual($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero->inverted()) . ' ' . ($zero->inverted()->isEqual($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero) . ' ' . ($zero->isEqual($period1) ? '==' : '!=') . ' ' . stringify($period1) . "\n";
echo stringify($period1) . ' ' . ($period1->isEqual($period2) ? '==' : '!=') . ' ' . stringify($period2) . "\n";
echo stringify($period2) . ' ' . ($period2->isEqual($period1) ? '==' : '!=') . ' ' . stringify($period1) . "\n";
echo stringify($period1->inverted()) . ' ' . ($period1->inverted()->isEqual($period2) ? '==' : '!=') . ' ' . stringify($period2) . "\n";
echo stringify($period2->inverted()) . ' ' . ($period2->inverted()->isEqual($period1) ? '==' : '!=') . ' ' . stringify($period1) . "\n";
echo stringify($period1) . ' ' . ($period1->isEqual($period1->allInverted()) ? '==' : '!=') . ' ' . stringify($period1->allInverted()) . "\n";

--EXPECT--
Period('P0D') == Period('P0D')
Period('-P0D') == Period('P0D')
Period('P0D') != Period('P123Y456M789D')
Period('P123Y456M789D') != Period('P321Y654M987D')
Period('P321Y654M987D') != Period('P123Y456M789D')
Period('-P123Y456M789D') != Period('P321Y654M987D')
Period('-P321Y654M987D') != Period('P123Y456M789D')
Period('P123Y456M789D') == Period('-P-123Y-456M-789D')
