--TEST--
DateTimeFormatter->format with Instanted
--FILE--
<?php

include __DIR__ . '/include.php';

$zoneBln = time\Zone::fromIdentifier('Europe/Berlin');
$zoneNy  = time\Zone::fromIdentifier('America/New_York');
$instant  = time\Instant::fromYmd(2001, 2, 3, 4, 5, 6, 987654321);

$zdt = $instant->toZonedDateTime($zoneNy);
$instantedWithZone = new class($instant, $zoneNy) implements time\Instanted, time\Zoned {
    public function __construct(
        public readonly time\Instant $instant,
        public readonly time\Zone $zone,
    ) {}

    public function add(time\Duration|time\Period $durationOrPeriod): self {}
    public function sub(time\Duration|time\Period $durationOrPeriod): self {}
};

echo "Formatter without zone:\n";
$formatter = new \time\DateTimeFormatter('D Y-m-d H:i:sf P [e]');
echo "  " . stringify($zdt) . ' = ' . $formatter->format($zdt) . "\n";
echo "  Instanted(".stringify($instantedWithZone->instant).")+Zoned(" . stringify($instantedWithZone->zone) . ') = ' . $formatter->format($instantedWithZone) . "\n";
echo "  " . stringify($instant) . ' = ' . $formatter->format($instant) . "\n";

echo "Formatter with zone:\n";
$formatter = new \time\DateTimeFormatter('D Y-m-d H:i:sf P [e]', zone: $zoneBln);
echo "  " . stringify($zdt) . ' = ' . $formatter->format($zdt) . "\n";
echo "  Instanted(".stringify($instantedWithZone->instant).")+Zoned(" . stringify($instantedWithZone->zone) . ') = ' . $formatter->format($instantedWithZone) . "\n";
echo "  " . stringify($instant) . ' = ' . $formatter->format($instant) . "\n";

--EXPECT--
Formatter without zone:
  ZonedDateTime('Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]') = Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]
  Instanted(Instant('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321))+Zoned(time\Zone('America/New_York')) = Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]
  Instant('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321) = Sat 2001-02-03 04:05:06.987654321 +00:00 [+00:00]
Formatter with zone:
  ZonedDateTime('Fri 2001-02-02 23:05:06.987654321 -05:00 [America/New_York]') = Sat 2001-02-03 05:05:06.987654321 +01:00 [Europe/Berlin]
  Instanted(Instant('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321))+Zoned(time\Zone('America/New_York')) = Sat 2001-02-03 05:05:06.987654321 +01:00 [Europe/Berlin]
  Instant('Sat 2001-02-03 04:05:06.987654321', 981173106, 987654321) = Sat 2001-02-03 05:05:06.987654321 +01:00 [Europe/Berlin]
