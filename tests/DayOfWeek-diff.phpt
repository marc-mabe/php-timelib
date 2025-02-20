--TEST--
DayOfWeek->diff
--FILE--
<?php

include __DIR__ . '/include.php';

foreach (time\DayOfWeek::cases() as $dow) {
    foreach (time\DayOfWeek::cases() as $other) {
        echo $dow->name . ' to ' . $other->name . ': ' . stringify($dow->diff($other)) . "\n";
    }
}

--EXPECT--
Monday to Monday: Period('P0D')
Monday to Tuesday: Period('P1D')
Monday to Wednesday: Period('P2D')
Monday to Thursday: Period('P3D')
Monday to Friday: Period('P4D')
Monday to Saturday: Period('P5D')
Monday to Sunday: Period('P6D')
Tuesday to Monday: Period('P-1D')
Tuesday to Tuesday: Period('P0D')
Tuesday to Wednesday: Period('P1D')
Tuesday to Thursday: Period('P2D')
Tuesday to Friday: Period('P3D')
Tuesday to Saturday: Period('P4D')
Tuesday to Sunday: Period('P5D')
Wednesday to Monday: Period('P-2D')
Wednesday to Tuesday: Period('P-1D')
Wednesday to Wednesday: Period('P0D')
Wednesday to Thursday: Period('P1D')
Wednesday to Friday: Period('P2D')
Wednesday to Saturday: Period('P3D')
Wednesday to Sunday: Period('P4D')
Thursday to Monday: Period('P-3D')
Thursday to Tuesday: Period('P-2D')
Thursday to Wednesday: Period('P-1D')
Thursday to Thursday: Period('P0D')
Thursday to Friday: Period('P1D')
Thursday to Saturday: Period('P2D')
Thursday to Sunday: Period('P3D')
Friday to Monday: Period('P-4D')
Friday to Tuesday: Period('P-3D')
Friday to Wednesday: Period('P-2D')
Friday to Thursday: Period('P-1D')
Friday to Friday: Period('P0D')
Friday to Saturday: Period('P1D')
Friday to Sunday: Period('P2D')
Saturday to Monday: Period('P-5D')
Saturday to Tuesday: Period('P-4D')
Saturday to Wednesday: Period('P-3D')
Saturday to Thursday: Period('P-2D')
Saturday to Friday: Period('P-1D')
Saturday to Saturday: Period('P0D')
Saturday to Sunday: Period('P1D')
Sunday to Monday: Period('P-6D')
Sunday to Tuesday: Period('P-5D')
Sunday to Wednesday: Period('P-4D')
Sunday to Thursday: Period('P-3D')
Sunday to Friday: Period('P-2D')
Sunday to Saturday: Period('P-1D')
Sunday to Sunday: Period('P0D')
