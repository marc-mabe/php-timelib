<?php declare(strict_types=1);

namespace time;

class LagacyDateTimeFormatter
{
    public function __construct(
        public string $format = 'Y-m-d H:i:s',
        public ?Zone $zone = null,
    ) {}

    /**
     * @throws InvalidValueException
     */
    public function format(Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        // Convert an Instanted into a Date&Time&Zoned
        // by preferring the optionally defined zone of the formatter
        if ($dateTimeZone instanceof Instanted) {
            if ($this->zone) {
                if (!$dateTimeZone instanceof Date
                    || !$dateTimeZone instanceof Time
                    || !$dateTimeZone instanceof Zoned
                    || $dateTimeZone->zone !== $this->zone
                ) {
                    $dateTimeZone = $dateTimeZone->instant->toZonedDateTime($this->zone);
                }
            } elseif ($dateTimeZone instanceof Zoned) {
                if (!$dateTimeZone instanceof Date || !$dateTimeZone instanceof Time) {
                    $dateTimeZone = $dateTimeZone->instant->toZonedDateTime($dateTimeZone->zone);
                }
            } else {
                $dateTimeZone = $dateTimeZone->instant;
            }
        }

        $formatted = '';
        $formatLen = \strlen($this->format);

        for ($i = 0; $i < $formatLen; $i++) {
            $chr   = $this->format[$i];
            $token = LagacyFormatToken::tryFrom($chr);

            if (!$token) {
                if (\preg_match('#^[a-z]$#i', $chr)) {
                    throw new InvalidValueException("Invalid format '{$this->format}': Unknown token '{$chr}' at {$i}");
                }

                $formatted .= $chr;
                continue;
            }

            if ($token === LagacyFormatToken::ESCAPE) {
                $formatted .= $this->format[++$i] ?? '';
                continue;
            }

            $formatted .= $this->formatToken($token, $dateTimeZone);
        }

        return $formatted;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatToken(LagacyFormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        \assert($token !== LagacyFormatToken::ESCAPE);

        return match ($token) {
            LagacyFormatToken::SecondsSinceUnixEpoch => $dateTimeZone instanceof Instanted
                ? (string)$dateTimeZone->instant->toUnixTimestamp()
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires an Instanted"),

            // Year
            LagacyFormatToken::Year,
            LagacyFormatToken::Year2Digit,
            LagacyFormatToken::YearExtended,
            LagacyFormatToken::YearExtendedSign => $this->formatYear($token, $dateTimeZone),
            LagacyFormatToken::IsLeapYear => $dateTimeZone instanceof Date
                ? ($dateTimeZone->calendar->isLeapYear($dateTimeZone->year) ? '1' : '0')
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),

            // Month
            LagacyFormatToken::Month => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->month
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::MonthWithLeadingZeros => $dateTimeZone instanceof Date
                ? \str_pad((string)$dateTimeZone->month, 2, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::MonthName => $dateTimeZone instanceof Date
                ? $dateTimeZone->calendar->getMonthName($dateTimeZone->year, $dateTimeZone->month)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::MonthAbbreviation => $dateTimeZone instanceof Date
                ? $dateTimeZone->calendar->getMonthAbbreviation($dateTimeZone->year, $dateTimeZone->month)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::DaysInMonth => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->calendar->getDaysInMonth($dateTimeZone->year, $dateTimeZone->month)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),

            // Week
            LagacyFormatToken::DayOfWeekName,
            LagacyFormatToken::DayOfWeekAbbreviation,
            LagacyFormatToken::DayOfWeekNumber => $this->formatDayOfWeek($token, $dateTimeZone),

            // Day
            LagacyFormatToken::DayOfMonth => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfMonth
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::DayOfMonthWithLeadingZeros => $dateTimeZone instanceof Date
                ? \str_pad((string)$dateTimeZone->dayOfMonth, 2, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::DayOfMonthOrdinalSuffix => $dateTimeZone instanceof Date
                ? match ($dateTimeZone->dayOfMonth) {
                    1, 21, 31 => 'st',
                    2, 22     => 'nd',
                    3, 23     => 'rd',
                    default   => 'th',
                }
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),
            LagacyFormatToken::DayOfYear => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfYear
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date"),

            // Time
            LagacyFormatToken::MeridiemAbbrLower => $dateTimeZone instanceof Time
                ? $dateTimeZone->hour >= 12 ? 'pm' : 'am'
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),
            LagacyFormatToken::MeridiemAbbrUpper => $dateTimeZone instanceof Time
                ? $dateTimeZone->hour >= 12 ? 'PM' : 'AM'
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),

