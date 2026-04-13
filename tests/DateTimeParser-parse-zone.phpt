--TEST--
DateTimeParser->parse zone patterns
--FILE--
<?php

include __DIR__ . '/include.inc';

function parseZdt(string $pattern, string $text) {
    try {
        $parser = new \time\DateTimeParser($pattern);
        $zdt = $parser->parseToZonedDateTime($text);
        echo "{$pattern}\n\t{$text}\n\t";
        echo stringify($zdt) . "\n";
    } catch (Throwable $e) {
        echo "{$pattern}\n\t{$text}\n\t" . $e::class . ": {$e->getMessage()}\n";
    }
}

echo "=== Zone ID (VV) ===\n";
parseZdt("uuuu-MM-dd'T'HH:mm:ssVV", "2024-03-15T14:30:45America/New_York");
parseZdt("uuuu-MM-dd'T'HH:mm:ssVV", "2024-03-15T14:30:45Z");

echo "\n=== Zone Offset X (Z for zero) ===\n";
parseZdt("uuuu-MM-dd'T'HH:mm:ssX", "2024-03-15T14:30:45-04");
parseZdt("uuuu-MM-dd'T'HH:mm:ssX", "2024-03-15T14:30:45Z");
parseZdt("uuuu-MM-dd'T'HH:mm:ssXX", "2024-03-15T14:30:45-0400");
parseZdt("uuuu-MM-dd'T'HH:mm:ssXX", "2024-03-15T14:30:45Z");
parseZdt("uuuu-MM-dd'T'HH:mm:ssXXX", "2024-03-15T14:30:45-04:00");
parseZdt("uuuu-MM-dd'T'HH:mm:ssXXX", "2024-03-15T14:30:45Z");
parseZdt("uuuu-MM-dd'T'HH:mm:ssXXXXX", "2024-03-15T14:30:45-04:00:30");

echo "\n=== Zone Offset x ===\n";
parseZdt("uuuu-MM-dd'T'HH:mm:ssx", "2024-03-15T14:30:45-04");
parseZdt("uuuu-MM-dd'T'HH:mm:ssx", "2024-03-15T14:30:45+00");
parseZdt("uuuu-MM-dd'T'HH:mm:ssxx", "2024-03-15T14:30:45-0400");
parseZdt("uuuu-MM-dd'T'HH:mm:ssxx", "2024-03-15T14:30:45+0000");
parseZdt("uuuu-MM-dd'T'HH:mm:ssxxx", "2024-03-15T14:30:45-04:00");
parseZdt("uuuu-MM-dd'T'HH:mm:ssxxx", "2024-03-15T14:30:45+00:00");

echo "\n=== Zone Offset Z ===\n";
parseZdt("uuuu-MM-dd'T'HH:mm:ssZ", "2024-03-15T14:30:45-0400");
parseZdt("uuuu-MM-dd'T'HH:mm:ssZZZZ", "2024-03-15T14:30:45GMT-04:00");
parseZdt("uuuu-MM-dd'T'HH:mm:ssZZZZZ", "2024-03-15T14:30:45-04:00");
parseZdt("uuuu-MM-dd'T'HH:mm:ssZZZZZ", "2024-03-15T14:30:45Z");

echo "\n=== Localized Zone Offset O ===\n";
parseZdt("uuuu-MM-dd'T'HH:mm:ssO", "2024-03-15T14:30:45GMT-4");
parseZdt("uuuu-MM-dd'T'HH:mm:ssO", "2024-03-15T14:30:45GMT");
parseZdt("uuuu-MM-dd'T'HH:mm:ssOOOO", "2024-03-15T14:30:45GMT-04:00");
parseZdt("uuuu-MM-dd'T'HH:mm:ssOOOO", "2024-03-15T14:30:45GMT+05:30");

echo "\n=== ISO formats ===\n";
parseZdt("uuuu-MM-dd'T'HH:mm:ssXXX'['VV']'", "2024-03-15T14:30:45-04:00[America/New_York]");

