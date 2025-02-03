--TEST--
Basic Clock
--FILE--
<?php

include __DIR__ . '/../vendor/autoload.php';

$moment = \dt\Moment::fromUnixTimestampTuple([1738599906, 123456789]);

echo "Moment: {$moment->format('Y-m-d H:i:s.u')}\n";

--EXPECT--
Moment: 2025-02-03 16:25:06.123456
