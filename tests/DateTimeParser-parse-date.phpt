--TEST--
DateTimeParser->parse date patterns
--FILE--
<?php

include __DIR__ . '/include.inc';

function parseDate(string $pattern, string $text) {
    try {
        $parser = new \time\DateTimeParser($pattern);
        $date = $parser->parseToPlainDate($text);
        echo "{$pattern}\t{$text}\t";
        echo stringify($date) . "\n";
    } catch (Throwable $e) {
        echo "{$pattern}\t{$text}\t" . $e::class . ": {$e->getMessage()}\n";
    }
}

echo "=== Year patterns ===\n";
parseDate("uuuu", "2024");
parseDate("uuuu", "0999");
parseDate("uu", "24");
parseDate("uu", "99");
parseDate("y", "2024");
parseDate("yy", "24");
parseDate("yyyy", "2024");

echo "\n=== Week of Year patterns ===\n";
parseDate("YYYY-'W'ww", "2024-W11");
parseDate("YYYY-'W'w", "2024-W1");
parseDate("YYYY-'W'ww", "2024-W01");
parseDate("YYYY-'W'ww", "2020-W53");

echo "\n=== Month patterns ===\n";
parseDate("M", "3");
parseDate("M", "12");
parseDate("MM", "03");
parseDate("MM", "12");
parseDate("MMM", "Mar");
parseDate("MMM", "mar");
parseDate("MMMM", "March");
parseDate("MMMM", "march");

echo "\n=== Day patterns ===\n";
parseDate("d", "5");
parseDate("d", "15");
parseDate("dd", "05");
parseDate("dd", "15");

echo "\n=== Combined date patterns ===\n";
parseDate("uuuu-MM-dd", "2024-03-15");
parseDate("dd/MM/uuuu", "15/03/2024");
parseDate("MMMM d',' uuuu", "March 15, 2024");
parseDate("d MMMM uuuu", "15 March 2024");
parseDate("uuuu_MM_dd", "2024_03_15");
parseDate("MMMM d, uuuu", "March 15, 2024");

echo "\n=== Day of year ===\n";
parseDate("uuuu-DDD", "2024-075");
parseDate("uuuu-D", "2024-75");

echo "\n=== Day of week (ignored for date calculation) ===\n";
parseDate("EEE',' uuuu-MM-dd", "Fri, 2024-03-15");
parseDate("EEEE',' MMMM d',' uuuu", "Friday, March 15, 2024");

echo "\n=== Era ===\n";
parseDate("G uuuu-MM-dd", "AD 2024-03-15");
parseDate("G uuuu-MM-dd", "BC 0500-06-15");
parseDate("GGGG uuuu", "Anno Domini 2024");
parseDate("GGGG y", "Before Christ 500");

--EXPECT--
=== Year patterns ===
uuuu	2024	PlainDate('Mon 2024-01-01')
uuuu	0999	PlainDate('Tue 999-01-01')
uu	24	PlainDate('Mon 2024-01-01')
uu	99	PlainDate('Thu 2099-01-01')
y	2024	PlainDate('Mon 2024-01-01')
yy	24	PlainDate('Mon 2024-01-01')
yyyy	2024	PlainDate('Mon 2024-01-01')

=== Week of Year patterns ===
YYYY-'W'ww	2024-W11	PlainDate('Mon 2024-01-01')
YYYY-'W'w	2024-W1	PlainDate('Mon 2024-01-01')
YYYY-'W'ww	2024-W01	PlainDate('Mon 2024-01-01')
YYYY-'W'ww	2020-W53	PlainDate('Wed 2020-01-01')

=== Month patterns ===
M	3	PlainDate('Sun 1970-03-01')
M	12	PlainDate('Tue 1970-12-01')
MM	03	PlainDate('Sun 1970-03-01')
MM	12	PlainDate('Tue 1970-12-01')
MMM	Mar	PlainDate('Sun 1970-03-01')
MMM	mar	PlainDate('Sun 1970-03-01')
MMMM	March	PlainDate('Sun 1970-03-01')
MMMM	march	PlainDate('Sun 1970-03-01')

=== Day patterns ===
d	5	PlainDate('Mon 1970-01-05')
d	15	PlainDate('Thu 1970-01-15')
dd	05	PlainDate('Mon 1970-01-05')
dd	15	PlainDate('Thu 1970-01-15')

=== Combined date patterns ===
uuuu-MM-dd	2024-03-15	PlainDate('Fri 2024-03-15')
dd/MM/uuuu	15/03/2024	PlainDate('Fri 2024-03-15')
MMMM d',' uuuu	March 15, 2024	PlainDate('Fri 2024-03-15')
d MMMM uuuu	15 March 2024	PlainDate('Fri 2024-03-15')
uuuu_MM_dd	2024_03_15	PlainDate('Fri 2024-03-15')
MMMM d, uuuu	March 15, 2024	PlainDate('Fri 2024-03-15')

=== Day of year ===
uuuu-DDD	2024-075	PlainDate('Fri 2024-03-15')
uuuu-D	2024-75	PlainDate('Fri 2024-03-15')

=== Day of week (ignored for date calculation) ===
EEE',' uuuu-MM-dd	Fri, 2024-03-15	PlainDate('Fri 2024-03-15')
EEEE',' MMMM d',' uuuu	Friday, March 15, 2024	PlainDate('Fri 2024-03-15')

=== Era ===
G uuuu-MM-dd	AD 2024-03-15	PlainDate('Fri 2024-03-15')
G uuuu-MM-dd	BC 0500-06-15	PlainDate('Sat -499-06-15')
GGGG uuuu	Anno Domini 2024	PlainDate('Mon 2024-01-01')
GGGG y	Before Christ 500	PlainDate('Tue -499-01-01')
