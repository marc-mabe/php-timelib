--TEST--
Period->toIso
--FILE--
<?php

include __DIR__ . '/include.php';

$period = new time\Period(years: 12, months: 34, weeks: 56, days: 78);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(isNegative: true, years: 12, months: 34, weeks: 56, days: 78);
var_dump($period);
echo $period->toIso() . "\n";

$period = new time\Period(years: -12, months: -34, weeks: -56, days: -78);
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
}
P12Y34M56W78D
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
}
-P12Y34M56W78D
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
}
P-12Y-34M-56W-78D
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
}
-P0D
