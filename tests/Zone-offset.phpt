--TEST--
Zone->offset
--FILE--
<?php

include __DIR__ . '/include.php';

$identifiers = [
    'GMT', 'UTC', 'Etc/GMT', 'Etc/GMT+1',
    'CET', 'CEST',
    '+00:00', '+12:34', '-12:34', '+12:34:56', '-12:34:56',
    'Europe/Berlin'
];

foreach ($identifiers as $identifier) {
    $zone = time\Zone::fromIdentifier($identifier);
    echo stringify($zone) . ": " . stringify($zone->offset) . "\n";
}

--EXPECTF--
Zone('GMT'): Period('P0D')
Zone('UTC'): Period('P0D')
Zone('Etc/GMT'): Period('P0D')
Zone('Etc/GMT+1'): Period('-PT1H')
Zone('CET'): Period('PT1H')
Zone('CEST'): Period('PT2H')
Zone('+00:00'): Period('P0D')
Zone('+12:34'): Period('PT12H34M')
Zone('-12:34'): Period('-PT12H34M')
Zone('+12:34:56'): Period('PT12H34M56S')
Zone('-12:34:56'): Period('-PT12H34M56S')
Zone('Europe/Berlin'): null
