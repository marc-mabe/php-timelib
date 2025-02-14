--TEST--
MonotonicClock->takeUnixTimestamp
--FILE--
<?php

include __DIR__ . '/include.php';

$clock = new time\MonotonicClock();
echo stringify($clock) . "\n\n";

echo "### Unix Timestamp (float) ###\n";
echo "\tmicrotime():     " . ($mt = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(fractions: true)) . "\n";
echo (($ct - $mt) < 1.0 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp (int) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(fractions: false)) . "\n";
echo (($ct - $ts) < 1.0 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in milliseconds (float) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Millisecond, fractions: true)) . "\n";
echo ($ct / 1_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in milliseconds (int) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Millisecond, fractions: false)) . "\n";
echo ($ct / 1_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in microseconds (float) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Microsecond, fractions: true)) . "\n";
echo ($ct / 1_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in microseconds (int) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Microsecond, fractions: false)) . "\n";
echo ($ct / 1_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in nanoseconds (float) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Nanosecond, fractions: true)) . "\n";
echo ($ct / 1_000_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in nanoseconds (int) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Nanosecond, fractions: false)) . "\n";
echo ($ct / 1_000_000_000 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in minutes (float) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Minute, fractions: true)) . "\n";
echo ($ct * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in minutes (int) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Minute, fractions: false)) . "\n";
echo ($ct * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in hours (float) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Hour, fractions: true)) . "\n";
echo ($ct * 60 * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

echo "### Unix Timestamp in hours (int) ###\n";
echo "\tmicrotime():     " . ($ts = microtime(true)) . "\n";
echo "\tMonotonicClock:  " . ($ct = $clock->takeUnixTimestamp(\time\TimeUnit::Hour, fractions: false)) . "\n";
echo ($ct * 60 * 60 - $ts < 1 ? 'OK' : 'FAIL') . "\n";

--EXPECTF--
MonotonicClock(resolution: Duration('PT0.000000001S'), modifier: Duration('PT%sS'))

### Unix Timestamp (float) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp (int) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in milliseconds (float) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in milliseconds (int) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in microseconds (float) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in microseconds (int) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in nanoseconds (float) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in nanoseconds (int) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in minutes (float) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in minutes (int) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in hours (float) ###
	microtime():     %f
	MonotonicClock:  %f
OK
### Unix Timestamp in hours (int) ###
	microtime():     %f
	MonotonicClock:  %f
OK