echo "\n=== Zone abbreviations (z / v, ZoneAbbreviation::findZoneByAbbreviation) ===\n";
parseZdt("uuuu-MM-dd HH:mm:ss z", "2024-03-15 14:30:45 EST");
parseZdt("uuuu-MM-dd HH:mm:ss z", "2024-03-15 14:30:45 pst");
parseZdt("uuuu-MM-dd HH:mm:ss z", "2024-03-15 14:30:45 CET");
parseZdt("uuuu-MM-dd HH:mm:ss vvvv", "2024-03-15 14:30:45 PST");
parseZdt("uuuu-MM-dd HH:mm:ss z' TAIL'", "2024-03-15 14:30:45 EST TAIL");

--EXPECT--
=== Zone ID (VV) ===
uuuu-MM-dd'T'HH:mm:ssVV
	2024-03-15T14:30:45America/New_York
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
uuuu-MM-dd'T'HH:mm:ssVV
	2024-03-15T14:30:45Z
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')

=== Zone Offset X (Z for zero) ===
uuuu-MM-dd'T'HH:mm:ssX
	2024-03-15T14:30:45-04
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssX
	2024-03-15T14:30:45Z
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
uuuu-MM-dd'T'HH:mm:ssXX
	2024-03-15T14:30:45-0400
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssXX
	2024-03-15T14:30:45Z
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
uuuu-MM-dd'T'HH:mm:ssXXX
	2024-03-15T14:30:45-04:00
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssXXX
	2024-03-15T14:30:45Z
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
uuuu-MM-dd'T'HH:mm:ssXXXXX
	2024-03-15T14:30:45-04:00:30
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00:30 [-04:00:30]')

=== Zone Offset x ===
uuuu-MM-dd'T'HH:mm:ssx
	2024-03-15T14:30:45-04
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssx
	2024-03-15T14:30:45+00
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
uuuu-MM-dd'T'HH:mm:ssxx
	2024-03-15T14:30:45-0400
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssxx
	2024-03-15T14:30:45+0000
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
uuuu-MM-dd'T'HH:mm:ssxxx
	2024-03-15T14:30:45-04:00
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssxxx
	2024-03-15T14:30:45+00:00
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')

=== Zone Offset Z ===
uuuu-MM-dd'T'HH:mm:ssZ
	2024-03-15T14:30:45-0400
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssZZZZ
	2024-03-15T14:30:45GMT-04:00
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssZZZZZ
	2024-03-15T14:30:45-04:00
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssZZZZZ
	2024-03-15T14:30:45Z
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')

=== Localized Zone Offset O ===
uuuu-MM-dd'T'HH:mm:ssO
	2024-03-15T14:30:45GMT-4
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssO
	2024-03-15T14:30:45GMT
	ZonedDateTime('Fri 2024-03-15 14:30:45 +00:00 [+00:00]')
uuuu-MM-dd'T'HH:mm:ssOOOO
	2024-03-15T14:30:45GMT-04:00
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [-04:00]')
uuuu-MM-dd'T'HH:mm:ssOOOO
	2024-03-15T14:30:45GMT+05:30
	ZonedDateTime('Fri 2024-03-15 14:30:45 +05:30 [+05:30]')

=== ISO formats ===
uuuu-MM-dd'T'HH:mm:ssXXX'['VV']'
	2024-03-15T14:30:45-04:00[America/New_York]
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')

=== Zone abbreviations (z / v, ZoneAbbreviation::findZoneByAbbreviation) ===
uuuu-MM-dd HH:mm:ss z
	2024-03-15 14:30:45 EST
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
uuuu-MM-dd HH:mm:ss z
	2024-03-15 14:30:45 pst
	ZonedDateTime('Fri 2024-03-15 14:30:45 -07:00 [America/Los_Angeles]')
uuuu-MM-dd HH:mm:ss z
	2024-03-15 14:30:45 CET
	ZonedDateTime('Fri 2024-03-15 14:30:45 +01:00 [Europe/Berlin]')
uuuu-MM-dd HH:mm:ss vvvv
	2024-03-15 14:30:45 PST
	ZonedDateTime('Fri 2024-03-15 14:30:45 -07:00 [America/Los_Angeles]')
uuuu-MM-dd HH:mm:ss z' TAIL'
	2024-03-15 14:30:45 EST TAIL
	ZonedDateTime('Fri 2024-03-15 14:30:45 -04:00 [America/New_York]')
