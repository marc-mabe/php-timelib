--TEST--
ZoneOffset min/max range
--FILE--
<?php

include __DIR__ . '/include.php';

$min = new time\ZoneOffset(time\ZoneOffset::TOTAL_SECONDS_MIN);
echo 'Min ZoneOffset = ' . stringify($min) . "\n";

$max = new time\ZoneOffset(time\ZoneOffset::TOTAL_SECONDS_MAX);
echo 'Max ZoneOffset = ' . stringify($max) . "\n";

$rnd = new time\ZoneOffset(random_int(time\ZoneOffset::TOTAL_SECONDS_MIN, time\ZoneOffset::TOTAL_SECONDS_MAX));
echo 'Random ZoneOffset = ' . stringify($rnd) . "\n";

echo "> MAX = ";
try {
    new time\ZoneOffset(time\ZoneOffset::TOTAL_SECONDS_MAX + 1);
} catch (Throwable $e) {
    echo $e->getMessage() . "\n";
}

echo "< MIN = ";
try {
    new time\ZoneOffset(time\ZoneOffset::TOTAL_SECONDS_MIN - 1);
} catch (Throwable $e) {
    echo $e->getMessage() . "\n";
}

--EXPECTF--
Min ZoneOffset = time\ZoneOffset('-18:00')
Max ZoneOffset = time\ZoneOffset('+18:00')
Random ZoneOffset = time\ZoneOffset('%s')
> MAX = Zone offset must be between +18:00 and -18:00
< MIN = Zone offset must be between +18:00 and -18:00
