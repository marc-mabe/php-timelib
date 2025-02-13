--TEST--
WallClock->takeUnixTimestamp
--FILE--
<?php

include __DIR__ . '/include.php';

$clock = new time\WallClock();
var_dump($clock);

echo "Resolution: {$clock->getResolution()->toIso()}\n";

echo "### Unix Timestamp (float) ###\n";
echo "\tmicrotime(): " . ($mt = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(fractions: true)) . "\n";
echo (($ct - $mt) < 1.0 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp (int) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(fractions: false)) . "\n";
echo (($ct - $ts) < 1.0 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in milliseconds (float) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Millisecond, fractions: true)) . "\n";
echo ($ct / 1_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in milliseconds (int) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Millisecond, fractions: false)) . "\n";
echo ($ct / 1_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in microseconds (float) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Microsecond, fractions: true)) . "\n";
echo ($ct / 1_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in microseconds (int) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Microsecond, fractions: false)) . "\n";
echo ($ct / 1_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in nanoseconds (float) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Nanosecond, fractions: true)) . "\n";
echo ($ct / 1_000_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in nanoseconds (int) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Nanosecond, fractions: false)) . "\n";
echo ($ct / 1_000_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in minutes (float) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Minute, fractions: true)) . "\n";
echo ($ct * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in minutes (int) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Minute, fractions: false)) . "\n";
echo ($ct * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in hours (float) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Hour, fractions: true)) . "\n";
echo ($ct * 60 * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in hours (int) ###\n";
echo "\tmicrotime(): " . ($ts = microtime(true)) . "\n";
echo "\tWallClock:   " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Hour, fractions: false)) . "\n";
echo ($ct * 60 * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

--EXPECTF--
object(time\WallClock)#%d (1) {
  ["modifier"]=>
  object(time\Duration)#%d (10) {
    ["isNegative"]=>
    bool(false)
    ["years"]=>
    int(0)
    ["months"]=>
    int(0)
    ["days"]=>
    int(0)
    ["hours"]=>
    int(0)
    ["minutes"]=>
    int(0)
    ["seconds"]=>
    int(0)
    ["milliseconds"]=>
    int(0)
    ["microseconds"]=>
    int(0)
    ["nanoseconds"]=>
    int(0)
  }
}
Resolution: P0D
### Unix Timestamp (float) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp (int) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in milliseconds (float) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in milliseconds (int) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in microseconds (float) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in microseconds (int) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in nanoseconds (float) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in nanoseconds (int) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in minutes (float) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in minutes (int) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in hours (float) ###
	microtime(): %f
	WallClock:   %f
OK
### Unix Timestamp in hours (int) ###
	microtime(): %f
	WallClock:   %f
OK
