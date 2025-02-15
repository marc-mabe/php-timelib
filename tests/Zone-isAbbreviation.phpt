--TEST--
Zone->isAbbreviation
--FILE--
<?php

include __DIR__ . '/include.php';

$identifiers = [
    'UTC', 'GMT',
    'CET', 'CEST',
    '+00:00', '-00:00',
    'Europe/Berlin'
];

foreach ($identifiers as $identifier) {
    $zone = time\Zone::fromIdentifier($identifier);
    echo stringify($zone) . ": " . var_export($zone->isAbbreviation, true) . "\n";
}

--EXPECTF--
time\Zone('UTC'): true
time\Zone('GMT'): true
time\Zone('CET'): true
time\Zone('CEST'): true
time\Zone('+00:00'): false
time\Zone('+00:00'): false
time\Zone('Europe/Berlin'): false
