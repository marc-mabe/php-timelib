<?php

namespace dt;

class DateTimeFormatter
{
    public function __construct(
        public string $format = 'Y-m-d H:i:s',
    ) {}

    public function format(Date|Time|ZoneOffset|Zoned $dateTimeZoned): string
    {
        $formatted = '';
        $formatLen = \strlen($this->format);
        $inEscape  = false;

        for ($i = 0; $i < $formatLen; $i++) {
            $chr   = $this->format[$i];
            $token = FormatToken::tryFrom($chr);

            if (!$token) {
                if (!$inEscape && preg_match('#^[a-z]$#i', $chr)) {
                    throw new \ValueError("Invalid format '{$this->format}': '{$chr}' is an unknown token at {$i}");
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

            $formatted .= $this->formatToken($token, $dateTimeZoned);
        }

        return $formatted;
    }

    private function formatToken(FormatToken $token, Date|Time|ZoneOffset|Zoned $dateTimeZone): string
    {
        return match ($token) {
            // Year
            FormatToken::IsLeapYear => "TODO[{$token->name}]",
            FormatToken::Year => $dateTimeZone instanceof Date
                ? str_pad((string)$dateTimeZone->year, 0, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),

            FormatToken::Year2Digit => "TODO[{$token->name}]",
            FormatToken::YearExtended => "TODO[{$token->name}]",
            FormatToken::YearExtendedPlus => "TODO[{$token->name}]",
            FormatToken::YearOfWeekIso => "TODO[{$token->name}]",

            // Month
            FormatToken::MonthName => "TODO[{$token->name}]",
            FormatToken::MonthName3Letter => "TODO[{$token->name}]",
            FormatToken::MonthNumber => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->month->value
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::MonthNumberWithLeadingZeros => $dateTimeZone instanceof Date
                ? str_pad((string)$dateTimeZone->month->value, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DaysInMonth => "TODO[{$token->name}]",

            // Week
            FormatToken::WeekOfYearIso => "TODO[{$token->name}]",

            // Day
            FormatToken::DayOfMonth => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfMonth
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfMonthWithLeadingZeros => $dateTimeZone instanceof Date
                ? str_pad((string)$dateTimeZone->dayOfMonth, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfMonthOrdinalSuffix => "TODO[{$token->name}]",
            FormatToken::DayOfYear => $dateTimeZone instanceof Date
                ? (string)$dateTimeZone->dayOfYear
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a date"),
            FormatToken::DayOfWeekName3Letter => "TODO[{$token->name}]",
            FormatToken::DayOfWeekName => "TODO[{$token->name}]",
            FormatToken::DayOfWeekNumber => "TODO[{$token->name}]",
            FormatToken::DayOfWeekNumberIso => "TODO[{$token->name}]",

            // Time
            FormatToken::MeridiemAbbrLower => "TODO[{$token->name}]",
            FormatToken::MeridiemAbbrUpper => "TODO[{$token->name}]",

            FormatToken::Hour12 => $dateTimeZone instanceof Time
                ? (string)($dateTimeZone->hour % 12)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::Hour12WithLeadingZeros => $dateTimeZone instanceof Time
                ? str_pad((string)($dateTimeZone->hour % 12), 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::Hour24 => $dateTimeZone instanceof Time
                ? (string)$dateTimeZone->hour
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::Hour24WithLeadingZeros => $dateTimeZone instanceof Time
                ? str_pad((string)$dateTimeZone->hour, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            FormatToken::MinuteWithLeadingZeros => $dateTimeZone instanceof Time
                ? str_pad((string)$dateTimeZone->minute, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::SecondWithLeadingZeros => $dateTimeZone instanceof Time
                ? str_pad((string)$dateTimeZone->second, 2, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::SecondsSinceUnixEpoch => "TODO[{$token->name}]",

            FormatToken::MilliOfSecond => $dateTimeZone instanceof Time
                ? str_pad((string)$dateTimeZone->milliOfSecond, 3, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),
            FormatToken::MicroOfSecond => $dateTimeZone instanceof Time
                ? str_pad((string)$dateTimeZone->microOfSecond, 6, '0', STR_PAD_LEFT)
                : throw new \ValueError("Unexpected format: '{$token->value}' requires a time"),

            // Time zone and offset
            FormatToken::TimezoneIdentifier => $this->formatTzId($token, $dateTimeZone),
            FormatToken::TimezoneAbbrOrOffset => $this->formatTzId($token, $dateTimeZone),
            FormatToken::IsDaylightSavingTime => "TODO[{$token->name}]",
            FormatToken::OffsetWithoutColon => $this->formatTzOffset($token, $dateTimeZone),
            FormatToken::OffsetWithColon => $this->formatTzOffset($token, $dateTimeZone),
            FormatToken::OffsetWithColonOrZ => $this->formatTzOffset($token, $dateTimeZone),
            FormatToken::OffsetInSeconds => $this->formatTzOffset($token, $dateTimeZone),
        };
    }

    private function formatTzOffset(FormatToken $token, Date|Time|ZoneOffset|Zoned $dateTimeZone): string
    {
        $zoneOffset = null;
        if ($dateTimeZone instanceof ZoneOffset) {
            $zoneOffset = $dateTimeZone;
        } elseif ($dateTimeZone instanceof Zoned) {
            $zoneOffset = $dateTimeZone->zoneOffset;
        }

        $offset = $zoneOffset->offset;
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
                . str_pad($offset->hours, 2, '0', STR_PAD_LEFT)
                . str_pad($offset->minutes, 2, '0', STR_PAD_LEFT),
                // TODO: Should this add seconds if present?
            FormatToken::OffsetWithColon =>
                ($offset->isNegative ? '-' : '+')
                . str_pad($offset->hours, 2, '0', STR_PAD_LEFT)
                . ':'
                . str_pad($offset->minutes, 2, '0', STR_PAD_LEFT),
                // TODO: Should this add seconds if present?
            FormatToken::OffsetWithColonOrZ => $offset->isEmpty()
                ? 'Z'
                : $this->formatTzOffset(FormatToken::OffsetWithColon, $dateTimeZone),
            FormatToken::OffsetInSeconds => (string)$offset->seconds,
        };
    }

    private function formatTzId(FormatToken $token, Date|Time|ZoneOffset|Zoned $dateTimeZone): string
    {
        $zoneOffset = null;
        if ($dateTimeZone instanceof ZoneOffset) {
            $zoneOffset = $dateTimeZone;
        } elseif ($dateTimeZone instanceof Zoned) {
            $zoneOffset = $dateTimeZone->zoneOffset;
        }

        if (!$zoneOffset) {
            throw new \ValueError(
                "Unexpected format: '{$token->value}' requires time zone"
            );
        }

        return match ($token) {
            FormatToken::TimezoneIdentifier => $zoneOffset->identifier,
            FormatToken::TimezoneAbbrOrOffset => $zoneOffset->abbr ?: $this->formatTzOffset(FormatToken::OffsetWithColon, $dateTimeZone),
        };
    }
}
