--TEST--
Period->addBy()
--FILE--
<?php

include __DIR__ . '/include.php';

$periods = [
    new time\Period(),
    new time\Period(years: 1, months: 1, weeks: 1, days: 1),
    new time\Period(isNegative: true, years: 1, months: 1, weeks: 1, days: 1),
    new time\Period(years: -1, months: -1, weeks: -1, days: -1),
    new time\Period(isNegative: true, years: -1, months: -1, weeks: -1, days: -1),
    new time\Period(years: 123456789, months: -987654321, weeks: -10000, days: 20000),
    new time\Period(isNegative: true, years: 123456789, months: -987654321, weeks: -10000, days: 20000),
];

foreach ($periods as $basePeriod) {
    foreach ($periods as $otherPeriod) {
        echo stringify($basePeriod)
            . ' + ' . stringify($otherPeriod)
            . ' = ' . stringify($basePeriod->addBy($otherPeriod))
            . "\n";
    }
}

--EXPECT--
Period('P0D') + Period('P0D') = Period('P0D')
Period('P0D') + Period('P1Y1M1W1D') = Period('P1Y1M1W1D')
Period('P0D') + Period('-P1Y1M1W1D') = Period('-P1Y1M1W1D')
Period('P0D') + Period('P-1Y-1M-1W-1D') = Period('P-1Y-1M-1W-1D')
Period('P0D') + Period('-P-1Y-1M-1W-1D') = Period('-P-1Y-1M-1W-1D')
Period('P0D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P123456789Y-987654321M-10000W20000D')
Period('P0D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('-P123456789Y-987654321M-10000W20000D')
Period('P1Y1M1W1D') + Period('P0D') = Period('P1Y1M1W1D')
Period('P1Y1M1W1D') + Period('P1Y1M1W1D') = Period('P2Y2M2W2D')
Period('P1Y1M1W1D') + Period('-P1Y1M1W1D') = Period('P0D')
Period('P1Y1M1W1D') + Period('P-1Y-1M-1W-1D') = Period('P0D')
Period('P1Y1M1W1D') + Period('-P-1Y-1M-1W-1D') = Period('P2Y2M2W2D')
Period('P1Y1M1W1D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P123456790Y-987654320M-9999W20001D')
Period('P1Y1M1W1D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('P-123456788Y987654322M10001W-19999D')
Period('-P1Y1M1W1D') + Period('P0D') = Period('-P1Y1M1W1D')
Period('-P1Y1M1W1D') + Period('P1Y1M1W1D') = Period('P0D')
Period('-P1Y1M1W1D') + Period('-P1Y1M1W1D') = Period('P2Y2M2W2D')
Period('-P1Y1M1W1D') + Period('P-1Y-1M-1W-1D') = Period('P2Y2M2W2D')
Period('-P1Y1M1W1D') + Period('-P-1Y-1M-1W-1D') = Period('P0D')
Period('-P1Y1M1W1D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P-123456788Y987654322M10001W-19999D')
Period('-P1Y1M1W1D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('P123456790Y-987654320M-9999W20001D')
Period('P-1Y-1M-1W-1D') + Period('P0D') = Period('P-1Y-1M-1W-1D')
Period('P-1Y-1M-1W-1D') + Period('P1Y1M1W1D') = Period('P0D')
Period('P-1Y-1M-1W-1D') + Period('-P1Y1M1W1D') = Period('P-2Y-2M-2W-2D')
Period('P-1Y-1M-1W-1D') + Period('P-1Y-1M-1W-1D') = Period('P-2Y-2M-2W-2D')
Period('P-1Y-1M-1W-1D') + Period('-P-1Y-1M-1W-1D') = Period('P0D')
Period('P-1Y-1M-1W-1D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P123456788Y-987654322M-10001W19999D')
Period('P-1Y-1M-1W-1D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('P-123456790Y987654320M9999W-20001D')
Period('-P-1Y-1M-1W-1D') + Period('P0D') = Period('-P-1Y-1M-1W-1D')
Period('-P-1Y-1M-1W-1D') + Period('P1Y1M1W1D') = Period('P-2Y-2M-2W-2D')
Period('-P-1Y-1M-1W-1D') + Period('-P1Y1M1W1D') = Period('P0D')
Period('-P-1Y-1M-1W-1D') + Period('P-1Y-1M-1W-1D') = Period('P0D')
Period('-P-1Y-1M-1W-1D') + Period('-P-1Y-1M-1W-1D') = Period('P-2Y-2M-2W-2D')
Period('-P-1Y-1M-1W-1D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P-123456790Y987654320M9999W-20001D')
Period('-P-1Y-1M-1W-1D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('P123456788Y-987654322M-10001W19999D')
Period('P123456789Y-987654321M-10000W20000D') + Period('P0D') = Period('P123456789Y-987654321M-10000W20000D')
Period('P123456789Y-987654321M-10000W20000D') + Period('P1Y1M1W1D') = Period('P123456790Y-987654320M-9999W20001D')
Period('P123456789Y-987654321M-10000W20000D') + Period('-P1Y1M1W1D') = Period('P123456788Y-987654322M-10001W19999D')
Period('P123456789Y-987654321M-10000W20000D') + Period('P-1Y-1M-1W-1D') = Period('P123456788Y-987654322M-10001W19999D')
Period('P123456789Y-987654321M-10000W20000D') + Period('-P-1Y-1M-1W-1D') = Period('P123456790Y-987654320M-9999W20001D')
Period('P123456789Y-987654321M-10000W20000D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P246913578Y-1975308642M-20000W40000D')
Period('P123456789Y-987654321M-10000W20000D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('P0D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('P0D') = Period('-P123456789Y-987654321M-10000W20000D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('P1Y1M1W1D') = Period('P123456788Y-987654322M-10001W19999D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('-P1Y1M1W1D') = Period('P123456790Y-987654320M-9999W20001D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('P-1Y-1M-1W-1D') = Period('P123456790Y-987654320M-9999W20001D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('-P-1Y-1M-1W-1D') = Period('P123456788Y-987654322M-10001W19999D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('P123456789Y-987654321M-10000W20000D') = Period('P0D')
Period('-P123456789Y-987654321M-10000W20000D') + Period('-P123456789Y-987654321M-10000W20000D') = Period('P246913578Y-1975308642M-20000W40000D')
