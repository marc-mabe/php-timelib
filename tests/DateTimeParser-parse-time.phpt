--TEST--
DateTimeParser->parse time patterns
--FILE--
<?php

include __DIR__ . '/include.inc';

function parseTime(string $pattern, string $text) {
    try {
        $parser = new \time\DateTimeParser($pattern);
        $time = $parser->parseToPlainTime($text);
        echo "{$pattern}\t{$text}\t";
        echo stringify($time) . "\n";
    } catch (Throwable $e) {
        echo "{$pattern}\t{$text}\t" . $e::class . ": {$e->getMessage()}\n";
    }
}

echo "=== Hour (0-23) ===\n";
parseTime("H", "9");
parseTime("H", "14");
parseTime("HH", "09");
parseTime("HH", "14");
parseTime("HH", "00");
parseTime("HH", "23");

echo "\n=== Hour (1-12) ===\n";
parseTime("h", "9");
parseTime("hh", "09");
parseTime("hh", "12");

echo "\n=== Hour (0-11) ===\n";
parseTime("K", "9");
parseTime("KK", "09");
parseTime("KK", "00");
parseTime("KK", "11");

echo "\n=== Hour (1-24) ===\n";
parseTime("k", "9");
parseTime("kk", "09");
parseTime("kk", "24");

echo "\n=== Minute ===\n";
parseTime("m", "5");
parseTime("mm", "05");
parseTime("mm", "30");
parseTime("mm", "59");

echo "\n=== Second ===\n";
parseTime("s", "7");
parseTime("ss", "07");
parseTime("ss", "45");
parseTime("ss", "59");

echo "\n=== AM/PM ===\n";
parseTime("hh a", "09 AM");
parseTime("hh a", "09 PM");
parseTime("hh a", "12 AM");
parseTime("hh a", "12 PM");
parseTime("h:mm a", "2:30 PM");

echo "\n=== Fraction of Second ===\n";
parseTime("HH:mm:ss.S", "14:30:45.1");
parseTime("HH:mm:ss.SS", "14:30:45.12");
parseTime("HH:mm:ss.SSS", "14:30:45.123");
parseTime("HH:mm:ss.SSSSSS", "14:30:45.123456");
parseTime("HH:mm:ss.SSSSSSSSS", "14:30:45.123456789");

echo "\n=== Combined time patterns ===\n";
parseTime("HH:mm:ss", "14:30:45");
parseTime("hh:mm:ss a", "02:30:45 PM");
parseTime("H:m:s", "9:5:7");

--EXPECT--
=== Hour (0-23) ===
H	9	PlainTime('09:00:00')
H	14	PlainTime('14:00:00')
HH	09	PlainTime('09:00:00')
HH	14	PlainTime('14:00:00')
HH	00	PlainTime('00:00:00')
HH	23	PlainTime('23:00:00')

=== Hour (1-12) ===
h	9	PlainTime('09:00:00')
hh	09	PlainTime('09:00:00')
hh	12	PlainTime('00:00:00')

=== Hour (0-11) ===
K	9	PlainTime('09:00:00')
KK	09	PlainTime('09:00:00')
KK	00	PlainTime('00:00:00')
KK	11	PlainTime('11:00:00')

=== Hour (1-24) ===
k	9	PlainTime('09:00:00')
kk	09	PlainTime('09:00:00')
kk	24	PlainTime('00:00:00')

=== Minute ===
m	5	PlainTime('00:05:00')
mm	05	PlainTime('00:05:00')
mm	30	PlainTime('00:30:00')
mm	59	PlainTime('00:59:00')

=== Second ===
s	7	PlainTime('00:00:07')
ss	07	PlainTime('00:00:07')
ss	45	PlainTime('00:00:45')
ss	59	PlainTime('00:00:59')

=== AM/PM ===
hh a	09 AM	PlainTime('09:00:00')
hh a	09 PM	PlainTime('21:00:00')
hh a	12 AM	PlainTime('00:00:00')
hh a	12 PM	PlainTime('12:00:00')
h:mm a	2:30 PM	PlainTime('14:30:00')

=== Fraction of Second ===
HH:mm:ss.S	14:30:45.1	PlainTime('14:30:45.1')
HH:mm:ss.SS	14:30:45.12	PlainTime('14:30:45.12')
HH:mm:ss.SSS	14:30:45.123	PlainTime('14:30:45.123')
HH:mm:ss.SSSSSS	14:30:45.123456	PlainTime('14:30:45.123456')
HH:mm:ss.SSSSSSSSS	14:30:45.123456789	PlainTime('14:30:45.123456789')

=== Combined time patterns ===
HH:mm:ss	14:30:45	PlainTime('14:30:45')
hh:mm:ss a	02:30:45 PM	PlainTime('14:30:45')
H:m:s	9:5:7	PlainTime('09:05:07')
