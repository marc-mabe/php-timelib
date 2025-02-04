--TEST--
Basic Clock
--FILE--
<?php

include __DIR__ . '/../vendor/autoload.php';

echo "Moment::fromUnixTimestampTuple([1738599906, 123456789])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([1738599906, 123456789]);
echo "    {$moment->format('Y-m-d H:i:sf')}\n";

echo "Moment::fromUnixTimestampTuple([0, 0])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([0, 0]);
echo "    {$moment->format('Y-m-d H:i:sf')}\n";

echo "Moment::fromUnixTimestampTuple([-1, 1])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([-1, 1]);
echo "    {$moment->format('Y-m-d H:i:sf')}\n";

echo "Moment::fromUnixTimestampTuple([-1738599906, 987654])\n";
$moment = \dt\Moment::fromUnixTimestampTuple([-1738599906, 987654]);
echo "    {$moment->format('Y-m-d H:i:sf')}\n";

--EXPECT--
Moment::fromUnixTimestampTuple([1738599906, 123456789])
    2025-02-03 16:25:06.123456789
Moment::fromUnixTimestampTuple([0, 0])
    1970-01-01 00:00:00
Moment::fromUnixTimestampTuple([-1, 1])
    1969-12-31 23:59:59.1
Moment::fromUnixTimestampTuple([-1738599906, 987654])
    1914-11-28 07:34:54.987654