            LagacyFormatToken::Hour12 => $dateTimeZone instanceof Time
                ? (string)($dateTimeZone->hour % 12)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),
            LagacyFormatToken::Hour12WithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)($dateTimeZone->hour % 12), 2, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),

            LagacyFormatToken::Hour24 => $dateTimeZone instanceof Time
                ? (string)$dateTimeZone->hour
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),
            LagacyFormatToken::Hour24WithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->hour, 2, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),

            LagacyFormatToken::MinuteWithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->minute, 2, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),
            LagacyFormatToken::SecondWithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->second, 2, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),

            LagacyFormatToken::MilliOfSecond => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->milliOfSecond, 3, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),
            LagacyFormatToken::MicroOfSecond => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->microOfSecond, 6, '0', STR_PAD_LEFT)
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),
            LagacyFormatToken::OptionalFractionOfSecondWithLeadingSeparator => $dateTimeZone instanceof Time
                ? ($dateTimeZone->nanoOfSecond
                    ? '.' . \rtrim(\str_pad((string)$dateTimeZone->nanoOfSecond, 9, '0', STR_PAD_LEFT), '0')
                    : ''
                )
                : throw new InvalidValueException("Unexpected format: '{$token->value}' requires a time"),

            // Time zone and offset
            LagacyFormatToken::TimezoneIdentifier => $this->formatZoneId($token, $dateTimeZone),
            LagacyFormatToken::TimezoneAbbrOrOffset => $this->formatZoneAbbreviationOrOffset($token, $dateTimeZone),
            LagacyFormatToken::IsDaylightSavingTime => "TODO[{$token->name}]", // TODO: Support IsDaylightSavingTime
            LagacyFormatToken::OffsetWithoutColon,
            LagacyFormatToken::OffsetWithColon,
            LagacyFormatToken::OffsetWithColonOrZ,
            LagacyFormatToken::OffsetInSeconds => $this->formatOffset($token, $dateTimeZone),
        };
    }

    private function formatYear(LagacyFormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        if (!$dateTimeZone instanceof Date) {
            throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date");
        }

        $yearAbs = (string)\abs($dateTimeZone->year);
        $sign    = $dateTimeZone->year < 0 ? '-' : '+';

        return match ($token) { // @phpstan-ignore match.unhandled
            LagacyFormatToken::Year => ($dateTimeZone->year < 0 ? $sign : '') . $yearAbs,
            LagacyFormatToken::Year2Digit => ($dateTimeZone->year < 0 ? $sign : '') . \substr($yearAbs, -2),
            LagacyFormatToken::YearExtended
                => ($dateTimeZone->year < 0 || $dateTimeZone->year >= 10000 ? $sign : '')
                . \str_pad($yearAbs, 4, '0', STR_PAD_LEFT),
            LagacyFormatToken::YearExtendedSign => $sign . \str_pad($yearAbs, 4, '0', STR_PAD_LEFT),
        };
    }

    /**
     * @throws InvalidValueException
     */
    private function formatDayOfWeek(LagacyFormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        if (!$dateTimeZone instanceof Date) {
            throw new InvalidValueException("Unexpected format: '{$token->value}' requires a date");
        }

        $dayOfWeek = $dateTimeZone->dayOfWeek;
        return (string)match ($token) { // @phpstan-ignore match.unhandled
            LagacyFormatToken::DayOfWeekName         => $dateTimeZone->calendar->getDayOfWeekName($dayOfWeek),
            LagacyFormatToken::DayOfWeekAbbreviation => $dateTimeZone->calendar->getDayOfWeekAbbreviation($dayOfWeek),
            LagacyFormatToken::DayOfWeekNumber       => $dayOfWeek,
        };
    }

    /**
     * @throws InvalidValueException
     */
    private function formatOffset(LagacyFormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $offset = null;
        if ($dateTimeZone instanceof ZonedDateTime) {
            $offset = $dateTimeZone->offset;
        } elseif ($dateTimeZone instanceof Zone) {
            $offset = $dateTimeZone->fixedOffset;
        } elseif ($dateTimeZone instanceof Zoned) {
            $offset = $dateTimeZone->zone->fixedOffset;
        }

        if ($offset === null) {
            throw new InvalidValueException(
                "Unexpected format: '{$token->value}' requires a date+time+zone or a fixed time offset"
            );
        }

        return match ($token) { // @phpstan-ignore match.unhandled
            LagacyFormatToken::OffsetWithoutColon => \str_replace(':', '', $offset->identifier),
            LagacyFormatToken::OffsetWithColon => $offset->identifier,
            LagacyFormatToken::OffsetWithColonOrZ => $offset->totalSeconds ? $offset->identifier : 'Z',
            LagacyFormatToken::OffsetInSeconds => (string)$offset->totalSeconds,
        };
    }

    /**
     * @throws InvalidValueException
     */
    private function formatZoneId(LagacyFormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $zone = null;
        if ($dateTimeZone instanceof Zone) {
            $zone = $dateTimeZone;
        } elseif ($dateTimeZone instanceof Zoned) {
            $zone = $dateTimeZone->zone;
        }

        if (!$zone) {
            throw new InvalidValueException(
                "Unexpected format: '{$token->value}' requires time zone"
            );
        }

        return $zone->identifier;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatZoneAbbreviationOrOffset(LagacyFormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $zone = null;
        if ($dateTimeZone instanceof Zone) {
            $zone = $dateTimeZone;
        } elseif ($dateTimeZone instanceof Zoned) {
            $zone = $dateTimeZone->zone;
        }

        if ($zone && $dateTimeZone instanceof Date && $dateTimeZone instanceof Time) {
            $zdt  = ZonedDateTime::fromDateTime($dateTimeZone, $dateTimeZone, zone: $zone);
            $abbr = ZoneAbbreviation::findAbbreviation($zone, $zdt->offset);

            return $abbr ?? 'GMT' . $this->formatOffset(LagacyFormatToken::OffsetWithoutColon, $zdt);
        }

        // Fallback to fixed offset if possible
        if ($zone?->fixedOffset) {
            return 'GMT' . $this->formatOffset(LagacyFormatToken::OffsetWithoutColon, $dateTimeZone);
        }

        throw new InvalidValueException(
            "Unexpected format: '{$token->value}' requires Date&Time&(Zone|Zoned) or a fixed time offset"
        );
    }
}
