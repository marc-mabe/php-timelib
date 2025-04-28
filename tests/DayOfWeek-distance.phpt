--TEST--
DayOfWeek->distance
--FILE--
<?php

include __DIR__ . '/include.php';

foreach (time\DayOfWeek::cases() as $dow) {
    foreach (time\DayOfWeek::cases() as $other) {
        echo $dow->name . ' to ' . $other->name . ': ' . $dow->distance($other) . "\n";
    }
}

--EXPECT--
Monday to Monday: 0
Monday to Tuesday: 1
Monday to Wednesday: 2
Monday to Thursday: 3
Monday to Friday: -3
Monday to Saturday: -2
Monday to Sunday: -1
Tuesday to Monday: -1
Tuesday to Tuesday: 0
Tuesday to Wednesday: 1
Tuesday to Thursday: 2
Tuesday to Friday: 3
Tuesday to Saturday: -3
Tuesday to Sunday: -2
Wednesday to Monday: -2
Wednesday to Tuesday: -1
Wednesday to Wednesday: 0
Wednesday to Thursday: 1
Wednesday to Friday: 2
Wednesday to Saturday: 3
Wednesday to Sunday: -3
Thursday to Monday: -3
Thursday to Tuesday: -2
Thursday to Wednesday: -1
Thursday to Thursday: 0
Thursday to Friday: 1
Thursday to Saturday: 2
Thursday to Sunday: 3
Friday to Monday: 3
Friday to Tuesday: -3
Friday to Wednesday: -2
Friday to Thursday: -1
Friday to Friday: 0
Friday to Saturday: 1
Friday to Sunday: 2
Saturday to Monday: 2
Saturday to Tuesday: 3
Saturday to Wednesday: -3
Saturday to Thursday: -2
Saturday to Friday: -1
Saturday to Saturday: 0
Saturday to Sunday: 1
Sunday to Monday: 1
Sunday to Tuesday: 2
Sunday to Wednesday: 3
Sunday to Thursday: -3
Sunday to Friday: -2
Sunday to Saturday: -1
Sunday to Sunday: 0
