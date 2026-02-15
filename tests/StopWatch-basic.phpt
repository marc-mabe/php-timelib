--TEST--
StopWatch: basic functionalities
--FILE--
<?php

include __DIR__ . '/include.php';

echo 'Measure 1s in MonotonicClock: ';
$watch = new time\StopWatch();
$watch->start();
sleep(1);
$watch->stop();
echo stringify($watch);
if ($watch->getElapsedTime(time\TimeUnit::Second) >= 1 && $watch->getElapsedTime(time\TimeUnit::Second) < 1.01) {
    echo " (OK)\n";
} else {
    echo " (FAIL)\n";
}
echo "Elapsed Hours: {$watch->getElapsedTime(time\TimeUnit::Hour, fractions: false)}\n";
echo "Elapsed Minutes: {$watch->getElapsedTime(time\TimeUnit::Minute, fractions: false)}\n";
echo "Elapsed Nanos: {$watch->getElapsedTime(time\TimeUnit::Nanosecond, fractions: false)}\n";
echo "Elapsed Micros: {$watch->getElapsedTime(time\TimeUnit::Microsecond, fractions: false)}\n";
echo "Elapsed Millis: {$watch->getElapsedTime(time\TimeUnit::Millisecond, fractions: false)}\n";
echo "Elapsed Seconds: {$watch->getElapsedTime(time\TimeUnit::Second, fractions: false)}\n";
echo "Elapsed Duration: " . stringify($watch->getElapsedDuration()) . "\n";

echo "\n";

echo 'Measure 1s in WallClock: ';
$clock = new time\WallClock();
$watch = new time\StopWatch($clock);
$watch->start();
sleep(1);
$watch->stop();
echo stringify($watch);
if ($watch->getElapsedTime(time\TimeUnit::Second) >= 1 && $watch->getElapsedTime(time\TimeUnit::Second) < 1.1) {
    echo " (OK)\n";
} else {
    echo " (FAIL)\n";
}
echo "Elapsed Hours: {$watch->getElapsedTime(time\TimeUnit::Hour, fractions: false)}\n";
echo "Elapsed Minutes: {$watch->getElapsedTime(time\TimeUnit::Minute, fractions: false)}\n";
echo "Elapsed Nanos: {$watch->getElapsedTime(time\TimeUnit::Nanosecond, fractions: false)}\n";
echo "Elapsed Micros: {$watch->getElapsedTime(time\TimeUnit::Microsecond, fractions: false)}\n";
echo "Elapsed Millis: {$watch->getElapsedTime(time\TimeUnit::Millisecond, fractions: false)}\n";
echo "Elapsed Seconds: {$watch->getElapsedTime(time\TimeUnit::Second, fractions: false)}\n";
echo "Elapsed Duration: " . stringify($watch->getElapsedDuration()) . "\n";

echo "\nReset\n";
$watch->reset();
echo "Elapsed Hours: {$watch->getElapsedTime(time\TimeUnit::Hour, fractions: false)}\n";
echo "Elapsed Minutes: {$watch->getElapsedTime(time\TimeUnit::Minute, fractions: false)}\n";
echo "Elapsed Nanos: {$watch->getElapsedTime(time\TimeUnit::Nanosecond, fractions: false)}\n";
echo "Elapsed Micros: {$watch->getElapsedTime(time\TimeUnit::Microsecond, fractions: false)}\n";
echo "Elapsed Millis: {$watch->getElapsedTime(time\TimeUnit::Millisecond, fractions: false)}\n";
echo "Elapsed Seconds: {$watch->getElapsedTime(time\TimeUnit::Second, fractions: false)}\n";
echo "Elapsed Duration: " . stringify($watch->getElapsedDuration()) . "\n";

--EXPECTF--
Measure 1s in MonotonicClock: time\StopWatch(elapsed: %dns, isRunning: false) (OK)
Elapsed Hours: 0
Elapsed Minutes: 0
Elapsed Nanos: 100%d
Elapsed Micros: 100%d
Elapsed Millis: 100%d
Elapsed Seconds: 1
Elapsed Duration: Duration('PT1.00%dS')

Measure 1s in WallClock: time\StopWatch(elapsed: %dns, isRunning: false) (OK)
Elapsed Hours: 0
Elapsed Minutes: 0
Elapsed Nanos: 10%d
Elapsed Micros: 10%d
Elapsed Millis: 10%d
Elapsed Seconds: 1
Elapsed Duration: Duration('PT1.0%dS')

Reset
Elapsed Hours: 0
Elapsed Minutes: 0
Elapsed Nanos: 0
Elapsed Micros: 0
Elapsed Millis: 0
Elapsed Seconds: 0
Elapsed Duration: Duration('PT0S')
