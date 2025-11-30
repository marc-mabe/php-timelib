--TEST--
PlainDate::fromXXX
--FILE--
<?php

include __DIR__ . '/include.php';

echo "PlainDate::fromYmd\n";

echo "  fromYmd(0, 1, 1)\n";
echo '    ' . stringify(\time\PlainDate::fromYmd(0, 1, 1)) . "\n";

echo "  fromYmd(1970, 1, 1)\n";
echo '    ' . stringify(\time\PlainDate::fromYmd(1970, 1, 1)) . "\n";

echo "  fromYmd(2025, 2, 3)\n";
echo '    ' . stringify(\time\PlainDate::fromYmd(2025, 2, 3)) . "\n";

echo "PlainDate::fromYd\n";

echo "  fromYd(2025, 34)\n";
echo '    ' . stringify(\time\PlainDate::fromYd(2025, 34)) . "\n";

echo "  fromYd(1970, 1)\n";
echo '    ' . stringify(\time\PlainDate::fromYd(1970, 1)) . "\n";

echo "  fromYd(0, 1)\n";
echo '    ' . stringify(\time\PlainDate::fromYd(0, 1)) . "\n";

echo "  fromYd(-1234, 34)\n";
echo '    ' . stringify(\time\PlainDate::fromYd(-1234, 34)) . "\n";

--EXPECT--
PlainDate::fromYmd
  fromYmd(0, 1, 1)
    PlainDate('Sat 0-01-01')
  fromYmd(1970, 1, 1)
    PlainDate('Thu 1970-01-01')
  fromYmd(2025, 2, 3)
    PlainDate('Mon 2025-02-03')
PlainDate::fromYd
  fromYd(2025, 34)
    PlainDate('Mon 2025-02-03')
  fromYd(1970, 1)
    PlainDate('Thu 1970-01-01')
  fromYd(0, 1)
    PlainDate('Sat 0-01-01')
  fromYd(-1234, 34)
    PlainDate('Thu -1234-02-03')
