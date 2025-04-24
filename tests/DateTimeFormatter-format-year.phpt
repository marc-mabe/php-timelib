--TEST--
DateTimeFormatter->format year
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

format(time\FormatToken::Year->value, time\Moment::fromYmd(2000, 1, 1));
format(time\FormatToken::Year->value, time\Moment::fromYmd(0, 1, 1));
format(time\FormatToken::Year->value, time\Moment::fromYmd(-1, 1, 1));

format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(1912, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(1950, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(1999, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(2000, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(0, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(-1, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Moment::fromYmd(-1234, 1, 1));

echo "----------------\n";

format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(2000, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(0, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(-1, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(-1234, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(9999, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(-9999, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(10000, 1, 1));
format(time\FormatToken::YearExtended->value, time\Moment::fromYmd(-10000, 1, 1));

echo "----------------\n";

format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(2000, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(0, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(-1, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(-1234, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(9999, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(-9999, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(10000, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Moment::fromYmd(-10000, 1, 1));

--EXPECT--
Moment('Sat 2000-01-01 00:00:00', 946684800, 0)	Y	2000
Moment('Sat 0-01-01 00:00:00', -62167219200, 0)	Y	0
Moment('Fri -1-01-01 00:00:00', -62198755200, 0)	Y	-1
Moment('Mon 1912-01-01 00:00:00', -1830384000, 0)	y	12
Moment('Sun 1950-01-01 00:00:00', -631152000, 0)	y	50
Moment('Fri 1999-01-01 00:00:00', 915148800, 0)	y	99
Moment('Sat 2000-01-01 00:00:00', 946684800, 0)	y	00
Moment('Sat 0-01-01 00:00:00', -62167219200, 0)	y	0
Moment('Fri -1-01-01 00:00:00', -62198755200, 0)	y	-1
Moment('Sat -1234-01-01 00:00:00', -101108476800, 0)	y	-34
----------------
Moment('Sat 2000-01-01 00:00:00', 946684800, 0)	x	2000
Moment('Sat 0-01-01 00:00:00', -62167219200, 0)	x	0000
Moment('Fri -1-01-01 00:00:00', -62198755200, 0)	x	-0001
Moment('Sat -1234-01-01 00:00:00', -101108476800, 0)	x	-1234
Moment('Fri 9999-01-01 00:00:00', 253370764800, 0)	x	9999
Moment('Mon -9999-01-01 00:00:00', -377705116800, 0)	x	-9999
Moment('Sat 10000-01-01 00:00:00', 253402300800, 0)	x	+10000
Moment('Sat -10000-01-01 00:00:00', -377736739200, 0)	x	-10000
----------------
Moment('Sat 2000-01-01 00:00:00', 946684800, 0)	X	+2000
Moment('Sat 0-01-01 00:00:00', -62167219200, 0)	X	+0000
Moment('Fri -1-01-01 00:00:00', -62198755200, 0)	X	-0001
Moment('Sat -1234-01-01 00:00:00', -101108476800, 0)	X	-1234
Moment('Fri 9999-01-01 00:00:00', 253370764800, 0)	X	+9999
Moment('Mon -9999-01-01 00:00:00', -377705116800, 0)	X	-9999
Moment('Sat 10000-01-01 00:00:00', 253402300800, 0)	X	+10000
Moment('Sat -10000-01-01 00:00:00', -377736739200, 0)	X	-10000
