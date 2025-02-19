<?php declare(strict_types=1);

namespace time;

class ZoneOffset extends Zone implements \Stringable
{
    public string $name {
        get => $this->identifier;
    }

    public bool $isAbbreviation {
        get => false;
    }

    public ZoneOffset $offset { get => $this; }

    public function __construct(
        public readonly int $totalSeconds
    ) {
        $abs        = new Duration(seconds: $totalSeconds)->abs();
        $identifier = ($totalSeconds < 0 ? '-' : '+')
            . \str_pad((string)$abs->totalHours, 2, '0', STR_PAD_LEFT)
            . ':' . \str_pad((string)$abs->minutesOfHour, 2, '0', STR_PAD_LEFT)
            . ($abs->secondsOfMinute ? ':' . \str_pad((string)$abs->secondsOfMinute, 2, '0', STR_PAD_LEFT) : '');

        parent::__construct($identifier);
    }

    public function toDuration(): Duration
    {
        return new Duration(seconds: $this->totalSeconds);
    }

    public static function fromDuration(Duration $duration): self {
        if ($duration->nanosOfSecond) {
            throw new \ValueError("A time offset can not contain fractions of a second");
        }

        return new self($duration->totalSeconds);
    }
}
