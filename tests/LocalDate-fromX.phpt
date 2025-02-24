--TEST--
LocalDate::fromXXX
--FILE--
<?php

include __DIR__ . '/include.php';

echo "LocalDate::fromYmd\n";

echo "  fromYmd(0, 1, 1)\n";
echo '    ' . stringify(\time\LocalDate::fromYmd(0, 1, 1)) . "\n";

echo "  fromYmd(1970, 1, 1)\n";
echo '    ' . stringify(\time\LocalDate::fromYmd(1970, 1, 1)) . "\n";

echo "  fromYmd(2025, 2, 3)\n";
echo '    ' . stringify(\time\LocalDate::fromYmd(2025, 2, 3)) . "\n";

echo "LocalDate::fromYd\n";

echo "  fromYd(2025, 34)\n";
echo '    ' . stringify(\time\LocalDate::fromYd(2025, 34)) . "\n";

echo "  fromYd(1970, 1)\n";
echo '    ' . stringify(\time\LocalDate::fromYd(1970, 1)) . "\n";

echo "  fromYd(0, 1)\n";
echo '    ' . stringify(\time\LocalDate::fromYd(0, 1)) . "\n";

echo "  fromYd(-1234, 34)\n";
echo '    ' . stringify(\time\LocalDate::fromYd(-1234, 34)) . "\n";

--EXPECT--
LocalDate::fromYmd
  fromYmd(0, 1, 1)
    LocalDate('Sat 0-01-01')
  fromYmd(1970, 1, 1)
    LocalDate('Thu 1970-01-01')
  fromYmd(2025, 2, 3)
    LocalDate('Mon 2025-02-03')
LocalDate::fromYd
  fromYd(2025, 34)
    LocalDate('Mon 2025-02-03')
  fromYd(1970, 1)
    LocalDate('Thu 1970-01-01')
  fromYd(0, 1)
    LocalDate('Sat 0-01-01')
  fromYd(-1234, 34)
    LocalDate('Thu -1234-02-03')
