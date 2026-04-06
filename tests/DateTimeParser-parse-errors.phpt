--TEST--
DateTimeParser parse errors
--FILE--
<?php

include __DIR__ . '/include.php';

function parseError(string $pattern, string $text) {
    try {
        $parser = new \time\DateTimeParser($pattern);
        $result = $parser->parse($text);
        echo "{$pattern}\t{$text}\tOK\n";
    } catch (Throwable $e) {
        echo "{$pattern}\t{$text}\t" . $e::class . ": " . $e->getMessage() . "\n";
    }
}

echo "=== Invalid format patterns ===\n";
parseError("yyyy@MM@dd", "2024@03@15");
parseError("unknown", "text");
parseError("#", "text");

echo "\n=== Mismatched literals ===\n";
parseError("uuuu-MM-dd", "2024/03/15");
parseError("uuuu-MM-dd", "2024-03-");

echo "\n=== Invalid values ===\n";
parseError("uuuu", "abc");
parseError("MM", "ab");

echo "\n=== Unexpected end of text ===\n";
parseError("uuuu-MM-dd", "2024-03");
parseError("HH:mm:ss", "14:30");

echo "\n=== Unparsed text remaining ===\n";
parseError("uuuu", "2024extra");
parseError("uuuu-MM-dd", "2024-03-15extra");

echo "\n=== Invalid month names ===\n";
parseError("MMMM", "Marching");
parseError("MMM", "Xyz");

echo "\n=== Invalid AM/PM ===\n";
parseError("a", "XX");
parseError("hh a", "12 XX");

echo "\n=== Invalid zone offsets ===\n";
parseError("X", "ABC");
parseError("XXX", "+99:99");

--EXPECTF--
=== Invalid format patterns ===
yyyy@MM@dd	2024@03@15	time\InvalidFormatError: Unquoted literal '@' in pattern: yyyy@MM@dd. Literals must be quoted with single or double quotes.
unknown	text	time\InvalidFormatError: Unquoted literal 'n' in pattern: unknown. Literals must be quoted with single or double quotes.
#	text	time\InvalidFormatError: Unquoted literal '#' in pattern: #. Literals must be quoted with single or double quotes.

=== Mismatched literals ===
uuuu-MM-dd	2024/03/15	time\InvalidValueException: Expected '-' at position 4 in '2024/03/15'
uuuu-MM-dd	2024-03-	time\InvalidValueException: %s

=== Invalid values ===
uuuu	abc	time\InvalidValueException: %s
MM	ab	time\InvalidValueException: %s

=== Unexpected end of text ===
uuuu-MM-dd	2024-03	time\InvalidValueException: %s
HH:mm:ss	14:30	time\InvalidValueException: %s

=== Unparsed text remaining ===
uuuu	2024extra	time\InvalidValueException: Unparsed text '2024extra' at position 4
uuuu-MM-dd	2024-03-15extra	time\InvalidValueException: Unparsed text '2024-03-15extra' at position 10

=== Invalid month names ===
MMMM	Marching	time\InvalidValueException: %s
MMM	Xyz	time\InvalidValueException: %s

=== Invalid AM/PM ===
a	XX	time\InvalidValueException: %s
hh a	12 XX	time\InvalidValueException: %s

=== Invalid zone offsets ===
X	ABC	time\InvalidValueException: %s
XXX	+99:99	time\RangeError: %s
