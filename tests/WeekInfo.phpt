--TEST--
WeekInfo
--FILE--
<?php

include __DIR__ . '/include.php';

$defs = [
    time\WeekInfo::fromIso(),
    new time\WeekInfo(\time\DayOfWeek::Sunday, 4),
];

$dates = [
    time\LocalDate::fromYmd(2005, 1, 1),
    time\LocalDate::fromYmd(2005, 1, 2),
    time\LocalDate::fromYmd(2005, 12, 31),
    time\LocalDate::fromYmd(2006, 1, 1),
    time\LocalDate::fromYmd(2006, 1, 2),
    time\LocalDate::fromYmd(2006, 12, 25),
    time\LocalDate::fromYmd(2006, 12, 26),
    time\LocalDate::fromYmd(2006, 12, 27),
    time\LocalDate::fromYmd(2006, 12, 28),
    time\LocalDate::fromYmd(2006, 12, 29),
    time\LocalDate::fromYmd(2006, 12, 30),
    time\LocalDate::fromYmd(2006, 12, 31),
    time\LocalDate::fromYmd(2007, 1, 1),
    time\LocalDate::fromYmd(2007, 1, 2),
    time\LocalDate::fromYmd(2007, 1, 3),
    time\LocalDate::fromYmd(2007, 1, 4),
    time\LocalDate::fromYmd(2007, 1, 5),
    time\LocalDate::fromYmd(2007, 1, 6),
    time\LocalDate::fromYmd(2007, 1, 7),
    time\LocalDate::fromYmd(2007, 1, 8),
    time\LocalDate::fromYmd(2007, 12, 30),
    time\LocalDate::fromYmd(2007, 12, 31),
    time\LocalDate::fromYmd(2008, 1, 1),
    time\LocalDate::fromYmd(2008, 1, 2),
    time\LocalDate::fromYmd(2008, 12, 28),
    time\LocalDate::fromYmd(2008, 12, 29),
    time\LocalDate::fromYmd(2008, 12, 30),
    time\LocalDate::fromYmd(2008, 12, 31),
    time\LocalDate::fromYmd(2009, 1, 1),
    time\LocalDate::fromYmd(2009, 12, 31),
    time\LocalDate::fromYmd(2010, 1, 1),
    time\LocalDate::fromYmd(2010, 1, 2),
    time\LocalDate::fromYmd(2010, 1, 3),
    time\LocalDate::fromYmd(2010, 1, 4),
];

foreach ($defs as $def) {
    foreach ($dates as $date) {
        echo stringify($date)
            . "\tW{$def->getWeekOfYear($date)}\t{$def->getYearOfWeek($date)}"
            . "\t" . stringify($def) . "\n";
    }

    echo "-----\n";
}

