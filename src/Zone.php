<?php declare(strict_types=1);

namespace time;

class Zone
{
    /** @var array<string, Zone> Registered zones by identifier */
    private static array $registry = [];

    public string $name {
        // FIXME: How to detect the human readable name of a time zone?
        get => $this->identifier;
    }

    /**
     * The time offset in case the zone is based on a fixed offset
     */
    public ?ZoneOffset $fixedOffset {
        get => $this->info->fixedOffset;
    }

    protected function __construct(
        public readonly string $identifier,
        public readonly ZoneInfo $info,
    ) {}

    public function __toString(): string
    {
        return $this->identifier;
    }

    public function getOffsetAt(Instant $instant): ZoneOffset
    {
        return $this->info->getOffsetAt($instant);
    }

    public static function fromIdentifier(string $identifier): self
    {
        $firstChar = $identifier[0] ?? null;
        if ($firstChar === '+' || $firstChar === '-') {
            return ZoneOffset::fromString($identifier);
        }

        if (\array_key_exists($identifier, self::$registry)) {
            return self::$registry[$identifier];
        }

        return self::fromBuildInIdentifier($identifier);
    }

    public static function register(Zone $zone): void
    {
        self::$registry[$zone->identifier] = $zone;
    }

    /** @return list<string> */
    public static function listIdentifiers(): array
    {
        return [
            ...\array_keys(self::$registry),
            ...\DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC),
        ];
    }

    private static function fromBuildInIdentifier(string $identifier): Zone
    {
        $legacy = new \DateTimeZone($identifier);

        // time zone abbreviations are not allowed here except for GMT and UTC
        $lower = \strtolower($identifier);
        if ($lower !== 'utc' && $lower !== 'gmt'
            &&\array_key_exists($lower, \DateTimeZone::listAbbreviations())
        ) {
            throw new \RuntimeException("Time zone identifier '{$identifier}' is ambiguous");
        }

        $info = new class ($legacy) extends ZoneInfo {
            private null|ZoneOffset|false $_fixedOffset = false;
            public ?ZoneOffset $fixedOffset {
                get {
                    if ($this->_fixedOffset !== false) {
                        return $this->_fixedOffset;
                    }

                    $fixedOffset = null;

                    // detect fixed offset by looking up transitions
                    // -> if no transition -> take offset at random date/time
                    // -> if only one starting at PHP_INT_MIN -> take it
                    /** @var false|list<array{ts: int, time: string, offset: int, isdst: bool, abbr: string}> $transitions */
                    $transitions = $this->legacy->getTransitions();
                    if ($transitions === false) {
                        $fixedOffset = new ZoneOffset(
                            \DateTime::createFromTimestamp(0)->setTimezone($this->legacy)->getOffset()
                        );
                    } elseif (\count($transitions) === 1 && $transitions[0]['ts'] === PHP_INT_MIN) {
                        $fixedOffset = new ZoneOffset($transitions[0]['offset']);
                    }

                    return $this->_fixedOffset = $fixedOffset;
                }
            }

            public function __construct(
                private readonly \DateTimeZone $legacy,
            ) {}

            public function getTransitions(
                ?Instant $from = null,
                ?Instant $until = null,
                ?int $limit = null,
            ): \Iterator {
                // There will never be a transition for zones with a fixed offset
                if ($this->fixedOffset) {
                    return;
                }

                if (!$from && !$until) {
                    $untilTs = \time();
                    $fromTs  = $untilTs - 60 * 60 * 24 * 365; // ~1year

                    // integer underflow
                    // @phpstan-ignore function.impossibleType
                    if (\is_float($fromTs)) {
                        $fromTs = PHP_INT_MIN;
                    }
                } elseif (!$from) {
                    $untilTs = $until->toUnixTimestampTuple()[0];
                    $fromTs  = $untilTs - 60 * 60 * 24 * 365; // ~1year

                    // integer underflow
                    // @phpstan-ignore function.impossibleType
                    if (\is_float($fromTs)) {
                        $fromTs = PHP_INT_MIN;
                    }
                } elseif (!$until) {
                    $fromTs  = $from->toUnixTimestampTuple()[0];
                    $untilTs = $fromTs + 60 * 60 * 24 * 365; // ~1year

                    // integer overflow
                    // @phpstan-ignore function.impossibleType
                    if (\is_float($untilTs)) {
                        $untilTs = PHP_INT_MAX;
                    }
                } else {
                    $fromTs  = $from->toUnixTimestampTuple()[0];
                    $untilTs = $until->toUnixTimestampTuple()[0];
                }

                $reverse = $fromTs > $untilTs;

                // Workaround bug GH-18051
                // With "timestamp_begin" set to the exact timestamp of a transition,
                // the transition might be reported twice.
                $getTransitions = function (int $from, int $to): array {
                    $transitions = $this->legacy->getTransitions($from, $to);

                    if (\count($transitions) >= 2 && $transitions[0]['ts'] === $transitions[1]['ts']) {
                        \array_shift($transitions);
                    }

                    return $transitions;
                };

                $getTransitions = $reverse
                    ? ($untilTs === \PHP_INT_MIN
                        ? static function (int $to, int $from) use ($getTransitions) {
                            return $getTransitions($from, $to - 1);
                        }
                        : static function (int $to, int $from) use ($getTransitions) {
                            $transitions = $getTransitions($from - 1, $to - 1);
                            \array_shift($transitions);
                            return $transitions;
                        }
                    )
                    : ($fromTs === \PHP_INT_MIN
                        ? static function (int $from, int $to) use ($getTransitions) {
                            return $getTransitions($from, $to - 1);
                        }
                        : static function (int $from, int $to) use ($getTransitions) {
                            $transitions = $getTransitions($from - 1, $to - 1);
                            \array_shift($transitions);
                            return $transitions;
                        }
                    );

                $transitions = $getTransitions($fromTs, $untilTs);
                // var_dump($transitions);

                if ($limit !== null) {
                    if ($limit < 0) {
                        $transitions = \array_slice($transitions, $limit, $limit * -1);
                    } else {
                        $transitions = \array_slice($transitions, 0, $limit);
                    }
                }

                foreach ($reverse ? \array_reverse($transitions) : $transitions as $transition) {
                    yield new ZoneTransition(
                        Instant::fromUnixTimestampTuple([$transition['ts'], 0]),
                        new ZoneOffset(totalSeconds: $transition['offset']),
                    );
                }
            }
        };

        return new Zone($legacy->getName(), $info);
    }
}
