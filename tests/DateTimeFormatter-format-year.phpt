--TEST--
DateTimeFormatter->format year
--FILE--
<?php

include __DIR__ . '/include.php';

const INT32_MAX = 2147483647;
const INT32_MIN = -2147483647 - 1;

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
format(time\FormatToken::Year->value, time\Instant::fromUnixTimestamp(INT32_MAX));
format(time\FormatToken::Year->value, time\Instant::fromUnixTimestamp(INT32_MIN));

echo "----------------\n";

format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(1912, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(1950, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(1911, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromYmd(2001, 1, 1));
format(time\FormatToken::Year2Digit->value, time\Instant::fromUnixTimestamp(INT32_MAX));
format(time\FormatToken::Year2Digit->value, time\Instant::fromUnixTimestamp(INT32_MIN));

echo "----------------\n";

format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromYmd(1999, 1, 1));
format(time\FormatToken::YearExtended->value, time\Instant::fromUnixTimestamp(INT32_MAX));
format(time\FormatToken::YearExtended->value, time\Instant::fromUnixTimestamp(INT32_MIN));

echo "----------------\n";

format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(1999, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromYmd(1902, 1, 1));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromUnixTimestamp(INT32_MAX));
format(time\FormatToken::YearExtendedSign->value, time\Instant::fromUnixTimestamp(INT32_MIN));

--EXPECT--
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	Y	2000
Instant('Tue 2038-01-19 03:14:07', 2147483647, 0)	Y	2038
Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)	Y	1901
----------------
Instant('Mon 1912-01-01 00:00:00', -1830384000, 0)	y	12
Instant('Sun 1950-01-01 00:00:00', -631152000, 0)	y	50
Instant('Sun 1911-01-01 00:00:00', -1861920000, 0)	y	11
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	y	00
Instant('Mon 2001-01-01 00:00:00', 978307200, 0)	y	01
Instant('Tue 2038-01-19 03:14:07', 2147483647, 0)	y	38
Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)	y	01
----------------
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	x	2000
Instant('Fri 1999-01-01 00:00:00', 915148800, 0)	x	1999
Instant('Tue 2038-01-19 03:14:07', 2147483647, 0)	x	2038
Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)	x	1901
----------------
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	X	+2000
Instant('Fri 1999-01-01 00:00:00', 915148800, 0)	X	+1999
Instant('Wed 1902-01-01 00:00:00', -2145916800, 0)	X	+1902
Instant('Tue 2038-01-19 03:14:07', 2147483647, 0)	X	+2038
Instant('Fri 1901-12-13 20:45:52', -2147483648, 0)	X	+1901
