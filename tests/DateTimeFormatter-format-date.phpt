--TEST--
DateTimeFormatter->format date patterns
--FILE--
<?php

include __DIR__ . '/include.php';

function format(string $pattern, time\Date|time\Time|time\Zone|time\Zoned $dateTimeZone) {
    try {
        $formatter = new \time\DateTimeFormatter($pattern);
        echo "{$pattern}\t" . stringify($dateTimeZone) . "\n\t";
        echo $formatter->format($dateTimeZone) . "\n";
    } catch (Throwable $e) {
        echo "{$pattern}\t" . stringify($dateTimeZone) . "\n\t" . $e::class . ": {$e->getMessage()}\n";
    }
}

$date = time\Instant::fromYmd(2024, 3, 15, 14, 30, 45, 123456789);

echo "=== Year ===\n";
format("u", $date);
format("uu", $date);
format("uuuu", $date);
format("uuuuu", $date);
format("y", $date);
format("yy", $date);
format("yyyy", $date);
// Two-digit y/Y keep the minus sign for BCE extended years (otherwise -499 and +1999 both become "99").
$bce499 = time\PlainDate::fromYmd(-499, 6, 15);
$bce1 = time\PlainDate::fromYmd(-1, 6, 15);
format("yy", $bce499);
format("yy", $bce1);
format("YY", $bce499);

echo "\n=== Related Gregorian year (r) ===\n";
// r uses GregorianCalendar extended years (no year 0; 1 BCE is -1), for the same local civil date as u/M/d — with ZonedDateTime that is the zone-adjusted local date.
$z2024 = time\ZonedDateTime::fromYmd(2024, 3, 15, 14, 30, 45, 123456789, new time\ZoneOffset(0));
format("r", $z2024);
format("rrrr", $z2024);
// IsoCalendar year 0 (astronomical) corresponds to Gregorian year -1 (1 BCE).
// PlainDate avoids Unix-second overflow on 32-bit (ZonedDateTime would wrap those instants).
$isoYear0 = time\PlainDate::fromYmd(0, 6, 15, time\IsoCalendar::getInstance());
format('"u="uuuu-MM-dd", r="rrrr', $isoYear0);
// Julian 1899-12-25 is the same day as Gregorian 1900-01-06; r must follow Gregorian, not the Julian calendar year.
$julNewYear = time\PlainDate::fromYmd(1899, 12, 25, new time\JulianCalendar());
format("'u='uuuu', r='rrrr", $julNewYear);
// Same absolute instant, different offset: local Julian date shifts, so r can change while the Instant is unchanged.
$julianZoneEdge = time\Instant::fromYmd(1999, 12, 31, 10, 0, 0, 0);
$zJulUtc = time\ZonedDateTime::fromInstant($julianZoneEdge, new time\ZoneOffset(0), new time\JulianCalendar());
$zJulPlus14 = time\ZonedDateTime::fromInstant($julianZoneEdge, new time\ZoneOffset(14 * 3600), new time\JulianCalendar());
format('"local "uuuu-MM-dd", r="rrrr', $zJulUtc);
format('"local "uuuu-MM-dd", r="rrrr', $zJulPlus14);

$week1 = time\Instant::fromYmd(2024, 1, 1, 12, 0, 0, 0);
$week53 = time\Instant::fromYmd(2020, 12, 31, 12, 0, 0, 0);
$yearBoundaryStart = time\Instant::fromYmd(2019, 12, 30, 12, 0, 0, 0);
$yearBoundaryEnd = time\Instant::fromYmd(2021, 1, 1, 12, 0, 0, 0);

echo "\n=== Week of Year ===\n";
format("w", $date);
format("ww", $date);
format("w", $week1);
format("ww", $week1);
format("w", $week53);
format("ww", $week53);

echo "\n=== Week-Based Year ===\n";
format("Y", $date);
format("YY", $date);
format("YYYY", $date);
format("'calendar year:' uuuu '/ week-based year:' YYYY", $yearBoundaryStart);
format("'calendar year:' uuuu '/ week-based year:' YYYY", $yearBoundaryEnd);

echo "\n=== Month ===\n";
format("M", $date);
format("MM", $date);
format("MMM", $date);
format("MMMM", $date);
format("MMMMM", $date);
format("L", $date);
format("LL", $date);
format("LLL", $date);
format("LLLL", $date);
format("LLLLL", $date);

echo "\n=== Day ===\n";
format("d", $date);
format("dd", $date);
format("D", $date);
format("DD", $date);
format("DDD", $date);

echo "\n=== Modified Julian day ===\n";
// MJD = getJdnByYmd(year, month, day) − 2400001 (1970-01-01 ISO is 40587).
format("g", $date);
format("gggggg", $date);
$mjdEpoch = time\Instant::fromYmd(1970, 1, 1, 0, 0, 0, 0);
format("g", $mjdEpoch);

