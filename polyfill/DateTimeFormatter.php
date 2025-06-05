<?php declare(strict_types=1);

namespace time;

class DateTimeFormatter
{
    public function __construct(
        public string $format = 'Y-m-d H:i:s',
        public ?Zone $zone = null,
    ) {}

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
            $token = FormatToken::tryFrom($chr);

            if (!$token) {
                if (\preg_match('#^[a-z]$#i', $chr)) {
                    throw new \ValueError("Invalid format '{$this->format}': Unknown token '{$chr}' at {$i}");
                }

                $formatted .= $chr;
                continue;
            }

            if ($token === FormatToken::ESCAPE) {
                $formatted .= $this->format[++$i] ?? '';
                continue;
            }

            $formatted .= $this->formatToken($token, $dateTimeZone);
        }

        return $formatted;
    }

    private function formatToken(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        return match ($token) {
            FormatToken::SecondsSinceUnixEpoch => $dateTimeZone instanceof Instanted
                ? (string)$dateTimeZone->instant->toUnixTimestamp()
                : throw new \ValueError("Unexpected format: '{$token->value}' requires an Instanted"),

            // Year
            FormatToken::Year,
            FormatToken::Year2Digit,
            FormatToken::YearExtended,
            FormatToken::YearExtendedSign => $this->formatYear($token, $dateTimeZone),
            FormatToken::IsLeapYear => $dateTimeZone instanceof Date
                ? ($dateTimeZone->calendar->isLeapYear($dateTimeZone->year) ? '1' : '0')
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),

            // Month
            FormatToken::Month => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->month
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthWithLeadingZeros => $dateTimeZone instanceof Date
                ? \str_pad((string)$dateTimeZone->month, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthName => $dateTimeZone instanceof Date
                ? $dateTimeZone->calendar->getNameOfMonth($dateTimeZone->year, $dateTimeZone->month)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthAbbreviation => $dateTimeZone instanceof Date
                ? $dateTimeZone->calendar->getAbbreviationOfMonth($dateTimeZone->year, $dateTimeZone->month)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DaysInMonth => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->calendar->getDaysInMonth($dateTimeZone->year, $dateTimeZone->month)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),

            // Week
            FormatToken::WeekOfYearIso => $dateTimeZone instanceof Date
                ? (string)WeekInfo::fromIso()->getWeekOfYear($dateTimeZone)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::YearOfWeekIso => $dateTimeZone instanceof Date
                ? (string)WeekInfo::fromIso()->getYearOfWeek($dateTimeZone)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfWeekName,
            FormatToken::DayOfWeekName3Letter,
            FormatToken::DayOfWeekNumber,
            FormatToken::DayOfWeekNumberIso => $this->formatDayOfWeek($token, $dateTimeZone),

            // Day
            FormatToken::DayOfMonth => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfMonth
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfMonthWithLeadingZeros => $dateTimeZone instanceof Date
                ? \str_pad((string)$dateTimeZone->dayOfMonth, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfMonthOrdinalSuffix => $dateTimeZone instanceof Date
                ? match ($dateTimeZone->dayOfMonth) {
                    1, 21, 31 => 'st',
                    2, 22     => 'nd',
                    3, 23     => 'rd',
                    default   => 'th',
                }
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfYear => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfYear
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),

            // Time
            FormatToken::MeridiemAbbrLower => $dateTimeZone instanceof Time
                ? $dateTimeZone->hour >= 12 ? 'pm' : 'am'
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::MeridiemAbbrUpper => $dateTimeZone instanceof Time
                ? $dateTimeZone->hour >= 12 ? 'PM' : 'AM'
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            FormatToken::Hour12 => $dateTimeZone instanceof Time
                ? (string)($dateTimeZone->hour % 12)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::Hour12WithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)($dateTimeZone->hour % 12), 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            FormatToken::Hour24 => $dateTimeZone instanceof Time
                ? (string)$dateTimeZone->hour
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::Hour24WithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->hour, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            FormatToken::MinuteWithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->minute, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::SecondWithLeadingZeros => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->second, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            FormatToken::MilliOfSecond => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->milliOfSecond, 3, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::MicroOfSecond => $dateTimeZone instanceof Time
                ? \str_pad((string)$dateTimeZone->microOfSecond, 6, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::OptionalFractionOfSecondWithLeadingSeparator => $dateTimeZone instanceof Time
                ? ($dateTimeZone->nanoOfSecond
                    ? '.' . \rtrim(\str_pad((string)$dateTimeZone->nanoOfSecond, 9, '0', STR_PAD_LEFT), '0')
                    : ''
                )
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            // Time zone and offset
            FormatToken::TimezoneIdentifier => $this->formatZoneId($token, $dateTimeZone),
            FormatToken::TimezoneAbbrOrOffset => $this->formatZoneAbbreviationOrOffset($token, $dateTimeZone),
            FormatToken::IsDaylightSavingTime => "TODO[{$token->name}]", // TODO: Support IsDaylightSavingTime
            FormatToken::OffsetWithoutColon,
            FormatToken::OffsetWithColon,
            FormatToken::OffsetWithColonOrZ,
            FormatToken::OffsetInSeconds => $this->formatOffset($token, $dateTimeZone),

            default => throw new \LogicException("Unhandled token '{$token->value}'"),
        };
    }

    private function formatYear(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        if (!$dateTimeZone instanceof Date) {
            throw new \ValueError("Unexpected format: '{$token->value}' requires a date");
        }

        $yearAbs = (string)\abs($dateTimeZone->year);
        $sign    = $dateTimeZone->year < 0 ? '-' : '+';

        return match ($token) {
            FormatToken::Year => ($dateTimeZone->year < 0 ? $sign : '') . $yearAbs,
            FormatToken::Year2Digit => ($dateTimeZone->year < 0 ? $sign : '') . \substr($yearAbs, -2),
            FormatToken::YearExtended
                => ($dateTimeZone->year < 0 || $dateTimeZone->year >= 10000 ? $sign : '')
                . \str_pad($yearAbs, 4, '0', STR_PAD_LEFT),
            FormatToken::YearExtendedSign => $sign . \str_pad($yearAbs, 4, '0', STR_PAD_LEFT),
            default => throw new \LogicException("Unhandled token '{$token->value}'"),
        };
    }

    private function formatDayOfWeek(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        if (!$dateTimeZone instanceof Date) {
            throw new \ValueError("Unexpected format: '{$token->value}' requires a date");
        }

        $dayOfWeek = $dateTimeZone->dayOfWeek;
        return (string)match ($token) {
            FormatToken::DayOfWeekName => $dayOfWeek->name,
            FormatToken::DayOfWeekName3Letter => \substr($dayOfWeek->name, 0, 3),
            FormatToken::DayOfWeekNumber => $dayOfWeek->getIsoNumber() === 7 ? 0 : $dayOfWeek->getIsoNumber(),
            FormatToken::DayOfWeekNumberIso => $dayOfWeek->getIsoNumber(),
            default => throw new \LogicException("Unhandled token '{$token->value}'"),
        };
    }

    private function formatOffset(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
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
            throw new \ValueError(
                "Unexpected format: '{$token->value}' requires a date+time+zone or a fixed time offset"
            );
        }

        return match ($token) {
            FormatToken::OffsetWithoutColon => \str_replace(':', '', $offset->identifier),
            FormatToken::OffsetWithColon => $offset->identifier,
            FormatToken::OffsetWithColonOrZ => $offset->totalSeconds ? $offset->identifier : 'Z',
            FormatToken::OffsetInSeconds => (string)$offset->totalSeconds,
            default => throw new \LogicException("Unhandled token '{$token->value}'"),
        };
    }

    private function formatZoneId(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $zone = null;
        if ($dateTimeZone instanceof Zone) {
            $zone = $dateTimeZone;
        } elseif ($dateTimeZone instanceof Zoned) {
            $zone = $dateTimeZone->zone;
        }

        if (!$zone) {
            throw new \ValueError(
                "Unexpected format: '{$token->value}' requires time zone"
            );
        }

        return $zone->identifier;
    }

    private function formatZoneAbbreviationOrOffset(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
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

            return $abbr ?? 'GMT' . $this->formatOffset(FormatToken::OffsetWithoutColon, $zdt);
        }

        // Fallback to fixed offset if possible
        if ($zone?->fixedOffset) {
            return 'GMT' . $this->formatOffset(FormatToken::OffsetWithoutColon, $dateTimeZone);
        }

        throw new \ValueError(
            "Unexpected format: '{$token->value}' requires Date&Time&(Zone|Zoned) or a fixed time offset"
        );
    }
}
