<?php declare(strict_types=1);

namespace time;

final class ZoneOffset extends Zone implements \Stringable
{
    const int TOTAL_SECONDS_MAX = 60 * 60 * 18;
    const int TOTAL_SECONDS_MIN = -60 * 60 * 18;

    public string $name {
        get => $this->identifier;
    }

    public ZoneOffset $fixedOffset { get => $this; }

    public function __construct(
        public readonly int $totalSeconds
    ) {
        if ($totalSeconds > self::TOTAL_SECONDS_MAX || $totalSeconds < self::TOTAL_SECONDS_MIN) {
            throw new \InvalidArgumentException(\sprintf(
                "Zone offset must be between %s and %s",
                new self(self::TOTAL_SECONDS_MAX)->identifier,
                new self(self::TOTAL_SECONDS_MIN)->identifier,
            ));
        }

        $abs        = new Duration(seconds: \abs($totalSeconds));
        $identifier = ($totalSeconds < 0 ? '-' : '+')
            . \str_pad((string)$abs->totalHours, 2, '0', STR_PAD_LEFT)
            . ':' . \str_pad((string)$abs->minutesOfHour, 2, '0', STR_PAD_LEFT)
            . ($abs->secondsOfMinute ? ':' . \str_pad((string)$abs->secondsOfMinute, 2, '0', STR_PAD_LEFT) : '');

        $info = new class ($this) extends ZoneInfo {
            public readonly ZoneOffset $fixedOffset;

            public function __construct(
                ?ZoneOffset $fixedOffset
            ) {
                \assert($fixedOffset !== null);
                $this->fixedOffset = $fixedOffset;
            }

            public function getTransitions(
                ?Moment $from = null,
                ?Moment $until = null,
                ?int $limit = null,
                bool $reverse = false,
            ): \Iterator {
                return new \ArrayIterator();
            }

            public function getOffsetAt(Moment $moment): ZoneOffset
            {
                return $this->fixedOffset;
            }
        };

        parent::__construct($identifier, $info);
    }

    public function toDuration(): Duration
    {
        return new Duration(seconds: $this->totalSeconds);
    }

    public static function fromDuration(Duration $duration): self
    {
        if ($duration->nanosOfSecond) {
            throw new \ValueError("A time offset can not contain fractions of a second");
        }

        return new self($duration->totalSeconds);
    }

    public static function fromString(string $string): self
    {
        $match = \preg_match(
            '/^(?:GMT|UTC)?(?<sign>[+-])(?<h>\d\d):(?<m>\d\d)(:?(?<s>\d\d))?$/',
            $string,
            $matches
        );
        \assert($match !== false);

        if (!$match) {
            throw new \InvalidArgumentException("Invalid time offset '{$string}'");
        }

        $duration = new Duration(
            hours: (int)$matches['h'],
            minutes: (int)$matches['m'],
            seconds: (int)($matches['s'] ?? 0),
        );

        if ($matches['sign'] === '-') {
            $duration = $duration->negated();
        }

        return self::fromDuration($duration);
    }
}
