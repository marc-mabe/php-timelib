<?php declare(strict_types=1);

namespace time;

final class WallClock implements Clock
{
    /** @var \Closure(): array{int, int<0, 999999999>} */
    private static \Closure $timer;
    private static Duration $timerResolution;

    public readonly Duration $resolution;

    public function __construct(
        public readonly Duration $modifier = new Duration(),
    ) {
        if (!isset(self::$timer, self::$timerResolution)) {
            // \microtime() function is only available on operating systems
            // that support the gettimeofday() system call.
            if (\function_exists('microtime')) {
                /** @phpstan-ignore assign.propertyType */
                self::$timer = static function () {
                    [$us, $s] = \explode(' ', \microtime(), 2);
                    return [(int)$s, (int)\substr($us, 2, -2) * 1_000];
                };
                self::$timerResolution = new Duration(microseconds: 1);
            } else {
                self::$timer = static function () {
                    return [\time(), 0];
                };
                self::$timerResolution = new Duration(seconds: 1);
            }
        }

        $this->resolution = self::$timerResolution;
    }

    public function takeMoment(): Moment
    {
        $tuple = (self::$timer)();
        return Moment::fromUnixTimestampTuple($tuple)->add($this->modifier);
    }

    public function takeZonedDateTime(Zone $zone): ZonedDateTime
    {
        $tuple = (self::$timer)();
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
            ? (self::$timer)()
            : $this->takeMoment()->toUnixTimestampTuple();
    }
}
