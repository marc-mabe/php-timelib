--TEST--
IsoCalendar->getMonthName() & getMonthAbbreviation()
--FILE--
<?php

include __DIR__ . '/include.php';

$cal = time\IsoCalendar::getInstance();

$years = [-1, 0, 1970];
foreach ($years as $year) {
    for ($month = 1; $month <= 12; $month++) {
        echo "{$year}-{$month}: {$cal->getMonthName($year, $month)}, {$cal->getMonthAbbreviation($year, $month)}\n";
    }
}

--EXPECT--
-1-1: January, Jan
-1-2: February, Feb
-1-3: March, Mar
-1-4: April, Apr
-1-5: May, May
-1-6: June, Jun
-1-7: July, Jul
-1-8: August, Aug
-1-9: September, Sep
-1-10: October, Oct
-1-11: November, Nov
-1-12: December, Dec
0-1: January, Jan
0-2: February, Feb
0-3: March, Mar
0-4: April, Apr
0-5: May, May
0-6: June, Jun
0-7: July, Jul
0-8: August, Aug
0-9: September, Sep
0-10: October, Oct
0-11: November, Nov
0-12: December, Dec
1970-1: January, Jan
1970-2: February, Feb
1970-3: March, Mar
1970-4: April, Apr
1970-5: May, May
1970-6: June, Jun
1970-7: July, Jul
1970-8: August, Aug
1970-9: September, Sep
1970-10: October, Oct
1970-11: November, Nov
1970-12: December, Dec
