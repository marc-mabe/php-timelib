<?php declare(strict_types=1);

namespace time;

final class MonotonicClock implements Clock
{
    public readonly Duration $resolution;

    public readonly Duration $modifier;

    public function __construct(?Duration $modifier = null) {
        $hr = \hrtime();
        /** @phpstan-ignore identical.alwaysFalse */
        if (false === $hr) {
            throw new \RuntimeException('No monotonic timer available');
        }

        if ($modifier === null) {
            [$us, $s] = \explode(' ', \microtime(), 2);
            $modifier = new Duration(
                seconds:     (int)$s - $hr[0],
                nanoseconds: (int)\substr($us, 2, -2) * 1_000 - $hr[1],
            );
        }
        $this->modifier = $modifier;

        $this->resolution = new Duration(nanoseconds: 1);
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
        /** @phpstan-ignore return.type */
        return \hrtime();
    }
}
