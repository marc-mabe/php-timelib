<?php declare(strict_types=1);

namespace time;

final class PlainTime implements Time
{
    public const int SECONDS_PER_MINUTE = 60;
    public const int SECONDS_PER_HOUR = 3600;
    public const int NANOS_PER_SECOND = 1_000_000_000;
    public const int MICROS_PER_SECOND = 1_000_000;
    public const int MILLIS_PER_SECOND = 1_000;

    public int $hour {
        get => \intdiv($this->secondsSinceMidnight, self::SECONDS_PER_HOUR) % 24;
    }

    public int $minute  {
        get => \intdiv($this->secondsSinceMidnight, self::SECONDS_PER_MINUTE) % self::SECONDS_PER_MINUTE;
    }

    public int $second  {
        get => $this->secondsSinceMidnight % self::SECONDS_PER_MINUTE;
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
        $secondsSinceMidnight = $hour * self::SECONDS_PER_HOUR + $minute * self::SECONDS_PER_MINUTE + $second;
        return new self($secondsSinceMidnight, $nanoOfSecond);
    }
}
