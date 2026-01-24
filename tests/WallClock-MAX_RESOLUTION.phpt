--TEST--
WallClock::MAX_RESOLUTION
--FILE--
<?php

include __DIR__ . '/include.php';

echo stringify(time\WallClock::MAX_RESOLUTION);

--EXPECTF--
Duration('PT%fS')
