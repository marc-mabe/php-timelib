--TEST--
Period->isEqual()
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Duration();

$duration1 = new time\Duration(seconds: 123, nanoseconds: 456);
$duration2 = new time\Duration(seconds: 987, nanoseconds: 654);

echo stringify($zero) . ' ' . ($zero->isEqual($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero->inverted()) . ' ' . ($zero->inverted()->isEqual($zero) ? '==' : '!=') . ' ' . stringify($zero) . "\n";
echo stringify($zero) . ' ' . ($zero->isEqual($duration1) ? '==' : '!=') . ' ' . stringify($duration1) . "\n";
echo stringify($duration1) . ' ' . ($duration1->isEqual($duration2) ? '==' : '!=') . ' ' . stringify($duration2) . "\n";
echo stringify($duration2) . ' ' . ($duration2->isEqual($duration1) ? '==' : '!=') . ' ' . stringify($duration1) . "\n";
echo stringify($duration1->inverted()) . ' ' . ($duration1->inverted()->isEqual($duration2) ? '==' : '!=') . ' ' . stringify($duration2) . "\n";
echo stringify($duration2->inverted()) . ' ' . ($duration2->inverted()->isEqual($duration1) ? '==' : '!=') . ' ' . stringify($duration1) . "\n";

--EXPECT--
Duration('PT0S') == Duration('PT0S')
Duration('PT0S') == Duration('PT0S')
Duration('PT0S') != Duration('PT2M3.000000456S')
Duration('PT2M3.000000456S') != Duration('PT16M27.000000654S')
Duration('PT16M27.000000654S') != Duration('PT2M3.000000456S')
Duration('-PT2M3.000000456S') != Duration('PT16M27.000000654S')
Duration('-PT16M27.000000654S') != Duration('PT2M3.000000456S')
