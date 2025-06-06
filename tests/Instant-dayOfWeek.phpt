--TEST--
Instant->add() and Instant->sub()
--FILE--
<?php

include __DIR__ . '/include.php';

$instants = [
    time\Instant::fromYmd(1969, 12, 25, 1),
    time\Instant::fromYmd(1969, 12, 26, 2),
    time\Instant::fromYmd(1969, 12, 27, 3),
    time\Instant::fromYmd(1969, 12, 28, 4),
    time\Instant::fromYmd(1969, 12, 29, 5),
    time\Instant::fromYmd(1969, 12, 30, 6),
    time\Instant::fromYmd(1969, 12, 31, 7),
    time\Instant::fromYmd(1970, 1, 1, 8),
    time\Instant::fromYmd(1970, 1, 2, 9),
    time\Instant::fromYmd(1970, 1, 3, 10),
    time\Instant::fromYmd(1970, 1, 4, 11),
    time\Instant::fromYmd(1970, 1, 5, 12),
    time\Instant::fromYmd(1970, 1, 6, 13),
    time\Instant::fromYmd(1970, 1, 7, 14),
    time\Instant::fromYmd(2025, 02, 21, 15),
];

foreach ($instants as $instant) {
    echo stringify($instant) . ": " . $instant->dayOfWeek . "\n";
}

--EXPECT--
Instant('Thu 1969-12-25 01:00:00', -601200, 0): 4
Instant('Fri 1969-12-26 02:00:00', -511200, 0): 5
Instant('Sat 1969-12-27 03:00:00', -421200, 0): 6
Instant('Sun 1969-12-28 04:00:00', -331200, 0): 7
Instant('Mon 1969-12-29 05:00:00', -241200, 0): 1
Instant('Tue 1969-12-30 06:00:00', -151200, 0): 2
Instant('Wed 1969-12-31 07:00:00', -61200, 0): 3
Instant('Thu 1970-01-01 08:00:00', 28800, 0): 4
Instant('Fri 1970-01-02 09:00:00', 118800, 0): 5
Instant('Sat 1970-01-03 10:00:00', 208800, 0): 6
Instant('Sun 1970-01-04 11:00:00', 298800, 0): 7
Instant('Mon 1970-01-05 12:00:00', 388800, 0): 1
Instant('Tue 1970-01-06 13:00:00', 478800, 0): 2
Instant('Wed 1970-01-07 14:00:00', 568800, 0): 3
Instant('Fri 2025-02-21 15:00:00', 1740150000, 0): 5
