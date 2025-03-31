--TEST--
Period->toIso
--FILE--
<?php

include __DIR__ . '/include.php';

$period = new time\Period(
    years: 12, months: 34, weeks: 56, days: 78,
    hours: 123, minutes: 456, seconds: 789,
    milliseconds: 123, microseconds: 456, nanoseconds: 789,
);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(
    isNegative: true,
    years: 12, months: 34, weeks: 56, days: 78,
    hours: 123, minutes: 456, seconds: 789,
    milliseconds: 123, microseconds: 456, nanoseconds: 789,
);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(
    years: -12, months: -34, weeks: -56, days: -78,
    hours: -123, minutes: -456, seconds: -789,
    milliseconds: -123, microseconds: -456, nanoseconds: -789,
);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period();
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(isNegative: true);
var_dump($period);
echo $period->toIso() . "\n";

--EXPECTF--
object(time\Period)#%d (%d) {
  ["isNegative"]=>
  bool(false)
  ["years"]=>
  int(12)
  ["months"]=>
  int(34)
  ["weeks"]=>
  int(56)
  ["days"]=>
  int(78)
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
P12Y34M56W78DT123H456M789.123456789S
object(time\Period)#%d (%d) {
  ["isNegative"]=>
  bool(true)
  ["years"]=>
  int(12)
  ["months"]=>
  int(34)
  ["weeks"]=>
  int(56)
  ["days"]=>
  int(78)
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
-P12Y34M56W78DT123H456M789.123456789S
object(time\Period)#%d (%d) {
  ["isNegative"]=>
  bool(false)
  ["years"]=>
  int(-12)
  ["months"]=>
  int(-34)
  ["weeks"]=>
  int(-56)
  ["days"]=>
  int(-78)
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
P-12Y-34M-56W-78DT-123H-456M-789.123456789S
object(time\Period)#%d (%d) {
  ["isNegative"]=>
  bool(false)
  ["years"]=>
  int(0)
  ["months"]=>
  int(0)
  ["weeks"]=>
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
  ["isNegative"]=>
  bool(true)
  ["years"]=>
  int(0)
  ["months"]=>
  int(0)
  ["weeks"]=>
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
