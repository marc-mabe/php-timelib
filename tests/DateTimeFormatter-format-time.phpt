--TEST--
DateTimeFormatter->format time patterns
--FILE--
<?php

include __DIR__ . '/include.inc';

function format(string $pattern, time\Date|time\Time|time\Zone|time\Zoned $dateTimeZone) {
    try {
        $formatter = new \time\DateTimeFormatter($pattern);
        echo "{$pattern}\t" . stringify($dateTimeZone) . "\n\t";
        echo $formatter->format($dateTimeZone) . "\n";
    } catch (Throwable $e) {
        echo "{$pattern}\t" . stringify($dateTimeZone) . "\n\t" . $e::class . ": {$e->getMessage()}\n";
    }
}

$morning = time\Instant::fromYmd(2024, 3, 15, 9, 5, 7, 123456789);
$afternoon = time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 500000000);
$midnight = time\Instant::fromYmd(2024, 3, 15, 0, 0, 0, 0);
$noon = time\Instant::fromYmd(2024, 3, 15, 12, 0, 0, 0);

echo "=== Hour (0-23) H ===\n";
format("H", $morning);
format("HH", $morning);
format("H", $afternoon);
format("HH", $afternoon);
format("H", $midnight);
format("HH", $midnight);

echo "\n=== Clock Hour (1-12) h ===\n";
format("h", $morning);
format("hh", $morning);
format("h", $afternoon);
format("hh", $afternoon);
format("h", $midnight);
format("hh", $midnight);
format("h", $noon);
format("hh", $noon);

echo "\n=== Hour (0-11) K ===\n";
format("K", $morning);
format("KK", $morning);
format("K", $afternoon);
format("KK", $afternoon);

echo "\n=== Clock Hour (1-24) k ===\n";
format("k", $morning);
format("kk", $morning);
format("k", $afternoon);
format("kk", $afternoon);
format("k", $midnight);
format("kk", $midnight);

echo "\n=== Minute ===\n";
format("m", $morning);
format("mm", $morning);
format("m", $afternoon);
format("mm", $afternoon);

echo "\n=== Second ===\n";
format("s", $morning);
format("ss", $morning);
format("s", $afternoon);
format("ss", $afternoon);

echo "\n=== AM/PM ===\n";
format("a", $morning);
format("a", $afternoon);
format("a", $midnight);
format("a", $noon);

echo "\n=== Fraction of Second ===\n";
format("S", $morning);
format("SS", $morning);
format("SSS", $morning);
format("SSSSSS", $morning);
format("SSSSSSSSS", $morning);

echo "\n=== Combined Time ===\n";
format("HH:mm:ss", $morning);
format("hh:mm:ss a", $morning);
format("HH:mm:ss.SSS", $morning);
format("hh:mm:ss a", $afternoon);

--EXPECT--
=== Hour (0-23) H ===
H	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	9
HH	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09
H	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	14
HH	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	14
H	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	0
HH	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	00

=== Clock Hour (1-12) h ===
h	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	9
hh	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09
h	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	2
hh	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	02
h	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	12
hh	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	12
h	Instant('Fri 2024-03-15 12:00:00', 1710504000, 0)
	12
hh	Instant('Fri 2024-03-15 12:00:00', 1710504000, 0)
	12

=== Hour (0-11) K ===
K	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	9
KK	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09
K	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	2
KK	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	02

=== Clock Hour (1-24) k ===
k	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	9
kk	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09
k	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	14
kk	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	14
k	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	24
kk	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	24

=== Minute ===
m	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	5
mm	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	05
m	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	30
mm	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	30

=== Second ===
s	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	7
ss	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	07
s	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	45
ss	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	45

=== AM/PM ===
a	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	AM
a	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	PM
a	Instant('Fri 2024-03-15 00:00:00', 1710460800, 0)
	AM
a	Instant('Fri 2024-03-15 12:00:00', 1710504000, 0)
	PM

=== Fraction of Second ===
S	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	1
SS	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	12
SSS	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	123
SSSSSS	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	123456
SSSSSSSSS	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	123456789

=== Combined Time ===
HH:mm:ss	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09:05:07
hh:mm:ss a	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09:05:07 AM
HH:mm:ss.SSS	Instant('Fri 2024-03-15 09:05:07.123456789', 1710493507, 123456789)
	09:05:07.123
hh:mm:ss a	Instant('Fri 2024-03-15 14:30:45.5', 1710513045, 500000000)
	02:30:45 PM
