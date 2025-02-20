--TEST--
DayOfWeek->getPrevious & DayOfWeek->getNext
--FILE--
<?php

include __DIR__ . '/include.php';

foreach (time\DayOfWeek::cases() as $dow) {
    echo $dow->name . ': ' . $dow->getPrevious()->name . ' - ' . $dow->getNext()->name . "\n";
}

--EXPECT--
Monday: Sunday - Tuesday
Tuesday: Monday - Wednesday
Wednesday: Tuesday - Thursday
Thursday: Wednesday - Friday
Friday: Thursday - Saturday
Saturday: Friday - Sunday
Sunday: Saturday - Monday
