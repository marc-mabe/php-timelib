--TEST--
WallClock->takeUnixTimestamp
--FILE--
<?php

include __DIR__ . '/include.php';

$clock = new time\WallClock();
echo stringify($clock) . "\n\n";

echo "### Unix Timestamp with fractions ###\n";
echo "\tmicrotime(): " . ($mt = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(fractions: true)) . "\n";
echo (($ct - $mt) < 1.0 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(fractions: false)) . "\n";
echo (($ct - $ts) < 1.0 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in milliseconds with fractions ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Millisecond, fractions: true)) . "\n";
echo ($ct / 1_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in milliseconds ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Millisecond, fractions: false)) . "\n";
echo ($ct / 1_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in microseconds with fractions ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Microsecond, fractions: true)) . "\n";
echo ($ct / 1_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in microseconds ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Microsecond, fractions: false)) . "\n";
echo ($ct / 1_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in nanoseconds ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Nanosecond)) . "\n";
echo ($ct / 1_000_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in minutes with fractions ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Minute, fractions: true)) . "\n";
echo ($ct * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in minutes ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Minute, fractions: false)) . "\n";
echo ($ct * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in hours with fractions ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Hour, fractions: true)) . "\n";
echo ($ct * 60 * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in hours ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Hour, fractions: false)) . "\n";
echo ($ct * 60 * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

--EXPECTF--
WallClock(resolution: Duration('PT0.000001S'), modifier: Duration('PT0S'))

### Unix Timestamp with fractions ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in milliseconds with fractions ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in milliseconds ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in microseconds with fractions ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in microseconds ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in nanoseconds ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in minutes with fractions ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in minutes ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in hours with fractions ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in hours ###
	microtime(): %f
	WallClock:   %f
OK
