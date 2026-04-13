--TEST--
DateTimeFormatter format errors
--FILE--
<?php

include __DIR__ . '/include.inc';

function formatError(string $pattern) {
    try {
        $formatter = new \time\DateTimeFormatter($pattern);
        echo "{$pattern}\tOK\n";
    } catch (Throwable $e) {
        echo "{$pattern}\t" . $e::class . ": " . $e->getMessage() . "\n";
    }
}

echo "=== Unquoted literals ===\n";
formatError("yyyy@MM@dd");
formatError("yyyy~MM~dd");

echo "\n=== Unquoted characters ===\n";
formatError("f");
formatError("#");
formatError("{");
formatError("}");

echo "\n=== Unterminated quotes ===\n";
formatError("'unterminated");
formatError("\"unterminated");

echo "\n=== Invalid symbol counts ===\n";
formatError("VVV");
formatError("zzzzz");
formatError("OO");
formatError("XXXXXX");
formatError("ZZZZZZ");
formatError("FF");
formatError("ddd");
formatError("HHH");
formatError("DDDD");
formatError("SSSSSSSSSS");

echo "\n=== Unterminated optional sections ===\n";
formatError("[yyyy");
formatError("uuuu-MM-dd['T'HH:mm:ss");

echo "\n=== Unexpected closing bracket ===\n";
formatError("]yyyy");
formatError("uuuu]-MM-dd");

--EXPECT--
=== Unquoted literals ===
yyyy@MM@dd	time\InvalidFormatError: Unquoted literal '@' in pattern: yyyy@MM@dd. Literals must be quoted with single or double quotes.
yyyy~MM~dd	time\InvalidFormatError: Unquoted literal '~' in pattern: yyyy~MM~dd. Literals must be quoted with single or double quotes.

=== Unquoted characters ===
f	time\InvalidFormatError: Unquoted literal 'f' in pattern: f. Literals must be quoted with single or double quotes.
#	time\InvalidFormatError: Unquoted literal '#' in pattern: #. Literals must be quoted with single or double quotes.
{	time\InvalidFormatError: Unquoted literal '{' in pattern: {. Literals must be quoted with single or double quotes.
}	time\InvalidFormatError: Unquoted literal '}' in pattern: }. Literals must be quoted with single or double quotes.

=== Unterminated quotes ===
'unterminated	time\InvalidFormatError: Unterminated quote in pattern: 'unterminated
"unterminated	time\InvalidFormatError: Unterminated quote in pattern: "unterminated

=== Invalid symbol counts ===
VVV	time\InvalidFormatError: Pattern letter 'V' must appear exactly twice in pattern: VVV
zzzzz	time\InvalidFormatError: Pattern letter 'z' cannot appear more than 4 times in pattern: zzzzz
OO	time\InvalidFormatError: Pattern letter 'O' must appear exactly 1 or 4 times in pattern: OO
XXXXXX	time\InvalidFormatError: Pattern letter 'X' cannot appear more than 5 times in pattern: XXXXXX
ZZZZZZ	time\InvalidFormatError: Pattern letter 'Z' cannot appear more than 5 times in pattern: ZZZZZZ
FF	time\InvalidFormatError: Pattern letter 'F' can only appear once in pattern: FF
ddd	time\InvalidFormatError: Pattern letter 'd' cannot appear more than twice in pattern: ddd
HHH	time\InvalidFormatError: Pattern letter 'H' cannot appear more than twice in pattern: HHH
DDDD	time\InvalidFormatError: Pattern letter 'D' cannot appear more than 3 times in pattern: DDDD
SSSSSSSSSS	time\InvalidFormatError: Pattern letter 'S' cannot appear more than 9 times in pattern: SSSSSSSSSS

=== Unterminated optional sections ===
[yyyy	time\InvalidFormatError: Unterminated optional section in pattern: [yyyy
uuuu-MM-dd['T'HH:mm:ss	time\InvalidFormatError: Unterminated optional section in pattern: uuuu-MM-dd['T'HH:mm:ss

=== Unexpected closing bracket ===
]yyyy	time\InvalidFormatError: Unexpected ']' in pattern: ]yyyy
uuuu]-MM-dd	time\InvalidFormatError: Unexpected ']' in pattern: uuuu]-MM-dd
