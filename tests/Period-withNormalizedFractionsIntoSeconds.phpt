--TEST--
Period->withNormalizedFractionsIntoSeconds
--FILE--
<?php

include __DIR__ . '/include.php';

$periods = [
    new time\Period(
        years: 123, months: 456, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 123, microseconds: 456, nanoseconds: 789,
    ),
    new time\Period(
        years: -123, months: -456, days: -789,
        hours: -123, minutes: -456, seconds: -789,
        milliseconds: -123, microseconds: -456, nanoseconds: -789,
    ),
    new time\Period(
        years: 123, months: 456, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: -123, microseconds: -456, nanoseconds: -789,
    ),
    new time\Period(
        years: 123, months: 456, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 1234, microseconds: 2345, nanoseconds: 3456,
    ),
    new time\Period(
        years: 123, months: 456, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: -1234, microseconds: 2345, nanoseconds: -3456,
    ),
    new time\Period(
        years: 123, months: 456, days: 789,
        hours: 123, minutes: 456, seconds: 789,
        milliseconds: 1234, microseconds: -2345, nanoseconds: 3456,
    ),
];

foreach ($periods as $period) {
    $normalized = $period->withNormalizedFractionsIntoSeconds();
    echo stringify($period)
        . "({$period->seconds}s {$period->milliseconds}ms {$period->microseconds}us {$period->nanoseconds}ns)"
        . ' -> ' . stringify($normalized)
        . "({$normalized->seconds}s {$normalized->milliseconds}ms {$normalized->microseconds}us {$normalized->nanoseconds}ns)"
        . "\n";
}

--EXPECTF--
Period('P123Y456M789DT123H456M789.123456789S')(789s 123ms 456us 789ns) -> Period('P123Y456M789DT123H456M789.123456789S')(789s 123ms 456us 789ns)
Period('P-123Y-456M-789DT-123H-456M-789.123456789S')(-789s -123ms -456us -789ns) -> Period('P-123Y-456M-789DT-123H-456M-789.123456789S')(-789s -123ms -456us -789ns)
Period('P123Y456M789DT123H456M788.876543211S')(789s -123ms -456us -789ns) -> Period('P123Y456M789DT123H456M788.876543211S')(788s 876ms 543us 211ns)
Period('P123Y456M789DT123H456M790.236348456S')(789s 1234ms 2345us 3456ns) -> Period('P123Y456M789DT123H456M790.236348456S')(790s 236ms 348us 456ns)
Period('P123Y456M789DT123H456M787.768341544S')(789s -1234ms 2345us -3456ns) -> Period('P123Y456M789DT123H456M787.768341544S')(787s 768ms 341us 544ns)
Period('P123Y456M789DT123H456M790.231658456S')(789s 1234ms -2345us 3456ns) -> Period('P123Y456M789DT123H456M790.231658456S')(790s 231ms 658us 456ns)
