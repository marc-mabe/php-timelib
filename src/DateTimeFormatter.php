<?php

namespace time;

class DateTimeFormatter
{
    public function __construct(
        public string $format = 'Y-m-d H:i:s',
    ) {}

    public function format(Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $formatted = '';
        $formatLen = \strlen($this->format);
        $inEscape  = false;

        for ($i = 0; $i < $formatLen; $i++) {
            $chr   = $this->format[$i];
            $token = FormatToken::tryFrom($chr);

            if (!$token) {
                if (!$inEscape && \preg_match('#^[a-z]$#i', $chr)) {
                    throw new \ValueError("Invalid format '{$this->format}': Unknown token '{$chr}' at {$i}");
                }

                $formatted .= $chr;
                continue;
            }

            if ($token === FormatToken::ESCAPE) {
                if ($inEscape) {
                    $inEscape = false;
                    $formatted .= $chr;
                } else {
                    $inEscape = true;
                }

                continue;
            }

            $formatted .= $this->formatToken($token, $dateTimeZone);
        }

        return $formatted;
    }

    private function formatToken(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        return match ($token) {
            // Year
            FormatToken::Year,
            FormatToken::Year2Digit,
            FormatToken::YearExtended,
            FormatToken::YearExtendedSign => $this->formatYear($token, $dateTimeZone),
            FormatToken::IsLeapYear => $dateTimeZone instanceof Date
                ? (GregorianCalendar::isLeapYear($dateTimeZone->year) ? '1' : '0')
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::YearOfWeekIso => "TODO[{$token->name}]",

            // Month
            FormatToken::Month => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->month->value
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthWithLeadingZeros => $dateTimeZone instanceof Date
                ? \str_pad((string)$dateTimeZone->month->value, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthName => $dateTimeZone instanceof Date
                ? $dateTimeZone->month->name
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthAbbreviation => $dateTimeZone instanceof Date
                ? $dateTimeZone->month->getAbbreviation()
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DaysInMonth => $dateTimeZone instanceof Date
                ? (string)GregorianCalendar::getDaysInMonth($dateTimeZone->year, $dateTimeZone->month)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),

            // Week
            FormatToken::WeekOfYearIso => "TODO[{$token->name}]",
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
            FormatToken::DayOfMonthOrdinalSuffix => "TODO[{$token->name}]",
            FormatToken::DayOfYear => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfYear
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),

            // Time
            FormatToken::MeridiemAbbrLower => "TODO[{$token->name}]",
            FormatToken::MeridiemAbbrUpper => "TODO[{$token->name}]",

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
            FormatToken::SecondsSinceUnixEpoch => "TODO[{$token->name}]",

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
            FormatToken::IsDaylightSavingTime => "TODO[{$token->name}]",
            FormatToken::OffsetWithoutColon,
            FormatToken::OffsetWithColon,
            FormatToken::OffsetWithColonOrZ,
            FormatToken::OffsetInSeconds => $this->formatOffset($token, $dateTimeZone),
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
            FormatToken::DayOfWeekNumber => $dayOfWeek->value === 7 ? 0 : $dayOfWeek->value,
            FormatToken::DayOfWeekNumberIso => $dayOfWeek->value,
        };
    }

    private function formatOffset(FormatToken $token, Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $offset = null;
        if ($dateTimeZone instanceof Zone) {
            $offset = $dateTimeZone->offset;
        } elseif ($dateTimeZone instanceof Zoned) {
            $offset = $dateTimeZone->zone->offset;
        }

        if ($offset === null) {
            if ($dateTimeZone instanceof ZonedDateTime) {
                $offset = $dateTimeZone->offset;
            }
        }

        if ($offset === null) {
            throw new \ValueError(
                "Unexpected format: '{$token->value}' requires a date/time and a time zone or a time zone with fixed offset"
            );
        }

        return match ($token) {
            FormatToken::OffsetWithoutColon =>
                ($offset->isNegative ? '-' : '+')
                . \str_pad($offset->hours, 2, '0', STR_PAD_LEFT)
                . \str_pad($offset->minutes, 2, '0', STR_PAD_LEFT)
                . ($offset->seconds ? \str_pad($offset->seconds, 2, '0', STR_PAD_LEFT) : ''),
            FormatToken::OffsetWithColon =>
                ($offset->isNegative ? '-' : '+')
                . \str_pad($offset->hours, 2, '0', STR_PAD_LEFT)
                . ':' . \str_pad($offset->minutes, 2, '0', STR_PAD_LEFT)
                . ($offset->seconds ? ':' . \str_pad($offset->seconds, 2, '0', STR_PAD_LEFT) : ''),
            FormatToken::OffsetWithColonOrZ => $offset->isEmpty()
                ? 'Z'
                : $this->formatOffset(FormatToken::OffsetWithColon, $dateTimeZone),
            FormatToken::OffsetInSeconds => (string)$offset->seconds,
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
        // If we have a zone and it's an abbreviation -> take it
        $zone = null;
        if ($dateTimeZone instanceof Zone) {
            $zone = $dateTimeZone;
        } elseif ($dateTimeZone instanceof Zoned) {
            $zone = $dateTimeZone->zone;
        }

        if ($zone?->isAbbreviation) {
            return $zone->identifier;
        }

        // If we have a date+time+zone -> lookup timezonedb
        if ($dateTimeZone instanceof Date && $dateTimeZone instanceof Time && $dateTimeZone instanceof Zoned) {
            $z = $dateTimeZone->dayOfYear - 1;
            $i = str_pad($dateTimeZone->minute, 2, '0', STR_PAD_LEFT);
            $s = str_pad($dateTimeZone->second, 2, '0', STR_PAD_LEFT);

            return \DateTimeImmutable::createFromFormat(
                'Y-z G:i:s',
                "{$dateTimeZone->year}-{$z} {$dateTimeZone->hour}:{$i}:{$s}",
                $dateTimeZone->zone->toLegacy(),
            )->format('T');
        }

        // if we have a zone with fixed offset -> take it
        if ($zone?->offset) {
            return 'GMT' . $this->formatOffset(FormatToken::OffsetWithoutColon, $dateTimeZone);
        }

        throw new \ValueError(
            "Unexpected format: '{$token->value}' requires a time offset"
        );
    }
}
