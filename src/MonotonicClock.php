<?php declare(strict_types=1);

namespace time;

final class MonotonicClock implements Clock
{
    public readonly Duration $resolution;

    public readonly Duration $modifier;

    /** @var \Closure(): array{int, int<0,999999999>} */
    private readonly \Closure $timer;

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

        // Speedup timer in case an empty modifier is used
        // like for time measurement StopWatch
        if ($modifier->isZero) {
            /** @phpstan-ignore assign.propertyType */
            $this->timer = \hrtime(...);
        } else {
            $this->timer = static function () use ($modifier) {
                /** @phpstan-ignore argument.type */
                return $modifier->addToUnixTimestampTuple(\hrtime());
            };
        }

    }

    public function takeMoment(): Moment
    {
        return Moment::fromUnixTimestampTuple(($this->timer)());
    }

    public function takeZonedDateTime(Zone $zone): ZonedDateTime
    {
        return $this->takeMoment()->toZonedDateTime($zone);
    }

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->takeMoment()->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0, 999999999>} */
    public function takeUnixTimestampTuple(): array
    {
        return ($this->timer)();
    }
}
