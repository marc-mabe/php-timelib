--TEST--
DateTimeFormatter->format unix timestamp
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

format(time\FormatToken::SecondsSinceUnixEpoch->value, time\Instant::fromYmd(2000, 1, 1));
format(time\FormatToken::SecondsSinceUnixEpoch->value, time\Instant::fromYmd(1970, 1, 1));
format(time\FormatToken::SecondsSinceUnixEpoch->value, time\Instant::fromYmd(1969, 12, 31, 23, 59, 59, 999999999));
format(time\FormatToken::SecondsSinceUnixEpoch->value, time\Instant::fromUnixTimestamp(PHP_INT_MAX));
format(time\FormatToken::SecondsSinceUnixEpoch->value, time\Instant::fromUnixTimestamp(PHP_INT_MIN));
--EXPECTF--
Instant('Sat 2000-01-01 00:00:00', 946684800, 0)	U	946684800
Instant('Thu 1970-01-01 00:00:00', 0, 0)	U	0
Instant('Wed 1969-12-31 23:59:59.999999999', -1, 999999999)	U	-1
Instant('%s', %d, 0)	U	%d
Instant('%s', -%d, 0)	U	-%d
