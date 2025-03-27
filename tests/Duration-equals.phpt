--TEST--
Period->diff
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Duration();

$duration1 = new time\Duration(seconds: 123, nanoseconds: 456);
$duration2 = new time\Duration(seconds: 987, nanoseconds: 654);

echo stringify($zero) . ' ' . ($zero->equals($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero->inverted()) . ' ' . ($zero->inverted()->equals($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero) . ' ' . ($zero->equals($duration1) ? '==' : '!=') . ' ' . stringify($duration1) . "\n";
echo stringify($duration1) . ' ' . ($duration1->equals($duration2) ? '==' : '!=') . ' ' . stringify($duration2) . "\n";
echo stringify($duration2) . ' ' . ($duration2->equals($duration1) ? '==' : '!=') . ' ' . stringify($duration1) . "\n";
echo stringify($duration1->inverted()) . ' ' . ($duration1->inverted()->equals($duration2) ? '==' : '!=') . ' ' . stringify($duration2) . "\n";
echo stringify($duration2->inverted()) . ' ' . ($duration2->inverted()->equals($duration1) ? '==' : '!=') . ' ' . stringify($duration1) . "\n";

--EXPECTF--
Duration('PT0S') == Duration('PT0S')
Duration('PT0S') == Duration('PT0S')
Duration('PT0S') != Duration('PT2M3.000000456S')
Duration('PT2M3.000000456S') != Duration('PT16M27.000000654S')
Duration('PT16M27.000000654S') != Duration('PT2M3.000000456S')
Duration('-PT2M3.000000456S') != Duration('PT16M27.000000654S')
Duration('-PT16M27.000000654S') != Duration('PT2M3.000000456S')
