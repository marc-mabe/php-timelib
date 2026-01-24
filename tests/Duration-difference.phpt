--TEST--
Duration->difference()
--FILE--
<?php

include __DIR__ . '/include.php';

$zero = new time\Duration();

$duration1 = new time\Duration(seconds: 987654321, nanoseconds: 123456789);
$duration2 = new time\Duration(seconds: 123456789, nanoseconds: 987654321);

echo stringify($zero) . ' diff ' . stringify($duration1) . ' = ' . stringify($zero->difference($duration1)) . "\n";
echo stringify($duration1) . ' diff ' . stringify($duration2) . ' = ' . stringify($duration1->difference($duration2)) . "\n";
echo stringify($duration2) . ' diff ' . stringify($duration1) . ' = ' . stringify($duration2->difference($duration1)) . "\n";
echo stringify($duration1->inverted()) . ' diff ' . stringify($duration2) . ' = ' . stringify($duration1->inverted()->difference($duration2)) . "\n";
echo stringify($duration2->inverted()) . ' diff ' . stringify($duration1) . ' = ' . stringify($duration2->inverted()->difference($duration1)) . "\n";

--EXPECT--
Duration('PT0S') diff Duration('PT274348H25M21.123456789S') = Duration('PT274348H25M21.123456789S')
Duration('PT274348H25M21.123456789S') diff Duration('PT34293H33M9.987654321S') = Duration('PT240054H52M12.864197532S')
Duration('PT34293H33M9.987654321S') diff Duration('PT274348H25M21.123456789S') = Duration('PT240054H52M12.864197532S')
Duration('-PT274348H25M21.123456789S') diff Duration('PT34293H33M9.987654321S') = Duration('PT308641H58M31.11111111S')
Duration('-PT34293H33M9.987654321S') diff Duration('PT274348H25M21.123456789S') = Duration('PT308641H58M31.11111111S')
