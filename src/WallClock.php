<?php declare(strict_types=1);

namespace time;

final class WallClock implements Clock
{
    public readonly Duration $resolution;

    public function __construct(
        public readonly Duration $modifier = new Duration(),
    ) {
        $this->resolution = new Duration(microseconds: 1);
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
        return $this->modifier->isZero
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
