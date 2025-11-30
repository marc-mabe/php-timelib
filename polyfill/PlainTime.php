<?php declare(strict_types=1);

namespace time;

final class PlainTime implements Time {
    public int $hour {
        get => \intdiv($this->secondsSinceMidnight, 3600) % 24;
    }

    public int $minute  {
        get => \intdiv($this->secondsSinceMidnight, 60) % 60;
    }

    public int $second  {
        get => $this->secondsSinceMidnight % 60;
    }

    public int $milliOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000_000);
    }

    public int $microOfSecond {
        get => \intdiv($this->nanoOfSecond, 1_000);
    }

    /**
     * @param int<0,86399> $secondsSinceMidnight
     * @param int<0,999999999> $nanoOfSecond
     */
    private function __construct(
        private readonly int $secondsSinceMidnight,
        public readonly int $nanoOfSecond,
    ) {}

    /**
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999999> $nanoOfSecond
     */
    public static function fromHms(
        int $hour,
        int $minute,
        int $second,
        int $nanoOfSecond = 0
    ): self {
        $secondsSinceMidnight = $hour * 3600 + $minute * 60 + $second;
        return new self($secondsSinceMidnight, $nanoOfSecond);
    }
}
