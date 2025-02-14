<?php

namespace time;

final class Zone
{
    public string $identifier {
        get => $this->legacy->getName();
    }

    public string $name {
        // FIXME: "\DateTimeZone::getName()" represents the identifier, not the name
        get => $this->legacy->getName();
    }

    public bool $isAbbreviation {
        get => \array_key_exists(\strtolower($this->identifier), \DateTimeZone::listAbbreviations());
    }

    /**
     * The time offset if the zone is based on a fixed offset or abbreviation.
     */
    public ?Duration $offset {
        get {
            if ($this->isAbbreviation) {
                // timezonedb lookup
                return new Duration(
                    seconds: \DateTime::createFromTimestamp(0)->setTimezone($this->legacy)->getOffset()
                );
            }

            $match = \preg_match(
                '/^(?:GMT|UTC)?(?<sign>[+-])(?<h>\d\d)(:?(?<m>\d\d)(:?(?<s>\d\d))?)?/',
                $this->identifier,
                $matches
            );
            \assert($match !== false);
            if ($match) {
                $duration = new Duration(
                    hours: (int)$matches['h'],
                    minutes: (int)($matches['m'] ?? 0),
                    seconds: (int)($matches['s'] ?? 0),
                );

                if ($matches['sign'] === '-') {
                    $duration = $duration->negated();
                }

                return $duration;
            }

            // lookup transitions -> if only one starting at PHP_INT_MIN -> take it
            $transitions = $this->legacy->getTransitions();
            if (\count($transitions) === 1 && $transitions[0]['ts'] === PHP_INT_MIN) {
                return new Duration(seconds: $transitions[0]['offset']);
            }

            return null;
        }
    }

    private function __construct(
        private readonly \DateTimeZone $legacy,
    ) {}

    public function toLegacy(): \DateTimeZone {
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

    public static function fromOffset(Duration $offset): self {
        if ($offset->nanoOfSeconds) {
            throw new \ValueError("A time offset can not contain fractions of a second");
        }

        $identifier = $offset->isNegative ? '-' : '+'
            . \str_pad((string)$offset->hours, '0', STR_PAD_LEFT)
            . ':' . \str_pad((string)$offset->minuteOfHours, '0', STR_PAD_LEFT)
            . ($offset->secondOfMinutes ? ':' . \str_pad((string)$offset->secondOfMinutes, '0', STR_PAD_LEFT) : '');
        return new self(new \DateTimeZone($identifier));
    }

    /**
     * Get available zone IDs based on geographical regions.
     * Other supported zone IDs like the once based on offsets are not included.
     *
     * @return list<string>
     */
    public static function getAvailableRegionZoneIds(): array {
        return \DateTimeZone::listIdentifiers();
    }
}
