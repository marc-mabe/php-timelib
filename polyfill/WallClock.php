<?php declare(strict_types=1);

namespace time;

if (\function_exists('microtime')) {
    /** @internal Max. resolution of WallClock */
    \define('__WALL_CLOCK_MAX_RESOLUTION', new Duration(microseconds: 1));
} else {
    /** @internal Max. resolution of WallClock */
    \define('__WALL_CLOCK_MAX_RESOLUTION', new Duration(seconds: 1));
}

final class WallClock implements Clock
{
    public const Duration MAX_RESOLUTION = __WALL_CLOCK_MAX_RESOLUTION;

    /** @var \Closure(): array{int, int<0,999999999>} */
    private static \Closure $globalMicroTimer;

    /** @var \Closure(): array{int, 0} */
    private static \Closure $globalSecondTimer;

    /** @var \Closure(): array{int, int<0,999999999>} */
    private readonly \Closure $timer;

    public function __construct(
        public readonly Duration $modifier = new Duration(),
        public readonly Duration $resolution = self::MAX_RESOLUTION,
    ) {
        if (!$resolution->isEqual(self::MAX_RESOLUTION)) {
            if ($resolution->isNegative) {
                throw new LogicError('Resolution must not be negative');
            }

            if ($resolution->isZero) {
                throw new LogicError('Resolution must not be zero');
            }

            if (!$resolution->moduloBy(self::MAX_RESOLUTION)->isZero) {
                throw new LogicError('Resolution must be a multiple of ' . self::class .  '::MAX_RESOLUTION');
            }
        }

        if ($resolution->nanosOfSecond) {
            if (!isset(self::$globalMicroTimer)) {
                /** @var \Closure(): array{int, int<0,999999999>} $timer */
                $timer = static function () {
                    [$us, $s] = \explode(' ', \microtime(), 2);
                    return [(int)$s, (int)\substr($us, 2, -2) * 1_000];
                };
                self::$globalMicroTimer = $timer;
            } else {
                $timer = self::$globalMicroTimer;
            }

            if ($resolution->totalSeconds !== 0 || $resolution->nanosOfSecond !== 100) {
                /** @var \Closure(): array{int, int<0,999999999>} $timer */
                $timer = static function () use ($timer, $resolution): array {
                    [$s, $ns] = ($timer)();

                    $ns -= $ns % $resolution->nanosOfSecond;

                    if ($resolution->totalSeconds) {
                        $s -= $s % $resolution->totalSeconds;
                    }

                    return [$s, $ns];
                };
            }
        } else {
            self::$globalSecondTimer ??= static fn () => [\time(), 0];
            $timer = self::$globalSecondTimer;

            if ($resolution->totalSeconds !== 1) {
                /** @var \Closure(): array{int, int<0,999999999>} $timer */
                $timer = static function () use ($timer, $resolution): array {
                    [$s, $ns] = ($timer)();

                    $s -= $s % $resolution->totalSeconds;

                    return [$s, $ns];
                };
            }
        } 

        $this->timer = $modifier->isZero
            ? $timer
            : static fn () => $modifier->addToUnixTimestampTuple(($timer)());
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
