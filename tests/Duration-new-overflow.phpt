--TEST--
new Duration - overflow detection
--FILE--
<?php

include __DIR__ . '/include.php';

var_dump(new time\Duration(seconds: PHP_INT_MAX, nanoseconds: 999999999));
var_dump(new time\Duration(seconds: PHP_INT_MAX-1, nanoseconds: 1999999999));
var_dump(new time\Duration(seconds: PHP_INT_MIN, nanoseconds: 0));
var_dump(new time\Duration(seconds: PHP_INT_MIN+1, nanoseconds: -1000000000));
var_dump(new time\Duration(milliseconds: 999, microseconds: 999999, nanoseconds: 999999999));
var_dump(new time\Duration(milliseconds: -999, microseconds: -999999, nanoseconds: -999999999));

try {
    var_dump(new time\Duration(seconds: PHP_INT_MAX, nanoseconds: 1000000000));
} catch (time\RangeError $e) {
    echo $e::class . ': ' . $e->getMessage() . "\n";
}

--EXPECTF--
object(time\Duration)#%d (%d) {
  ["totalSeconds"]=>
  int(%d)
  ["nanosOfSecond"]=>
  int(999999999)
}
object(time\Duration)#%d (%d) {
  ["totalSeconds"]=>
  int(%d)
  ["nanosOfSecond"]=>
  int(999999999)
}
object(time\Duration)#%d (%d) {
  ["totalSeconds"]=>
  int(-%d)
  ["nanosOfSecond"]=>
  int(0)
}
object(time\Duration)#%d (%d) {
  ["totalSeconds"]=>
  int(-%d)
  ["nanosOfSecond"]=>
  int(0)
}
object(time\Duration)#%d (%d) {
  ["totalSeconds"]=>
  int(2)
  ["nanosOfSecond"]=>
  int(998998999)
}
object(time\Duration)#%d (%d) {
  ["totalSeconds"]=>
  int(-3)
  ["nanosOfSecond"]=>
  int(1001001)
}
time\RangeError: Duration must be within -%d and %d total seconds
