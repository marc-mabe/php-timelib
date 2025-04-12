--TEST--
DateTimeFormatter->format with Momented
--FILE--
<?php

include __DIR__ . '/include.php';

$zoneBln = time\Zone::fromIdentifier('Europe/Berlin');
$zoneNy  = time\Zone::fromIdentifier('America/New_York');
$moment  = time\Moment::fromYmd(2001, 2, 3, 4, 5, 6, 987654321);

$zdt = $moment->toZonedDateTime($zoneNy);
$momentedWithZone = new class($moment, $zoneNy) implements time\Momented, time\Zoned {
    public function __construct(
        public readonly time\Moment $moment,
        public readonly time\Zone $zone,
    ) {}
};

echo "Formatter without zone:\n";
$formatter = new \time\DateTimeFormatter('D Y-m-d H:i:sf P [e]');
echo "  " . stringify($zdt) . ' = ' . $formatter->format($zdt) . "\n";
echo "  Momented(".stringify($momentedWithZone->moment).")+Zoned(" . stringify($momentedWithZone->zone) . ') = ' . $formatter->format($momentedWithZone) . "\n";
echo "  " . stringify($moment) . ' = ' . $formatter->format($moment) . "\n";

echo "Formatter with zone:\n";
$formatter = new \time\DateTimeFormatter('D Y-m-d H:i:sf P [e]', zone: $zoneBln);
echo "  " . stringify($zdt) . ' = ' . $formatter->format($zdt) . "\n";
echo "  Momented(".stringify($momentedWithZone->moment).")+Zoned(" . stringify($momentedWithZone->zone) . ') = ' . $formatter->format($momentedWithZone) . "\n";
echo "  " . stringify($moment) . ' = ' . $formatter->format($moment) . "\n";

--EXPECT--
Formatter without zone:
  ZonedDateTime('Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]') = Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]
  Momented(Moment('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321))+Zoned(time\Zone('America/New_York')) = Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]
  Moment('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321) = Sat 2001-02-03 04:05:06.987654321 +00:00 [+00:00]
Formatter with zone:
  ZonedDateTime('Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]') = Sat 2001-02-03 05:05:06.987654321 +01:00 [Europe/Berlin]
  Momented(Moment('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321))+Zoned(time\Zone('America/New_York')) = Sat 2001-02-03 05:05:06.987654321 +01:00 [Europe/Berlin]
  Moment('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321) = Sat 2001-02-03 05:05:06.987654321 +01:00 [Europe/Berlin]
