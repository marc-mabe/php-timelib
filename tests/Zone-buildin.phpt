--TEST--
Tine zone offsets and transitions of buildin zones
--FILE--
<?php

include __DIR__ . '/include.php';

$moments = [
    time\Moment::fromYmd(2000, 1, 2, 3, 4, 5),
    time\Moment::fromYmd(2000, 7, 6, 5, 4, 3),
    time\Moment::fromYmd(2000, 3, 26, 1, 0, 0),  // exact moment of sdt -> dst in Europe/Berlin
    time\Moment::fromYmd(1999, 10, 31, 1, 0, 0), // exact moment of dst -> std in Europe/Berlin
];
$zones = [
    'UTC',
    'GMT',
    'Europe/Berlin',
];

foreach ($zones as $zoneId) {
    $info = time\Zone::fromIdentifier($zoneId)->info;

    foreach ($moments as $moment) {
        echo $zoneId . ' ' . stringify($moment) . ":\n";

        echo '  fixedOffset: ' . stringify($info->fixedOffset) . "\n";
        echo '  getOffsetAt: ' . stringify($info->getOffsetAt($moment)) . "\n";
        echo '  getTransitionAt: ' . stringify($info->getTransitionAt($moment)) . "\n";
        echo '  getNextTransition: ' . stringify($info->getNextTransition($moment)) . "\n";
        echo '  getPrevTransition: ' . stringify($info->getPrevTransition($moment)) . "\n";

        echo "  Transitions -/+ 1 year:\n";
        $prevYear = $moment->sub(new time\Duration(hours: 24 * 365));
        $nextYear = $moment->add(new time\Duration(hours: 24 * 365));
        foreach ($info->getTransitions($prevYear, $nextYear) as $transition) {
            echo '    ' . stringify($transition) . "\n";
        }
    }
}

--EXPECT--
UTC Moment('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Moment('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Moment('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
UTC Moment('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Moment('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Moment('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Moment('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
GMT Moment('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: time\ZoneOffset('+00:00')
  getOffsetAt: time\ZoneOffset('+00:00')
  getTransitionAt: NULL
  getNextTransition: NULL
  getPrevTransition: NULL
  Transitions -/+ 1 year:
Europe/Berlin Moment('Sun 2000-01-02 03:04:05', 946782245, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 2000-10-29 01:00:00', 972781200, 0))
Europe/Berlin Moment('Thu 2000-07-06 05:04:03', 962859843, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+02:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 2000-10-29 01:00:00', 972781200, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 2000-10-29 01:00:00', 972781200, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2001-03-25 01:00:00', 985482000, 0))
Europe/Berlin Moment('Sun 2000-03-26 01:00:00', 954032400, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+02:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 2000-10-29 01:00:00', 972781200, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 2000-10-29 01:00:00', 972781200, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2001-03-25 01:00:00', 985482000, 0))
Europe/Berlin Moment('Sun 1999-10-31 01:00:00', 941331600, 0):
  fixedOffset: NULL
  getOffsetAt: time\ZoneOffset('+01:00')
  getTransitionAt: ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
  getNextTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
  getPrevTransition: ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 1999-03-28 01:00:00', 922582800, 0))
  Transitions -/+ 1 year:
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 1999-03-28 01:00:00', 922582800, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 1999-10-31 01:00:00', 941331600, 0))
    ZoneTransition(offset=time\ZoneOffset('+02:00'), moment=Moment('Sun 2000-03-26 01:00:00', 954032400, 0))
    ZoneTransition(offset=time\ZoneOffset('+01:00'), moment=Moment('Sun 2000-10-29 01:00:00', 972781200, 0))
