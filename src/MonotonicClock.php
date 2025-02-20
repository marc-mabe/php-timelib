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
            $wallTs   = new WallClock()->takeUnixTimestampTuple();
            $modifier = new Duration(
                seconds:     $wallTs[0] - $hr[0],
                nanoseconds: $wallTs[1] - $hr[1],
            );
        }

        $this->modifier   = $modifier;
        $this->resolution = new Duration(nanoseconds: 1);
    }

    public function takeMoment(): Moment
    {
        /** @var array{int, int<0, 999999999>} $tuple */
        $tuple = \hrtime();
        return Moment::fromUnixTimestampTuple($tuple)->add($this->modifier);
    }

    public function takeZonedDateTime(Zone $zone): ZonedDateTime
    {
        /** @var array{int, int<0, 999999999>} $tuple */
        $tuple = \hrtime();
        return Moment::fromUnixTimestampTuple($tuple)->add($this->modifier)->toZonedDateTime($zone);
    }

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->takeMoment()->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0, 999999999>} */
    public function takeUnixTimestampTuple(): array
    {
        // take current time asap to prevent additional overhead
        /** @var array{int, int<0, 999999999>} $tuple */
        $tuple = \hrtime();
        return $this->modifier->isZero
            ? $tuple
            : Moment::fromUnixTimestampTuple($tuple)->add($this->modifier)->toUnixTimestampTuple();;
    }
}
