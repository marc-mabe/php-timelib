<?php declare(strict_types=1);

namespace time;

final class WallClock implements Clock
{
    /** @var \Closure(): array{int, int<0,999999999>} */
    private static \Closure $globalTimer;
    private static Duration $globalTimerResolution;

    public readonly Duration $resolution;

    /** @var \Closure(): array{int, int<0,999999999>} */
    private readonly \Closure $timer;

    public function __construct(
        public readonly Duration $modifier = new Duration(),
    ) {
        if (!isset(self::$globalTimer, self::$globalTimerResolution)) {
            // \microtime() function is only available on operating systems
            // that support the gettimeofday() system call.
            if (\function_exists('microtime')) {
                /** @var \Closure(): array{int, int<0,999999999>} $timer */
                $timer = static function () {
                    [$us, $s] = \explode(' ', \microtime(), 2);
                    return [(int)$s, (int)\substr($us, 2, -2) * 1_000];
                };
                self::$globalTimer           = $timer;
                self::$globalTimerResolution = new Duration(microseconds: 1);
            } else {
                self::$globalTimer = static fn () => [\time(), 0];
                self::$globalTimerResolution = new Duration(seconds: 1);
            }
        }

        $this->resolution = self::$globalTimerResolution;
        $this->timer      = $modifier->isZero
            ? self::$globalTimer
            : static fn () => $modifier->addToUnixTimestampTuple((self::$globalTimer)());
    }

    public function takeInstant(): Instant
    {
        return Instant::fromUnixTimestampTuple(($this->timer)());
    }

    public function takeZonedDateTime(Zone $zone = new ZoneOffset(0), ?Calendar $calendar = null): ZonedDateTime
    {
        return $this->takeInstant()->toZonedDateTime($zone, $calendar);
    }

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->takeInstant()->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0,999999999>} */
    public function takeUnixTimestampTuple(): array
    {
        return ($this->timer)();
    }
}
