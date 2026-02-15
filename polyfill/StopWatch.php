<?php declare(strict_types=1);

namespace time;

require_once __DIR__ . '/include.php';

class StopWatch
{
    /** @var null|array{int, int<0,999999999>}  */
    private ?array $startedAt = null;

    /**
     * Elapsed time of previous runs
     *
     * @var array{int, int<0,999999999>}
     */
    private array $elapsedPrev = [0, 0];

    public bool $isRunning {
        get => $this->startedAt !== null;
    }

    public function __construct(
        public readonly Clock $clock = new MonotonicClock(initClock: null)
    ) {}

    public function start(): void
    {
        if ($this->startedAt) {
            throw new LogicError('StopWatch is already running');
        }

        $this->startedAt = $this->clock->takeUnixTimestampTuple();
    }

    public function stop(): void
    {
        // take current time asap to prevent additional overhead
        $now = $this->clock->takeUnixTimestampTuple();

        if ($this->startedAt) {
            $s  = $this->elapsedPrev[0] + ($now[0] - $this->startedAt[0]);
            $ns = $this->elapsedPrev[1] + ($now[1] - $this->startedAt[1]);

            $s += \intdiv($ns, 1_000_000_000);
            $ns = $ns % 1_000_000_000;

            if ($ns < 0) {
                $s -= 1;
                $ns = 1_000_000_000 - $ns;
            }

            \assert($ns <= 1_000_000_000);

            $this->elapsedPrev = [$s, $ns];
            $this->startedAt   = null;
        } else {
            throw new LogicError('StopWatch is not running');
        }
    }

    public function reset(): void
    {
        $this->elapsedPrev = [0, 0];
        $this->startedAt   = null;
    }

    public function getElapsedDuration(): Duration
    {
        if ($this->startedAt) {
            $now = $this->clock->takeUnixTimestampTuple();
            $s   = $this->elapsedPrev[0] + ($now[0] - $this->startedAt[0]);
            $ns  = $this->elapsedPrev[1] + ($now[1] - $this->startedAt[1]);
        } else {
            $s  = $this->elapsedPrev[0];
            $ns = $this->elapsedPrev[1];
        }

        return new Duration(seconds: $s, nanoseconds: $ns);
    }

    public function getElapsedTime(TimeUnit $unit = TimeUnit::Nanosecond, bool $fractions = true): int|float
    {
        if ($this->startedAt) {
            $now = $this->clock->takeUnixTimestampTuple();
            $s   = $this->elapsedPrev[0] + ($now[0] - $this->startedAt[0]);
            $ns  = $this->elapsedPrev[1] + ($now[1] - $this->startedAt[1]);

            $s += \intdiv($ns, 1_000_000_000);
            $ns = $ns % 1_000_000_000;

            if ($ns < 0) {
                $s -= 1;
                $ns = 1_000_000_000 - $ns;
            }

            \assert($ns <= 1_000_000_000);
        } else {
            $s  = $this->elapsedPrev[0];
            $ns = $this->elapsedPrev[1];
        }

        if ($fractions) {
            return match ($unit) {
                TimeUnit::Hour        => $s / 3600 + $ns / 1_000_000_000,
                TimeUnit::Minute      => $s / 60 + $ns / 1_000_000_000,
                TimeUnit::Second      => $s + $ns / 1_000_000_000,
                TimeUnit::Millisecond => $s * 1_000 + $ns / 1_000_000,
                TimeUnit::Microsecond => $s * 1_000_000 + $ns / 1_000,
                TimeUnit::Nanosecond  => $s * 1_000_000_000 + $ns,
            };
        }

        return match ($unit) {
            TimeUnit::Hour        => \intdiv($s, 3600),
            TimeUnit::Minute      => \intdiv($s, 60),
            TimeUnit::Second      => $s,
            TimeUnit::Millisecond => $s * 1_000 + intdiv($ns, 1_000_000),
            TimeUnit::Microsecond => $s * 1_000_000 + intdiv($ns, 1_000),
            TimeUnit::Nanosecond  => $s * 1_000_000_000 + $ns,
        };
    }
}
