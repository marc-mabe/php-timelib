<?php

namespace time;

final class WallClock implements Clock
{
    public readonly Period $resolution;

    public function __construct(
        public readonly Period $modifier = new Period(),
    ) {
        $this->resolution = new Period(microseconds: 1);
    }

    public function takeMoment(): Moment
    {
        $tuple = $this->takeUnixTimestampTupleWithoutModifier();
        return Moment::fromUnixTimestampTuple($tuple)->add($this->modifier);
    }

    public function takeZonedDateTime(Zone $zone): ZonedDateTime
    {
        $tuple = $this->takeUnixTimestampTupleWithoutModifier();
        return Moment::fromUnixTimestampTuple($tuple)->add($this->modifier)->toZonedDateTime($zone);
    }

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->takeMoment()->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0, 999999999>} */
    public function takeUnixTimestampTuple(): array
    {
        return $this->modifier->isEmpty()
            ? $this->takeUnixTimestampTupleWithoutModifier()
            : $this->takeMoment()->toUnixTimestampTuple();
    }

    /** @return array{int, int<0, 999999999>} */
    private function takeUnixTimestampTupleWithoutModifier(): array
    {
        [$us, $s] = \explode(' ', \microtime());
        return [(int)$s, (int)\substr($us, 2, -2) * 1_000];
    }
}
