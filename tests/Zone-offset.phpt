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
Zone('Etc/GMT+1'): Duration('-PTH1')
Zone('CET'): Duration('PTH1')
Zone('CEST'): Duration('PTH2')
Zone('+00:00'): Duration('P0D')
Zone('+12:34'): Duration('PTH12M34')
Zone('-12:34'): Duration('-PTH12M34')
Zone('+12:34:56'): Duration('PTH12M34S56')
Zone('-12:34:56'): Duration('-PTH12M34S56')
Zone('Europe/Berlin'): null
