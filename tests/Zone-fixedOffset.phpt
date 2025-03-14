--TEST--
Zone->fixedOffset
--FILE--
<?php

include __DIR__ . '/include.php';

$zones = [
    time\Zone::fromIdentifier('GMT'),
    time\Zone::fromIdentifier('UTC'),
    time\Zone::fromIdentifier('Etc/GMT'),
    time\Zone::fromIdentifier('Etc/GMT+1'),
    time\Zone::fromIdentifier('+00:00'),
    time\Zone::fromIdentifier('+12:34'),
    time\Zone::fromIdentifier('-12:34'),
    time\Zone::fromIdentifier('+12:34:56'),
    time\Zone::fromIdentifier('-12:34:56'),
    time\Zone::fromIdentifier('Europe/Berlin'),
    time\ZoneOffset::fromDuration(new time\Duration(hours: -1, minutes: -2, seconds: -3)),
];

foreach ($zones as $zone) {
    echo stringify($zone) . ": " . stringify($zone->fixedOffset) . "\n";
}

--EXPECTF--
time\Zone('GMT'): time\ZoneOffset('+00:00')
time\Zone('UTC'): time\ZoneOffset('+00:00')
time\Zone('Etc/GMT'): time\ZoneOffset('+00:00')
time\Zone('Etc/GMT+1'): time\ZoneOffset('-01:00')
time\ZoneOffset('+00:00'): time\ZoneOffset('+00:00')
time\ZoneOffset('+12:34'): time\ZoneOffset('+12:34')
time\ZoneOffset('-12:34'): time\ZoneOffset('-12:34')
time\ZoneOffset('+12:34:56'): time\ZoneOffset('+12:34:56')
time\ZoneOffset('-12:34:56'): time\ZoneOffset('-12:34:56')
time\Zone('Europe/Berlin'): NULL
time\ZoneOffset('-01:02:03'): time\ZoneOffset('-01:02:03')
