--TEST--
Zone: Time zone offsets and transitions of buildin zones
--FILE--
<?php

include __DIR__ . '/include.php';

$instants = [
    time\Instant::fromUnixTimestampTuple([0, 0]),
    time\Instant::fromYmd(2000, 1, 2, 3, 4, 5),
    time\Instant::fromYmd(2000, 7, 6, 5, 4, 3),
    time\Instant::fromYmd(2000, 3, 26, 1, 0, 0),  // exact instant of sdt -> dst in Europe/Berlin
    time\Instant::fromYmd(1999, 10, 31, 1, 0, 0), // exact instant of dst -> std in Europe/Berlin
];
$zones = [
    'UTC',
    'GMT',
    'Europe/Berlin',
    'Europe/London',
];

foreach ($zones as $zoneId) {
    $info = time\Zone::fromIdentifier($zoneId)->info;

    foreach ($instants as $instant) {
        echo $zoneId . ' ' . stringify($instant) . ":\n";

        echo '  fixedOffset: ' . stringify($info->fixedOffset) . "\n";
        echo '  getOffsetAt: ' . stringify($info->getOffsetAt($instant)) . "\n";
        echo '  getTransitionAt: ' . stringify($info->getTransitionAt($instant)) . "\n";
        echo '  getNextTransition: ' . stringify($info->getNextTransition($instant)) . "\n";
        echo '  getPrevTransition: ' . stringify($info->getPrevTransition($instant)) . "\n";

        echo "  Transitions -/+ 1 year:\n";
        $prevYear = $instant->sub(new time\Duration(hours: 24 * 365));
        $nextYear = $instant->add(new time\Duration(hours: 24 * 365));
        foreach ($info->getTransitions($prevYear, $nextYear) as $transition) {
            echo '    ' . stringify($transition) . "\n";
        }
    }
}

--EXPECT--
UTC Instant('Thu 1970-01-01 00:00:00', 0, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Instant('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Instant('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Instant('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Instant('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Instant('Thu 1970-01-01 00:00:00', 0, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Instant('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Instant('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Instant('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Instant('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
Europe/Berlin Instant('Thu 1970-01-01 00:00:00', 0, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1949-10-02 01:00:00', -639010800, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 1980-04-06 01:00:00', 323830800, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1949-10-02 01:00:00', -639010800, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1949-10-02 01:00:00', -639010800, 0))
Europe/Berlin Instant('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
Europe/Berlin Instant('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+02:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2001-03-25 01:00:00', 985482000, 0))
Europe/Berlin Instant('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+02:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2001-03-25 01:00:00', 985482000, 0))
Europe/Berlin Instant('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
Europe/London Instant('Thu 1970-01-01 00:00:00', 0, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sat 1968-10-26 23:00:00', -37242000, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1971-10-31 02:00:00', 57722400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sat 1968-10-26 23:00:00', -37242000, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sat 1968-10-26 23:00:00', -37242000, 0))
Europe/London Instant('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
Europe/London Instant('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2001-03-25 01:00:00', 985482000, 0))
Europe/London Instant('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2001-03-25 01:00:00', 985482000, 0))
Europe/London Instant('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), instant=Instant('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+00:00'), instant=Instant('Sun 2000-10-29 01:00:00', 972781200, 0))
