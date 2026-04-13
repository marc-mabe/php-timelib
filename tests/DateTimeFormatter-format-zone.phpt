--TEST--
DateTimeFormatter->format zone patterns
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

$zdt = time\ZonedDateTime::fromYmd(2024, 3, 15, 14, 30, 45, 0, time\Zone::fromIdentifier('America/New_York'));
$zdtPlus = time\ZonedDateTime::fromYmd(2024, 3, 15, 14, 30, 45, 0, time\Zone::fromIdentifier('Asia/Kolkata'));
$zdtUtc = time\ZonedDateTime::fromYmd(2024, 3, 15, 14, 30, 45, 0, new time\ZoneOffset(0));
$zdtOdd = time\ZonedDateTime::fromYmd(2024, 3, 15, 14, 30, 45, 0, new time\ZoneOffset(5 * 3600 + 45 * 60 + 30));

echo "=== Zone ID (VV) ===\n";
format("VV", $zdt);
format("VV", $zdtPlus);
format("VV", $zdtUtc);

echo "\n=== Zone Name (z) ===\n";
format("z", $zdt);
format("zz", $zdt);
format("zzz", $zdt);
format("zzzz", $zdt);

echo "\n=== Localized Zone Offset (O) ===\n";
format("O", $zdt);
format("OOOO", $zdt);
format("O", $zdtPlus);
format("OOOO", $zdtPlus);
format("O", $zdtUtc);
format("OOOO", $zdtUtc);
format("O", $zdtOdd);
format("OOOO", $zdtOdd);

echo "\n=== Zone Offset X (Z for zero) ===\n";
format("X", $zdt);
format("XX", $zdt);
format("XXX", $zdt);
format("XXXX", $zdt);
format("XXXXX", $zdt);
format("X", $zdtUtc);
format("XX", $zdtUtc);
format("XXX", $zdtUtc);

echo "\n=== Zone Offset x ===\n";
format("x", $zdt);
format("xx", $zdt);
format("xxx", $zdt);
format("xxxx", $zdt);
format("xxxxx", $zdt);
format("x", $zdtUtc);
format("xx", $zdtUtc);
format("xxx", $zdtUtc);

echo "\n=== Zone Offset Z ===\n";
format("Z", $zdt);
format("ZZ", $zdt);
format("ZZZ", $zdt);
format("ZZZZ", $zdt);
format("ZZZZZ", $zdt);
format("ZZZZZ", $zdtUtc);

echo "\n=== Offset with seconds ===\n";
format("XXXXX", $zdtOdd);
format("xxxxx", $zdtOdd);
format("ZZZZZ", $zdtOdd);
format("OOOO", $zdtOdd);

--EXPECT--
=== Zone ID (VV) ===
VV	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	America/New_York
VV	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:30 [Asia/Kolkata]')
	Asia/Kolkata
VV	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	+00:00

=== Zone Name (z) ===
z	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	EWT
zz	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	EWT
zzz	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	EWT
zzzz	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	EWT

=== Localized Zone Offset (O) ===
O	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	GMT-4
OOOO	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	GMT-04:00
O	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:30 [Asia/Kolkata]')
	GMT+5:30
OOOO	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:30 [Asia/Kolkata]')
	GMT+05:30
O	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	GMT
OOOO	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	GMT
O	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:45:30 [+05:45:30]')
	GMT+5:45:30
OOOO	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:45:30 [+05:45:30]')
	GMT+05:45:30

=== Zone Offset X (Z for zero) ===
X	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04
XX	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
XXX	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04:00
XXXX	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
XXXXX	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04:00
X	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	Z
XX	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	Z
XXX	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	Z

=== Zone Offset x ===
x	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04
xx	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
xxx	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04:00
xxxx	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
xxxxx	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04:00
x	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	+00
xx	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	+0000
xxx	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	+00:00

=== Zone Offset Z ===
Z	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
ZZ	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
ZZZ	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-0400
ZZZZ	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	GMT-04:00
ZZZZZ	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
	-04:00
ZZZZZ	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
	Z

=== Offset with seconds ===
XXXXX	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:45:30 [+05:45:30]')
	+05:45:30
xxxxx	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:45:30 [+05:45:30]')
	+05:45:30
ZZZZZ	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:45:30 [+05:45:30]')
	+05:45:30
OOOO	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:45:30 [+05:45:30]')
	GMT+05:45:30