echo "\n=== Day of Week ===\n";
format("E", $date);
format("EE", $date);
format("EEE", $date);
format("EEEE", $date);
format("EEEEE", $date);
format("e", $date);
format("ee", $date);
format("eee", $date);
format("eeee", $date);
format("eeeee", $date);

echo "\n=== Quarter ===\n";
format("Q", $date);
format("QQ", $date);
format("QQQ", $date);
format("QQQQ", $date);

echo "\n=== Week of Month ===\n";
// W = week within the month in successive blocks of getDaysInWeekByYmd(); F = ordinal n-th occurrence of this weekday in the month (CLDR).
format("W", $date);
format("F", $date);
format("W", $week1);
format("F", $week1);
$thirdTuesdayMar = time\Instant::fromYmd(2024, 3, 19, 12, 0, 0, 0);
format("F", $thirdTuesdayMar);

echo "\n=== Era ===\n";
format("G", $date);
format("GG", $date);
format("GGG", $date);
format("GGGG", $date);
format("GGGGG", $date);

--EXPECT--
=== Year ===
u	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
uu	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
uuuu	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
uuuuu	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	02024
y	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
yy	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	24
yyyy	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
yy	PlainDate('Sat -499-06-15')
	99
yy	PlainDate('Tue -1-06-15')
	01
YY	PlainDate('Sat -499-06-15')
	-99

=== Related Gregorian year (r) ===
r	ZonedDateTime('Fri 2024-03-15 14:30:45.123456789 +00:00 [+00:00]')
	2024
rrrr	ZonedDateTime('Fri 2024-03-15 14:30:45.123456789 +00:00 [+00:00]')
	2024
"u="uuuu-MM-dd", r="rrrr	PlainDate('Thu 0-06-15')
	u=0000-06-15, r=-0001
'u='uuuu', r='rrrr	PlainDate('Fri 1899-12-25')
	u=1899, r=1900
"local "uuuu-MM-dd", r="rrrr	ZonedDateTime('Thu 1999-12-18 10:00:00 +00:00 [+00:00]')
	local 1999-12-18, r=1999
"local "uuuu-MM-dd", r="rrrr	ZonedDateTime('Fri 1999-12-19 00:00:00 +14:00 [+14:00]')
	local 1999-12-19, r=2000

=== Week of Year ===
w	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	11
ww	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	11
w	Instant('Mon 2024-01-01 12:00:00', 1704110400, 0)
	1
ww	Instant('Mon 2024-01-01 12:00:00', 1704110400, 0)
	01
w	Instant('Thu 2020-12-31 12:00:00', 1609416000, 0)
	53
ww	Instant('Thu 2020-12-31 12:00:00', 1609416000, 0)
	53

=== Week-Based Year ===
Y	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
YY	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	24
YYYY	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	2024
'calendar year:' uuuu '/ week-based year:' YYYY	Instant('Mon 2019-12-30 12:00:00', 1577707200, 0)
	calendar year: 2019 / week-based year: 2020
'calendar year:' uuuu '/ week-based year:' YYYY	Instant('Fri 2021-01-01 12:00:00', 1609502400, 0)
	calendar year: 2021 / week-based year: 2020

=== Month ===
M	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	3
MM	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	03
MMM	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Mar
MMMM	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	March
MMMMM	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	M
L	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	3
LL	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	03
LLL	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Mar
LLLL	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	March
LLLLL	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	M

=== Day ===
d	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	15
dd	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	15
D	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	75
DD	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	75
DDD	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	075

=== Modified Julian day ===
g	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	60384
gggggg	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	060384
g	Instant('Thu 1970-01-01 00:00:00', 0, 0)
	40587

=== Day of Week ===
E	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Fri
EE	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Fri
EEE	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Fri
EEEE	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Friday
EEEEE	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	F
e	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	5
ee	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	05
eee	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Fri
eeee	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Friday
eeeee	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	F

=== Quarter ===
Q	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	1
QQ	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	01
QQQ	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Q1
QQQQ	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	1st quarter

=== Week of Month ===
W	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	3
F	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	3
W	Instant('Mon 2024-01-01 12:00:00', 1704110400, 0)
	1
F	Instant('Mon 2024-01-01 12:00:00', 1704110400, 0)
	1
F	Instant('Tue 2024-03-19 12:00:00', 1710849600, 0)
	3

=== Era ===
G	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	AD
GG	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	AD
GGG	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	AD
GGGG	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	Anno Domini
GGGGG	Instant('Fri 2024-03-15 14:30:45.123456789', 1710513045, 123456789)
	A
