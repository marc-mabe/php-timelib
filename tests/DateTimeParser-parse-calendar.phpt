--TEST--
DateTimeParser->parse with injected calendar
--FILE--
<?php

include __DIR__ . '/include.inc';

$calendar = new time\JulianCalendar();

echo "=== PlainDate with calendar ===\n";
$parserPlainDate = new \time\DateTimeParser('uuuu-MM-dd', $calendar);
$plainDate = $parserPlainDate->parseToPlainDate('2024-03-15');
echo "{$plainDate->year}-{$plainDate->month}-{$plainDate->dayOfMonth}\t" . get_class($plainDate->calendar) . "\n";

echo "\n=== PlainDateTime with calendar ===\n";
$parserPlainDateTime = new \time\DateTimeParser('uuuu-MM-dd HH:mm:ss', $calendar);
$plainDateTime = $parserPlainDateTime->parseToPlainDateTime('2024-03-15 14:30:45');
echo "{$plainDateTime->year}-{$plainDateTime->month}-{$plainDateTime->dayOfMonth}\t"
    . "{$plainDateTime->hour}:{$plainDateTime->minute}:{$plainDateTime->second}\t"
    . get_class($plainDateTime->calendar) . "\n";

echo "\n=== ZonedDateTime with calendar ===\n";
$parserZoned = new \time\DateTimeParser('uuuu-MM-dd HH:mm:ssX', $calendar);
$zoned = $parserZoned->parseToZonedDateTime('2024-03-15 14:30:45-05');
echo "{$zoned->year}-{$zoned->month}-{$zoned->dayOfMonth}\t"
    . "{$zoned->hour}:{$zoned->minute}:{$zoned->second}\t"
    . get_class($zoned->calendar) . "\n";

echo "\n=== Day of year with calendar ===\n";
$parserDayOfYear = new \time\DateTimeParser('uuuu-DDD', $calendar);
$plainDateByDayOfYear = $parserDayOfYear->parseToPlainDate('2024-075');
echo get_class($plainDateByDayOfYear->calendar) . "\n";

echo "\n=== Default ISO parser (no calendar override) ===\n";
$parserIso = new \time\DateTimeParser('uuuu-MM-dd');
$defaultIso = $parserIso->parseToPlainDate('2024-03-15');
echo "{$defaultIso->year}-{$defaultIso->month}-{$defaultIso->dayOfMonth}\t" . get_class($defaultIso->calendar) . "\n";

echo "\n=== parseToInstant with custom calendar ===\n";
$parserWithOffset = new \time\DateTimeParser('uuuu-MM-dd HH:mm:ss', $calendar);
$parsedJulianDate = $parserWithOffset->parseToPlainDateTime('2024-03-15 14:30:45');
$instant = $parserWithOffset->parseToInstant('2024-03-15 14:30:45');
$instantAsIso = $instant->toZonedDateTime();

echo "Parsed calendar date\t"
    . "{$parsedJulianDate->year}-{$parsedJulianDate->month}-{$parsedJulianDate->dayOfMonth}\t"
    . get_class($parsedJulianDate->calendar) . "\n";
echo "Instant converted to Iso\t"
    . "{$instantAsIso->year}-{$instantAsIso->month}-{$instantAsIso->dayOfMonth}\t"
    . get_class($instantAsIso->calendar) . "\n";

--EXPECT--
=== PlainDate with calendar ===
2024-3-15	time\JulianCalendar

=== PlainDateTime with calendar ===
2024-3-15	14:30:45	time\JulianCalendar

=== ZonedDateTime with calendar ===
2024-3-15	14:30:45	time\JulianCalendar

=== Day of year with calendar ===
time\JulianCalendar

=== Default ISO parser (no calendar override) ===
2024-3-15	time\IsoCalendar

=== parseToInstant with custom calendar ===
Parsed calendar date	2024-3-15	time\JulianCalendar
Instant converted to Iso	2024-3-28	time\IsoCalendar
