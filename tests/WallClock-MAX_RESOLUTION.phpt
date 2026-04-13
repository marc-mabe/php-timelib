--TEST--
WallClock::MAX_RESOLUTION
--FILE--
<?php

include __DIR__ . '/include.inc';

echo stringify(time\WallClock::MAX_RESOLUTION);

--EXPECTF--
Duration('PT%fS')
