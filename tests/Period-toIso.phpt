--TEST--
Period->toIso
--FILE--
<?php

include __DIR__ . '/include.php';

$period = new time\Period(
    years: 123, months: 456, days: 789,
    hours: 123, minutes: 456, seconds: 789,
    milliseconds: 123, microseconds: 456, nanoseconds: 789,
);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(
    isInverted: true,
    years: 123, months: 456, days: 789,
    hours: 123, minutes: 456, seconds: 789,
    milliseconds: 123, microseconds: 456, nanoseconds: 789,
);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(
    years: -123, months: -456, days: -789,
    hours: -123, minutes: -456, seconds: -789,
    milliseconds: -123, microseconds: -456, nanoseconds: -789,
);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period();
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(isInverted: true);
var_dump($period);
echo $period->toIso() . "\n";

--EXPECTF--
object(time\Period)#%d (%d) {
  ["isInverted"]=>
  bool(false)
  ["years"]=>
  int(123)
  ["months"]=>
  int(456)
  ["days"]=>
  int(789)
  ["hours"]=>
  int(123)
  ["minutes"]=>
  int(456)
  ["seconds"]=>
  int(789)
  ["milliseconds"]=>
  int(123)
  ["microseconds"]=>
  int(456)
  ["nanoseconds"]=>
  int(789)
}
P123Y456M789DT123H456M789.123456789S
object(time\Period)#%d (%d) {
  ["isInverted"]=>
  bool(true)
  ["years"]=>
  int(123)
  ["months"]=>
  int(456)
  ["days"]=>
  int(789)
  ["hours"]=>
  int(123)
  ["minutes"]=>
  int(456)
  ["seconds"]=>
  int(789)
  ["milliseconds"]=>
  int(123)
  ["microseconds"]=>
  int(456)
  ["nanoseconds"]=>
  int(789)
}
-P123Y456M789DT123H456M789.123456789S
object(time\Period)#%d (%d) {
  ["isInverted"]=>
  bool(false)
  ["years"]=>
  int(-123)
  ["months"]=>
  int(-456)
  ["days"]=>
  int(-789)
  ["hours"]=>
  int(-123)
  ["minutes"]=>
  int(-456)
  ["seconds"]=>
  int(-789)
  ["milliseconds"]=>
  int(-123)
  ["microseconds"]=>
  int(-456)
  ["nanoseconds"]=>
  int(-789)
}
P-123Y-456M-789DT-123H-456M-788.1123456789S
object(time\Period)#%d (%d) {
  ["isInverted"]=>
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
P0D
object(time\Period)#%d (%d) {
  ["isInverted"]=>
  bool(true)
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
-P0D
