--TEST--
WallClock->resolution
--FILE--
<?php

include __DIR__ . '/include.php';

echo "WallClock::MAX_RESOLUTION: " . stringify(time\WallClock::MAX_RESOLUTION) . "\n\n";

echo "Default resolution equals max. resolution:\n";
$clock = new time\WallClock();
echo stringify($clock) . "\n";
echo 'Time: ' . stringify($clock->takeUnixTimestampTuple()) . "\n";
echo ($clock->resolution === time\WallClock::MAX_RESOLUTION ? 'OK' : 'FAIL') . "\n\n";


echo "Resolution of 1 second:\n";
$clock = new time\WallClock(resolution: new time\Duration(seconds: 1));
echo stringify($clock) . "\n";

$t1 = $clock->takeUnixTimestampTuple();
usleep(100);
$t2 = $clock->takeUnixTimestampTuple();
echo 'Time: ' . stringify($t1) . "\n";
echo 'Time +100us ' . stringify($t2) . "\n";

if (
	$t1[1] === 0
	&& $t2[1] === 0
	&& $t2[0] - $t1[0] <= 1
) {
	echo "OK\n\n";
} else {
	echo "FAIL\n\n";
}


echo "Resolution of 2 seconds:\n";
$clock = new time\WallClock(resolution: new time\Duration(seconds: 2));
echo stringify($clock) . "\n";

$t1 = $clock->takeUnixTimestampTuple();
usleep(100);
$t2 = $clock->takeUnixTimestampTuple();
echo 'Time: ' . stringify($t1) . "\n";
echo 'Time +100us: ' . stringify($t2) . "\n";

if (
	$t1[0] % 2 === 0
	&& $t1[1] === 0
	&& $t2[0] % 2 === 0
	&& $t2[1] === 0
	&& $t2[0] - $t1[0] <= 2
) {
	echo "OK\n\n";
} else {
	echo "FAIL\n\n";
}

echo "Invalid negative resolution:\n";
try {
	$clock = new time\WallClock(resolution: new time\Duration(nanoseconds: -1));
} catch (time\LogicError $e) {
	echo $e::class . ": {$e->getMessage()}\n\n";
}

echo "Invalid resolution of zero:\n";
try {
	$clock = new time\WallClock(resolution: new time\Duration());
} catch (time\LogicError $e) {
	echo $e::class . ": {$e->getMessage()}\n\n";
}

echo "Invalid resolution not a multiple of max. resolution:\n";
try {
	$clock = new time\WallClock(resolution: new time\Duration(nanoseconds: 1));
} catch (time\LogicError $e) {
	echo $e::class . ": {$e->getMessage()}\n\n";
}

--EXPECTF--
WallClock::MAX_RESOLUTION: Duration('PT%fS')

Default resolution equals max. resolution:
WallClock(resolution: Duration('PT%fS'), modifier: Duration('PT0S'))
Time: [%d, %d]
OK

Resolution of 1 second:
WallClock(resolution: Duration('PT1S'), modifier: Duration('PT0S'))
Time: [%d, 0]
Time +100us [%d, 0]
OK

Resolution of 2 seconds:
WallClock(resolution: Duration('PT2S'), modifier: Duration('PT0S'))
Time: [%d, 0]
Time +100us: [%d, 0]
OK

Invalid negative resolution:
time\LogicError: Resolution must not be negative

Invalid resolution of zero:
time\LogicError: Resolution must not be zero

Invalid resolution not a multiple of max. resolution:
time\LogicError: Resolution must be a multiple of time\WallClock::MAX_RESOLUTION