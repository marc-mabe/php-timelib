--TEST--
StopWatch: basic functionalities
--FILE--
<?php

include __DIR__ . '/include.php';

echo 'Measure 1s in MonotonicClock: ';
$clock = new \time\MonotonicClock();
$watch = new \time\StopWatch($clock);
$watch->start();
sleep(1);
$watch->stop();
echo stringify($watch);
if ($watch->getElapsedTime(\time\TimeUnit::Second) >= 1 && $watch->getElapsedTime(\time\TimeUnit::Second) < 1.001) {
    echo " (OK)\n";
} else {
    echo " (FAIL)\n";
}

echo "Elapsed Hours: {$watch->getElapsedTime(\time\TimeUnit::Hour, fractions: false)}\n";
echo "Elapsed Minutes: {$watch->getElapsedTime(\time\TimeUnit::Minute, fractions: false)}\n";
echo "Elapsed Nanos: {$watch->getElapsedTime(\time\TimeUnit::Nanosecond, fractions: false)}\n";
echo "Elapsed Micros: {$watch->getElapsedTime(\time\TimeUnit::Microsecond, fractions: false)}\n";
echo "Elapsed Millis: {$watch->getElapsedTime(\time\TimeUnit::Millisecond, fractions: false)}\n";
echo "Elapsed Seconds: {$watch->getElapsedTime(\time\TimeUnit::Second, fractions: false)}\n";
echo "Elapsed Duration: " . stringify($watch->getElapsedDuration()) . "\n";
var_dump($watch);

echo 'Measure 1s in WallClock: ';
$clock = new \time\WallClock();
$watch = new \time\StopWatch($clock);
$watch->start();
sleep(1);
$watch->stop();
echo stringify($watch);
if ($watch->getElapsedTime(\time\TimeUnit::Second) >= 1 && $watch->getElapsedTime(\time\TimeUnit::Second) <= 2) {
    echo " (OK)\n";
} else {
    echo " (FAIL)\n";
}
var_dump($watch);

--EXPECTF--
Measure 1s in MonotonicClock: time\StopWatch(elapsed: %dns, isRunning: false) (OK)
Elapsed Hours: 0
Elapsed Minutes: 0
Elapsed Nanos: 100%d
Elapsed Micros: 100%d
Elapsed Millis: 1000
Elapsed Seconds: 1
Elapsed Duration: Duration('PT1.00%dS')
object(time\StopWatch)#%d (%d) {
  ["startedAt":"time\StopWatch":private]=>
  NULL
  ["elapsedNanosPrev":"time\StopWatch":private]=>
  int(1%d)
  ["clock"]=>
  object(time\MonotonicClock)#%d (%d) {
    ["resolution"]=>
    object(time\Duration)#%d (%d) {
      ["s":"time\Duration":private]=>
      int(%d)
      ["ns":"time\Duration":private]=>
      int(%d)
    }
    ["modifier"]=>
    object(time\Duration)#%d (%d) {
      ["s":"time\Duration":private]=>
      int(%d)
      ["ns":"time\Duration":private]=>
      int(%d)
    }
  }
}
Measure 1s in WallClock: time\StopWatch(elapsed: %dns, isRunning: false) (OK)
object(time\StopWatch)#%d (%d) {
  ["startedAt":"time\StopWatch":private]=>
  NULL
  ["elapsedNanosPrev":"time\StopWatch":private]=>
  int(%d)
  ["clock"]=>
  object(time\WallClock)#%d (%d) {
    ["resolution"]=>
    object(time\Duration)#%d (%d) {
      ["s":"time\Duration":private]=>
      int(%d)
      ["ns":"time\Duration":private]=>
      int(%d)
    }
    ["modifier"]=>
    object(time\Duration)#%d (%d) {
      ["s":"time\Duration":private]=>
      int(%d)
      ["ns":"time\Duration":private]=>
      int(%d)
    }
  }
}
