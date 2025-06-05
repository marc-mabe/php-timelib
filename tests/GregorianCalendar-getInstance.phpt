--TEST--
GregorianCalendar::getInstance()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal1 = time\GregorianCalendar::getInstance();
$cal2 = time\GregorianCalendar::getInstance();

var_dump($cal1, $cal1 === $cal2);

--EXPECTF--
object(time\GregorianCalendar)#%d (%d) {
}
bool(true)
