--TEST--
Duration->subtractBy()
--FILE--
<?php

include __DIR__ . '/include.php';

$durations = [
    new time\Duration(),
    new time\Duration(seconds: 1, nanoseconds: 987654321),
    new time\Duration(seconds: -1, nanoseconds: -987654321),
    new time\Duration(seconds: 123, nanoseconds: 987654321),
    new time\Duration(seconds: -123, nanoseconds: -987654321),
];

foreach ($durations as $baseDuration) {
    foreach ($durations as $otherDuration) {
        echo stringify($baseDuration)
            . ' - ' . stringify($otherDuration)
            . ' = ' . stringify($baseDuration->subtractBy($otherDuration))
            . "\n";
    }
}

--EXPECT--
Duration('PT0S') - Duration('PT0S') = Duration('PT0S')
Duration('PT0S') - Duration('PT1.987654321S') = Duration('-PT1.987654321S')
Duration('PT0S') - Duration('-PT1.987654321S') = Duration('PT1.987654321S')
Duration('PT0S') - Duration('PT2M3.987654321S') = Duration('-PT2M3.987654321S')
Duration('PT0S') - Duration('-PT2M3.987654321S') = Duration('PT2M3.987654321S')
Duration('PT1.987654321S') - Duration('PT0S') = Duration('PT1.987654321S')
Duration('PT1.987654321S') - Duration('PT1.987654321S') = Duration('PT0S')
Duration('PT1.987654321S') - Duration('-PT1.987654321S') = Duration('PT3.975308642S')
Duration('PT1.987654321S') - Duration('PT2M3.987654321S') = Duration('-PT2M2S')
Duration('PT1.987654321S') - Duration('-PT2M3.987654321S') = Duration('PT2M5.975308642S')
Duration('-PT1.987654321S') - Duration('PT0S') = Duration('-PT1.987654321S')
Duration('-PT1.987654321S') - Duration('PT1.987654321S') = Duration('-PT3.975308642S')
Duration('-PT1.987654321S') - Duration('-PT1.987654321S') = Duration('PT0S')
Duration('-PT1.987654321S') - Duration('PT2M3.987654321S') = Duration('-PT2M5.975308642S')
Duration('-PT1.987654321S') - Duration('-PT2M3.987654321S') = Duration('PT2M2S')
Duration('PT2M3.987654321S') - Duration('PT0S') = Duration('PT2M3.987654321S')
Duration('PT2M3.987654321S') - Duration('PT1.987654321S') = Duration('PT2M2S')
Duration('PT2M3.987654321S') - Duration('-PT1.987654321S') = Duration('PT2M5.975308642S')
Duration('PT2M3.987654321S') - Duration('PT2M3.987654321S') = Duration('PT0S')
Duration('PT2M3.987654321S') - Duration('-PT2M3.987654321S') = Duration('PT4M7.975308642S')
Duration('-PT2M3.987654321S') - Duration('PT0S') = Duration('-PT2M3.987654321S')
Duration('-PT2M3.987654321S') - Duration('PT1.987654321S') = Duration('-PT2M5.975308642S')
Duration('-PT2M3.987654321S') - Duration('-PT1.987654321S') = Duration('-PT2M2S')
Duration('-PT2M3.987654321S') - Duration('PT2M3.987654321S') = Duration('-PT4M7.975308642S')
Duration('-PT2M3.987654321S') - Duration('-PT2M3.987654321S') = Duration('PT0S')
