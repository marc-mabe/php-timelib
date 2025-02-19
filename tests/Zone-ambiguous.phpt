--TEST--
Disallow ambiguous time zone identifiers
--FILE--
<?php

include __DIR__ . '/include.php';

$zones = [
    'CET',
    'CEST',
    'IST',
    'GMT',
    'UTC',
    'Etc/GMT',
    'Etc/GMT+1',
    '+00:00',
    '+12:34',
    'Europe/Berlin',
];

foreach ($zones as $zoneId) {
    echo $zoneId . ": ";

    try {
        echo time\Zone::fromIdentifier($zoneId)->identifier . "\n";
    } catch (Throwable $e) {
        echo $e::class . ': ' . $e->getMessage() . "\n";
    }
}

--EXPECT--
CET: RuntimeException: Time zone identifier 'CET' is ambiguous
CEST: RuntimeException: Time zone identifier 'CEST' is ambiguous
IST: RuntimeException: Time zone identifier 'IST' is ambiguous
GMT: GMT
UTC: UTC
Etc/GMT: Etc/GMT
Etc/GMT+1: Etc/GMT+1
+00:00: +00:00
+12:34: +12:34
Europe/Berlin: Europe/Berlin
