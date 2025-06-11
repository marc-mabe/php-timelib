<?php declare(strict_types=1);

namespace time;

class StopWatch
{
    /** @var null|array{int, int<0,999999999>}  */
    private ?array $startedAt = null;

    /** Elapsed nano seconds of previous runs */
    private int $elapsedNanosPrev = 0;

    public bool $isRunning {
        get => $this->startedAt !== null;
    }

    public function __construct(
        public readonly Clock $clock = new MonotonicClock(initClock: null)
    ) {}

    public function start(): void
    {
        if ($this->isRunning) {
            throw new LogicError('StopWatch is already running');
        }

        $this->startedAt = $this->clock->takeUnixTimestampTuple();
    }

    public function stop(): void
    {
        // take current time asap to prevent additional overhead
        $runningNanos = $this->getRunningNanos();

        if (!$this->isRunning) {
            throw new LogicError('StopWatch is not running');
        }

        $this->elapsedNanosPrev += $runningNanos;
        $this->startedAt = null;
    }

    public function reset(): void
    {
        $this->elapsedNanosPrev = 0;
        $this->startedAt = null;
    }

    public function getElapsedDuration(): Duration
    {
        return new Duration(nanoseconds: $this->getElapsedNanos());
    }

    public function getElapsedTime(TimeUnit $unit = TimeUnit::Nanosecond, bool $fractions = true): int|float
    {
        $elapsedNanos = $this->getElapsedNanos();
        if ($fractions) {
            return match ($unit) {
                TimeUnit::Hour => $elapsedNanos / (1_000_000_000 * 3600),
                TimeUnit::Minute => $elapsedNanos / (1_000_000_000 * 60),
                TimeUnit::Second => $elapsedNanos / 1_000_000_000,
                TimeUnit::Millisecond => $elapsedNanos / 1_000_000,
                TimeUnit::Microsecond => $elapsedNanos / 1_000,
                TimeUnit::Nanosecond => $elapsedNanos,
            };
        }

        return match ($unit) {
            TimeUnit::Hour => \intdiv($elapsedNanos, 1_000_000_000 * 3600),
            TimeUnit::Minute => \intdiv($elapsedNanos, 1_000_000_000 * 60),
            TimeUnit::Second => \intdiv($elapsedNanos, 1_000_000_000),
            TimeUnit::Millisecond => \intdiv($elapsedNanos, 1_000_000),
            TimeUnit::Microsecond => \intdiv($elapsedNanos, 1_000),
            TimeUnit::Nanosecond => $elapsedNanos,
        };
    }

    private function getRunningNanos(): int
    {
        // take current time asap to prevent additional overhead
        $now = $this->clock->takeUnixTimestampTuple();

        if ($this->startedAt === null) {
            return 0;
        }

        $diffS  = $now[0] - $this->startedAt[0];
        $diffNs = $now[1] - $this->startedAt[1];

        return ($diffS * 1_000_000_000) + $diffNs;
    }

    private function getElapsedNanos(): int
    {
        return $this->elapsedNanosPrev + $this->getRunningNanos();
    }
}
