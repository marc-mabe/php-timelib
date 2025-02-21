--TEST--
Moment->add() and Moment->sub()
--FILE--
<?php

include __DIR__ . '/include.php';

$moments = [
    time\Moment::fromYmd(1969, 12, 25, 1),
    time\Moment::fromYmd(1969, 12, 26, 2),
    time\Moment::fromYmd(1969, 12, 27, 3),
    time\Moment::fromYmd(1969, 12, 28, 4),
    time\Moment::fromYmd(1969, 12, 29, 5),
    time\Moment::fromYmd(1969, 12, 30, 6),
    time\Moment::fromYmd(1969, 12, 31, 7),
    time\Moment::fromYmd(1970, 1, 1, 8),
    time\Moment::fromYmd(1970, 1, 2, 9),
    time\Moment::fromYmd(1970, 1, 3, 10),
    time\Moment::fromYmd(1970, 1, 4, 11),
    time\Moment::fromYmd(1970, 1, 5, 12),
    time\Moment::fromYmd(1970, 1, 6, 13),
    time\Moment::fromYmd(1970, 1, 7, 14),
    time\Moment::fromYmd(2025, 02, 21, 15),
];

foreach ($moments as $moment) {
    echo stringify($moment) . ": " . $moment->dayOfWeek->name . "\n";
}

--EXPECT--
Moment('Thu 1969-12-25 01:00:00', -601200, 0): Thursday
Moment('Fri 1969-12-26 02:00:00', -511200, 0): Friday
Moment('Sat 1969-12-27 03:00:00', -421200, 0): Saturday
Moment('Sun 1969-12-28 04:00:00', -331200, 0): Sunday
Moment('Mon 1969-12-29 05:00:00', -241200, 0): Monday
Moment('Tue 1969-12-30 06:00:00', -151200, 0): Tuesday
Moment('Wed 1969-12-31 07:00:00', -61200, 0): Wednesday
Moment('Thu 1970-01-01 08:00:00', 28800, 0): Thursday
Moment('Fri 1970-01-02 09:00:00', 118800, 0): Friday
Moment('Sat 1970-01-03 10:00:00', 208800, 0): Saturday
Moment('Sun 1970-01-04 11:00:00', 298800, 0): Sunday
Moment('Mon 1970-01-05 12:00:00', 388800, 0): Monday
Moment('Tue 1970-01-06 13:00:00', 478800, 0): Tuesday
Moment('Wed 1970-01-07 14:00:00', 568800, 0): Wednesday
Moment('Fri 2025-02-21 15:00:00', 1740150000, 0): Friday
