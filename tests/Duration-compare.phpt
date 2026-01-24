--TEST--
Duration->compare()
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Duration();

$duration1 = new time\Duration(seconds: 987654321, nanoseconds: 123456789);
$duration2 = new time\Duration(seconds: 123456789, nanoseconds: 987654321);
$duration3 = new time\Duration(seconds: 123456789, nanoseconds: 987654322);

echo stringify($zero) . ' compare ' . stringify($zero) . ' = ' . stringify($zero->compare($zero)) . "\n";
echo stringify($duration1) . ' compare ' . stringify($duration1) . ' = ' . stringify($duration1->compare(clone $duration1)) . "\n";
echo stringify($zero) . ' compare ' . stringify($duration1) . ' = ' . stringify($zero->compare($duration1)) . "\n";
echo stringify($duration1) . ' compare ' . stringify($duration2) . ' = ' . stringify($duration1->compare($duration2)) . "\n";
echo stringify($duration2) . ' compare ' . stringify($duration1) . ' = ' . stringify($duration2->compare($duration1)) . "\n";
echo stringify($duration2) . ' compare ' . stringify($duration3) . ' = ' . stringify($duration2->compare($duration3)) . "\n";
echo stringify($duration3) . ' compare ' . stringify($duration2) . ' = ' . stringify($duration3->compare($duration2)) . "\n";
echo stringify($duration1->inverted()) . ' compare ' . stringify($duration2) . ' = ' . stringify($duration1->inverted()->compare($duration2)) . "\n";
echo stringify($duration2->inverted()) . ' compare ' . stringify($duration1) . ' = ' . stringify($duration2->inverted()->compare($duration1)) . "\n";

--EXPECT--
Duration('PT0S') compare Duration('PT0S') = 0
Duration('PT274348H25M21.123456789S') compare Duration('PT274348H25M21.123456789S') = 0
Duration('PT0S') compare Duration('PT274348H25M21.123456789S') = -1
Duration('PT274348H25M21.123456789S') compare Duration('PT34293H33M9.987654321S') = 1
Duration('PT34293H33M9.987654321S') compare Duration('PT274348H25M21.123456789S') = -1
Duration('PT34293H33M9.987654321S') compare Duration('PT34293H33M9.987654322S') = -1
Duration('PT34293H33M9.987654322S') compare Duration('PT34293H33M9.987654321S') = 1
Duration('-PT274348H25M21.123456789S') compare Duration('PT34293H33M9.987654321S') = -1
Duration('-PT34293H33M9.987654321S') compare Duration('PT274348H25M21.123456789S') = -1