--EXPECT--
LocalDate('Sat 2005-01-01')	W53	2004	WeekInfo(Monday, 4)
LocalDate('Sun 2005-01-02')	W53	2004	WeekInfo(Monday, 4)
LocalDate('Sat 2005-12-31')	W52	2005	WeekInfo(Monday, 4)
LocalDate('Sun 2006-01-01')	W52	2005	WeekInfo(Monday, 4)
LocalDate('Mon 2006-01-02')	W1	2006	WeekInfo(Monday, 4)
LocalDate('Mon 2006-12-25')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Tue 2006-12-26')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Wed 2006-12-27')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Thu 2006-12-28')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Fri 2006-12-29')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Sat 2006-12-30')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Sun 2006-12-31')	W52	2006	WeekInfo(Monday, 4)
LocalDate('Mon 2007-01-01')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Tue 2007-01-02')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Wed 2007-01-03')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Thu 2007-01-04')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Fri 2007-01-05')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Sat 2007-01-06')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Sun 2007-01-07')	W1	2007	WeekInfo(Monday, 4)
LocalDate('Mon 2007-01-08')	W2	2007	WeekInfo(Monday, 4)
LocalDate('Sun 2007-12-30')	W52	2007	WeekInfo(Monday, 4)
LocalDate('Mon 2007-12-31')	W1	2008	WeekInfo(Monday, 4)
LocalDate('Tue 2008-01-01')	W1	2008	WeekInfo(Monday, 4)
LocalDate('Wed 2008-01-02')	W1	2008	WeekInfo(Monday, 4)
LocalDate('Sun 2008-12-28')	W52	2008	WeekInfo(Monday, 4)
LocalDate('Mon 2008-12-29')	W1	2009	WeekInfo(Monday, 4)
LocalDate('Tue 2008-12-30')	W1	2009	WeekInfo(Monday, 4)
LocalDate('Wed 2008-12-31')	W1	2009	WeekInfo(Monday, 4)
LocalDate('Thu 2009-01-01')	W1	2009	WeekInfo(Monday, 4)
LocalDate('Thu 2009-12-31')	W53	2009	WeekInfo(Monday, 4)
LocalDate('Fri 2010-01-01')	W53	2009	WeekInfo(Monday, 4)
LocalDate('Sat 2010-01-02')	W53	2009	WeekInfo(Monday, 4)
LocalDate('Sun 2010-01-03')	W53	2009	WeekInfo(Monday, 4)
LocalDate('Mon 2010-01-04')	W1	2010	WeekInfo(Monday, 4)
-----
LocalDate('Sat 2005-01-01')	W52	2004	WeekInfo(Sunday, 4)
LocalDate('Sun 2005-01-02')	W1	2005	WeekInfo(Sunday, 4)
LocalDate('Sat 2005-12-31')	W52	2005	WeekInfo(Sunday, 4)
LocalDate('Sun 2006-01-01')	W1	2006	WeekInfo(Sunday, 4)
LocalDate('Mon 2006-01-02')	W1	2006	WeekInfo(Sunday, 4)
LocalDate('Mon 2006-12-25')	W52	2006	WeekInfo(Sunday, 4)
LocalDate('Tue 2006-12-26')	W52	2006	WeekInfo(Sunday, 4)
LocalDate('Wed 2006-12-27')	W52	2006	WeekInfo(Sunday, 4)
LocalDate('Thu 2006-12-28')	W52	2006	WeekInfo(Sunday, 4)
LocalDate('Fri 2006-12-29')	W52	2006	WeekInfo(Sunday, 4)
LocalDate('Sat 2006-12-30')	W52	2006	WeekInfo(Sunday, 4)
LocalDate('Sun 2006-12-31')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Mon 2007-01-01')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Tue 2007-01-02')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Wed 2007-01-03')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Thu 2007-01-04')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Fri 2007-01-05')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Sat 2007-01-06')	W1	2007	WeekInfo(Sunday, 4)
LocalDate('Sun 2007-01-07')	W2	2007	WeekInfo(Sunday, 4)
LocalDate('Mon 2007-01-08')	W2	2007	WeekInfo(Sunday, 4)
LocalDate('Sun 2007-12-30')	W1	2008	WeekInfo(Sunday, 4)
LocalDate('Mon 2007-12-31')	W1	2008	WeekInfo(Sunday, 4)
LocalDate('Tue 2008-01-01')	W1	2008	WeekInfo(Sunday, 4)
LocalDate('Wed 2008-01-02')	W1	2008	WeekInfo(Sunday, 4)
LocalDate('Sun 2008-12-28')	W53	2008	WeekInfo(Sunday, 4)
LocalDate('Mon 2008-12-29')	W53	2008	WeekInfo(Sunday, 4)
LocalDate('Tue 2008-12-30')	W53	2008	WeekInfo(Sunday, 4)
LocalDate('Wed 2008-12-31')	W53	2008	WeekInfo(Sunday, 4)
LocalDate('Thu 2009-01-01')	W53	2008	WeekInfo(Sunday, 4)
LocalDate('Thu 2009-12-31')	W52	2009	WeekInfo(Sunday, 4)
LocalDate('Fri 2010-01-01')	W52	2009	WeekInfo(Sunday, 4)
LocalDate('Sat 2010-01-02')	W52	2009	WeekInfo(Sunday, 4)
LocalDate('Sun 2010-01-03')	W1	2010	WeekInfo(Sunday, 4)
LocalDate('Mon 2010-01-04')	W1	2010	WeekInfo(Sunday, 4)
-----
