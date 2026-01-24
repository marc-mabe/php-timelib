--TEST--
MonotonicClock::MAX_RESOLUTION
--FILE--
<?php

include __DIR__ . '/include.php';

echo stringify(time\MonotonicClock::MAX_RESOLUTION);

--EXPECT--
Duration('PT0.000000001S')
