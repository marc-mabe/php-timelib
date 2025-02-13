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
Zone('UTC'): true
Zone('GMT'): true
Zone('CET'): true
Zone('CEST'): true
Zone('+00:00'): false
Zone('+00:00'): false
Zone('Europe/Berlin'): false
