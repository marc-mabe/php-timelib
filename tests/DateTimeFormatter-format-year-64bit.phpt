--TEST--
DateTimeFormatter->format year (64bit)
--SKIPIF--
<?php if (PHP_INT_SIZE != 8) die('skip, this test is for 64 bit only');
--FILE--
<?php

include __DIR__ . '/include.php';

function format(string $format, time\Date|time\Time|time\Zone|time\Zoned $dateTimeZone) {
    $formatter = new \time\DateTimeFormatter($format);
    echo stringify($dateTimeZone) . "\t" . $format . "\t";
    try {
        echo $formatter->format($dateTimeZone) . "\n";
    } catch (Throwable $e) {
        echo $e::class . " {$e->getMessage()}\n";
    }
}

format(time\FormatToken::Year->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::Year->value, time\Instant::fromYmd(0, 1, 1));
format(time\FormatToken::Year->value, time\Instant::fromYmd(-1, 1, 1));
format(time\FormatToken::Year->value, time\Instant::fromUnixTimestamp(PHP_INT_MIN));
format(time\FormatToken::Year->value, time\Instant::fromUnixTimestamp(PHP_INT_MAX));

echo "----------------\n";

format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(1912, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(1950, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(1999, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(0, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(-1, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(-1234, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromUnixTimestamp(PHP_INT_MIN));
format(time\FormatToken::Year2Digit->value, time\Instant::fromUnixTimestamp(PHP_INT_MAX));

echo "----------------\n";

format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(0, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(-1, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(-1234, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(9999, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(-9999, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(10000, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(-10000, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromUnixTimestamp(PHP_INT_MIN));
format(time\FormatToken::YearExtended->value, time\Instant::fromUnixTimestamp(PHP_INT_MAX));

echo "----------------\n";

format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(0, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(-1, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(-1234, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(9999, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(-9999, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(10000, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(-10000, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromUnixTimestamp(PHP_INT_MIN));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromUnixTimestamp(PHP_INT_MAX));

--EXPECT--
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	Y	2000
Instant('Sat 0-01-01 00:00:00', -62167219200, 0)	Y	0
Instant('Fri -1-01-01 00:00:00', -62198755200, 0)	Y	-1
Instant('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)	Y	-292277022657
Instant('Sun 292277026596-12-04 15:30:07', 9223372036854775807, 0)	Y	292277026596
----------------
Instant('Mon 1912-01-01 00:00:00', -1830384000, 0)	y	12
Instant('Sun 1950-01-01 00:00:00', -631152000, 0)	y	50
Instant('Fri 1999-01-01 00:00:00', 915148800, 0)	y	99
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	y	00
Instant('Sat 0-01-01 00:00:00', -62167219200, 0)	y	0
Instant('Fri -1-01-01 00:00:00', -62198755200, 0)	y	-1
Instant('Sat -1234-01-01 00:00:00', -101108476800, 0)	y	-34
Instant('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)	y	-57
Instant('Sun 292277026596-12-04 15:30:07', 9223372036854775807, 0)	y	96
----------------
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	x	2000
Instant('Sat 0-01-01 00:00:00', -62167219200, 0)	x	0000
Instant('Fri -1-01-01 00:00:00', -62198755200, 0)	x	-0001
Instant('Sat -1234-01-01 00:00:00', -101108476800, 0)	x	-1234
Instant('Fri 9999-01-01 00:00:00', 253370764800, 0)	x	9999
Instant('Mon -9999-01-01 00:00:00', -377705116800, 0)	x	-9999
Instant('Sat 10000-01-01 00:00:00', 253402300800, 0)	x	+10000
Instant('Sat -10000-01-01 00:00:00', -377736739200, 0)	x	-10000
Instant('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)	x	-292277022657
Instant('Sun 292277026596-12-04 15:30:07', 9223372036854775807, 0)	x	+292277026596
----------------
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	X	+2000
Instant('Sat 0-01-01 00:00:00', -62167219200, 0)	X	+0000
Instant('Fri -1-01-01 00:00:00', -62198755200, 0)	X	-0001
Instant('Sat -1234-01-01 00:00:00', -101108476800, 0)	X	-1234
Instant('Fri 9999-01-01 00:00:00', 253370764800, 0)	X	+9999
Instant('Mon -9999-01-01 00:00:00', -377705116800, 0)	X	-9999
Instant('Sat 10000-01-01 00:00:00', 253402300800, 0)	X	+10000
Instant('Sat -10000-01-01 00:00:00', -377736739200, 0)	X	-10000
Instant('Sun -292277022657-01-27 08:29:52', -9223372036854775808, 0)	X	-292277022657
Instant('Sun 292277026596-12-04 15:30:07', 9223372036854775807, 0)	X	+292277026596
