<?php declare(strict_types=1);

namespace time;

final class MonotonicClock implements Clock
{
    public readonly Duration $resolution;

    /** @var \Closure(): array{int, int<0,999999999>} */
    private readonly \Closure $timer;

    public function __construct(
        public Duration $modifier = new Duration(),
        ?Clock $initClock = new WallClock(),
    ) {
        $hr = \hrtime();
        /** @phpstan-ignore identical.alwaysFalse */
        if (false === $hr) {
            throw new \RuntimeException('No monotonic timer available');
        }

        $this->resolution = new Duration(nanoseconds: 1);

        $timeModifier = $modifier;
        if ($initClock) {
            $initTs       = $initClock->takeUnixTimestampTuple();
            $timeModifier = $timeModifier->add(new Duration(
                seconds:     $initTs[0] - $hr[0],
                nanoseconds: $initTs[1] - $hr[1],
            ));
        }

        // Setup timer including resulting modifier
        if ($timeModifier->isZero) {
            /** @phpstan-ignore assign.propertyType */
            $this->timer = \hrtime(...);
        } else {
            $this->timer = static function () use ($timeModifier) {
                /** @phpstan-ignore argument.type */
                return $timeModifier->addToUnixTimestampTuple(\hrtime());
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
