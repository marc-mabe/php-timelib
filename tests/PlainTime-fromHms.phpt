--TEST--
PlainTime::fromHms
--FILE--
<?php

include __DIR__ . '/include.php';

echo "fromHms(0, 0, 0)\n";
echo '  ' . stringify(\time\PlainTime::fromHms(0, 0, 0)) . "\n";

echo "fromHms(23, 59, 59, 999999999)\n";
echo '  ' . stringify(\time\PlainTime::fromHms(23, 59, 59, 999999999)) . "\n";

echo "fromHms(12, 30, 30, 500)\n";
echo '  ' . stringify(\time\PlainTime::fromHms(12, 30, 30, 500)) . "\n";

--EXPECT--
fromHms(0, 0, 0)
  PlainTime('00:00:00')
fromHms(23, 59, 59, 999999999)
  PlainTime('23:59:59.999999999')
fromHms(12, 30, 30, 500)
  PlainTime('12:30:30.0000005')
