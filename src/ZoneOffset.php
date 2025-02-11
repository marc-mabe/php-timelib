<?php

namespace time;

final class ZoneOffset {
    private static self $default;

    public string $identifier { get => $this->legacy->getName(); }
    public bool $hasDst { get => false; } // TODO
    public string $name  { get => $this->legacy->getName(); } // TODO
    public string $abbr  { get => $this->legacy->getName(); } // TODO

    /** The time-offset if this is a fixed time-zone like UTC, GMT, CET etc. */
    public ?Duration $offset { get => null; } // TODO

    private function __construct(
        private readonly \DateTimeZone $legacy,
    ) {}

    public function toLegacyTz(): \DateTimeZone {
        return $this->legacy;
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromIdentifier(string $identifier): self {
        return new self(new \DateTimeZone($identifier));
    }

    public static function fromSystem(): self {
        return self::fromIdentifier(date_default_timezone_get());
    }

    public static function fromDuration(Duration $duration): self {
        if ($duration->hasDate()) {
            throw new \ValueError("A zone offset duration can contain time units only");
        }

        if ($duration->milliseconds || $duration->microseconds || $duration->nanoseconds) {
            throw new \ValueError("A zone offset duration can not contain fractions of a second");
        }

        $standardized = $duration->standardizedTo(TimeUnit::Minute);
        $identifier   = $standardized->isNegative ? '-' : '+'
            . str_pad((string)$standardized->hours, '0', STR_PAD_LEFT)
            . ':' . str_pad((string)$standardized->minutes, '0', STR_PAD_LEFT)
            . ($standardized->seconds ? ':' . str_pad((string)$standardized->minutes, '0', STR_PAD_LEFT) : '');
        return new self(new \DateTimeZone($identifier));
    }

    public static function fromUnit(TimeUnit $unit, int $value): self {
        return self::fromDuration(Duration::fromUnit($unit, $value));
    }

    public static function fromLegacyTz(\DateTimeZone $legacy): self {
        return new self($legacy);
    }

    /**
     * @param bool $withAliases
     * @param bool $withDead
     * @return list<self>
     */
    public static function list(bool $withAliases = true, bool $withDead = false): array {
        return array_map(
            static fn (string $id) => self::fromIdentifier($id),
            \DateTimeZone::listIdentifiers(),
        );
    }
}
