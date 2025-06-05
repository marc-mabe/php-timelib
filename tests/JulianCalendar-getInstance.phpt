--TEST--
JulianCalendar::getInstance()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal1 = time\JulianCalendar::getInstance();
$cal2 = time\JulianCalendar::getInstance();

var_dump($cal1, $cal1 === $cal2);

--EXPECTF--
object(time\JulianCalendar)#%d (%d) {
}
bool(true)
