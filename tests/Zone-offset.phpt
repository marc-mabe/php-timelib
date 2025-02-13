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
Zone('GMT'): Duration('P0D')
Zone('UTC'): Duration('P0D')
Zone('Etc/GMT'): Duration('P0D')
Zone('Etc/GMT+1'): Duration('-PT1H')
Zone('CET'): Duration('PT1H')
Zone('CEST'): Duration('PT2H')
Zone('+00:00'): Duration('P0D')
Zone('+12:34'): Duration('PT12H34M')
Zone('-12:34'): Duration('-PT12H34M')
Zone('+12:34:56'): Duration('PT12H34M56S')
Zone('-12:34:56'): Duration('-PT12H34M56S')
Zone('Europe/Berlin'): null
