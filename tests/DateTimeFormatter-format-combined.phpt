--TEST--
DateTimeFormatter->format combined patterns (ISO formats)
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

$zdt = time\ZonedDateTime::fromYmd(2024, 3, 15, 14, 30, 45, 123000000, time\Zone::fromIdentifier('America/New_York'));
$date = time\PlainDate::fromYmd(2024, 3, 15);
$time = time\PlainTime::fromHms(14, 30, 45);

echo "=== ISO-like formats ===\n";
format("uuuu-MM-dd", $zdt);
format("uuuu-MM-dd'T'HH:mm:ss", $zdt);
format("uuuu-MM-dd'T'HH:mm:ssXXX", $zdt);
format("uuuu-MM-dd'T'HH:mm:ss.SSSXXX", $zdt);
format("uuuu-MM-dd'T'HH:mm:ssXXX'['VV']'", $zdt);

echo "\n=== RFC 1123 like ===\n";
format("EEE',' d MMM uuuu HH:mm:ss z", $zdt);

echo "\n=== Human readable ===\n";
format("EEEE',' MMMM d',' uuuu", $zdt);
format("MMMM d',' uuuu 'at' h:mm a", $zdt);
format("d MMMM uuuu", $zdt);

echo "\n=== Unquoted _ and , literals ===\n";
format("uuuu_MM_dd", $zdt);
format("MMMM d, uuuu", $zdt);

echo "\n=== Optional sections ===\n";
format("uuuu-MM-dd['T'HH:mm:ss]", $zdt);
format("uuuu-MM-dd'T'HH:mm:ss[.SSS]", $zdt);
format("uuuu-MM-dd[ VV]", $zdt);
format("uuuu-MM-dd['T'HH:mm:ss]", $date);
format("uuuu-MM-dd[ VV]", $date);
format("HH:mm:ss[ 'on' uuuu-MM-dd]", $time);

echo "\n=== Nested optional sections ===\n";
format("uuuu-MM-dd['T'HH:mm[:ss]]", $zdt);

echo "\n=== Multiple optional sections ===\n";
format("[uuuu-MM-dd]['T'][HH:mm:ss]", $date);
format("[uuuu-MM-dd]['T'][HH:mm:ss]", $time);
format("[uuuu-MM-dd]['T'][HH:mm:ss]", $zdt);

--EXPECT--
=== ISO-like formats ===
uuuu-MM-dd	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15
uuuu-MM-dd'T'HH:mm:ss	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45
uuuu-MM-dd'T'HH:mm:ssXXX	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45-04:00
uuuu-MM-dd'T'HH:mm:ss.SSSXXX	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45.123-04:00
uuuu-MM-dd'T'HH:mm:ssXXX'['VV']'	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45-04:00[America/New_York]

=== RFC 1123 like ===
EEE',' d MMM uuuu HH:mm:ss z	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	Fri, 15 Mar 2024 14:30:45 EWT

=== Human readable ===
EEEE',' MMMM d',' uuuu	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	Friday, March 15, 2024
MMMM d',' uuuu 'at' h:mm a	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	March 15, 2024 at 2:30 PM
d MMMM uuuu	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	15 March 2024

=== Unquoted _ and , literals ===
uuuu_MM_dd	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024_03_15
MMMM d, uuuu	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	March 15, 2024

=== Optional sections ===
uuuu-MM-dd['T'HH:mm:ss]	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45
uuuu-MM-dd'T'HH:mm:ss[.SSS]	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45.123
uuuu-MM-dd[ VV]	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15 America/New_York
uuuu-MM-dd['T'HH:mm:ss]	PlainDate('Fri 2024-03-15')
	2024-03-15
uuuu-MM-dd[ VV]	PlainDate('Fri 2024-03-15')
	2024-03-15
HH:mm:ss[ 'on' uuuu-MM-dd]	PlainTime('14:30:45')
	14:30:45

=== Nested optional sections ===
uuuu-MM-dd['T'HH:mm[:ss]]	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45

=== Multiple optional sections ===
[uuuu-MM-dd]['T'][HH:mm:ss]	PlainDate('Fri 2024-03-15')
	2024-03-15T
[uuuu-MM-dd]['T'][HH:mm:ss]	PlainTime('14:30:45')
	T14:30:45
[uuuu-MM-dd]['T'][HH:mm:ss]	ZonedDateTime('Fri 2024-03-15 14:30:45.123 -04:00 [America/New_York]')
	2024-03-15T14:30:45
