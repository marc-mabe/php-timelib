<?php

namespace dt;

final class ZoneOffset {
    private static self $default;

    public string $identifier { get => $this->tz->getName(); }
    public bool $hasDst { get => false; } // TODO
    public string $name  { get => $this->tz->getName(); } // TODO
    public string $abbr  { get => $this->tz->getName(); } // TODO

    /** The time-offset if this is a fixed time-zone like UTC, GMT, CET etc. */
    public ?Duration $offset { get => null; } // TODO

    /** The linked canonical time-zone if this is an alias */
    public ?ZoneOffset $link { get => null; } // TODO

    // The zone was canonical in a previous version of the database.
    // Historical data for such zones is still preserved in the source code,
    // but it is not included when compiling the database with standard options.
    public bool $isDead  { get => false; } // TODO

    private function __construct(
        private \DateTimeZone $tz,
    ) {}

    public function toLegacyTz(): \DateTimeZone {
        return $this->tz;
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

        $identifier = $duration->isNegative ? '-' : '+'
            . str_pad((string)$duration->hours, '0', STR_PAD_LEFT)
            . ':' . str_pad((string)$duration->minutes, '0', STR_PAD_LEFT);
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
