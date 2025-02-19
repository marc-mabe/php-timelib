<?php declare(strict_types=1);

namespace time;

class Zone
{
    public readonly string $identifier;

    public string $name {
        // FIXME: How to detect the human readable name of a time zone?
        get => $this->identifier;
    }

    public bool $isAbbreviation {
        get => \array_key_exists(\strtolower($this->identifier), \DateTimeZone::listAbbreviations());
    }

    /**
     * The time offset if the zone is based on a fixed offset or abbreviation.
     */
    public ?ZoneOffset $offset {
        get {
            if ($this->isAbbreviation) {
                // timezonedb lookup
                $legacy = new \DateTimeZone($this->identifier);
                return ZoneOffset::fromDuration(new Duration(
                    seconds: \DateTime::createFromTimestamp(0)->setTimezone($legacy)->getOffset()
                ));
            }

            $match = \preg_match(
                '/^(?:GMT|UTC)?(?<sign>[+-])(?<h>\d\d):(?<m>\d\d)(:?(?<s>\d\d))?$/',
                $this->identifier,
                $matches
            );
            \assert($match !== false);
            if ($match) {
                $duration = new Duration(
                    hours: (int)$matches['h'],
                    minutes: (int)$matches['m'],
                    seconds: (int)($matches['s'] ?? 0),
                );

                if ($matches['sign'] === '-') {
                    $duration = $duration->negated();
                }

                return ZoneOffset::fromDuration($duration);
            }

            // lookup transitions -> if only one starting at PHP_INT_MIN -> take it
            $legacy      = new \DateTimeZone($this->identifier);
            $transitions = $legacy->getTransitions();
            if (\count($transitions) === 1 && $transitions[0]['ts'] === PHP_INT_MIN) {
                return ZoneOffset::fromDuration(new Duration(seconds: $transitions[0]['offset']));
            }

            return null;
        }
    }

    protected function __construct(string $identifier)
    {
        // check for time offset
        // lookup known regional time zones and abbreviations
        // normalize identifier
        $this->identifier = new \DateTimeZone($identifier)->getName();
    }

    public function __toString(): string
    {
        return $this->identifier;
    }

    public function format(DateTimeFormatter|string $format): string {
        $formatter = $format instanceof DateTimeFormatter ? $format : new DateTimeFormatter($format);
        return $formatter->format($this);
    }

    public static function fromIdentifier(string $identifier): self {
        return new self($identifier);
    }

    public static function fromSystem(): self {
        return self::fromIdentifier(date_default_timezone_get());
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
