<?php

namespace time;

interface Clock {
    public function getResolution(): Duration;

    public function takeMoment(): Moment;

    public function takeZonedDateTime(Zone $zone): ZonedDateTime;

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float;

    /** @return array{int, int<0, 999999999>} */
    public function takeUnixTimestampTuple(): array;
}
