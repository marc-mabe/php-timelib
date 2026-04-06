<?php declare(strict_types=1);

namespace time;

/** @phpstan-import-type FormatPatternToken from FormatPattern */
class DateTimeFormatter
{
    private const array QUARTER_ABBR = ['', 'Q1', 'Q2', 'Q3', 'Q4'];
    private const array QUARTER_WIDE = ['', '1st quarter', '2nd quarter', '3rd quarter', '4th quarter'];
    private const array QUARTER_NARROW = ['', '1', '2', '3', '4'];

    public readonly FormatPattern $formatPattern;

    /**
     * @throws InvalidFormatError
     */
    public function __construct(FormatPattern|string $pattern)
    {
        $this->formatPattern = \is_string($pattern) ? FormatPattern::parse($pattern) : $pattern;
    }

    /**
     * @throws InvalidValueException
     */
    public function format(Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        return $this->formatTokens($this->formatPattern->tokens, $dateTimeZone);
    }

    /**
     * @param list<FormatPatternToken> $tokens
     * @throws InvalidValueException
     */
    private function formatTokens(array $tokens, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $formatted = '';

        foreach ($tokens as $token) {
            if ($token['type'] === 'literal') {
                $formatted .= $token['value'];
            } elseif ($token['type'] === 'optional') {
                try {
                    /** @var list<FormatPatternToken> $optionalTokens */
                    $optionalTokens = $token['value'];
                    $formatted .= $this->formatTokens($optionalTokens, $dateTimeZone);
                } catch (InvalidValueException) {
                    // Skip optional section if any value is missing
                }
            } else {
                // token type is 'symbol'
                $formatted .= $this->formatSymbol($token['value'], $token['count'], $dateTimeZone);
            }
        }

        return $formatted;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatSymbol(FormatPatternSymbol $symbol, int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        return match ($symbol) {
            // Era
            FormatPatternSymbol::ERA => $this->formatEra($count, $dateTimeZone),

            // Year
            FormatPatternSymbol::YEAR => $this->formatYear($count, $dateTimeZone),
            FormatPatternSymbol::WEEK_BASED_YEAR => $this->formatWeekBasedYear($count, $dateTimeZone),
            FormatPatternSymbol::EXTENDED_YEAR => $this->formatExtendedYear($count, $dateTimeZone),
            FormatPatternSymbol::RELATED_GREGORIAN_YEAR => $this->formatRelatedGregorianYear($count, $dateTimeZone),

            // Quarter
            FormatPatternSymbol::QUARTER, FormatPatternSymbol::QUARTER_STANDALONE => $this->formatQuarter($count, $dateTimeZone),

            // Month
            FormatPatternSymbol::MONTH, FormatPatternSymbol::MONTH_STANDALONE => $this->formatMonth($count, $dateTimeZone),

            // Week
            FormatPatternSymbol::WEEK_OF_YEAR => $this->formatWeekOfYear($count, $dateTimeZone),
            FormatPatternSymbol::WEEK_OF_MONTH => $this->formatWeekOfMonth($count, $dateTimeZone),

            // Day
            FormatPatternSymbol::DAY_OF_MONTH => $this->formatDayOfMonth($count, $dateTimeZone),
            FormatPatternSymbol::DAY_OF_YEAR => $this->formatDayOfYear($count, $dateTimeZone),
            FormatPatternSymbol::DAY_OF_WEEK_IN_MONTH => $this->formatDayOfWeekInMonth($count, $dateTimeZone),
            FormatPatternSymbol::MODIFIED_JULIAN_DAY => $this->formatModifiedJulianDay($count, $dateTimeZone),

            // Weekday
            FormatPatternSymbol::WEEKDAY => $this->formatWeekday($count, $dateTimeZone),
            FormatPatternSymbol::LOCAL_DAY_OF_WEEK, FormatPatternSymbol::LOCAL_DAY_OF_WEEK_STANDALONE => $this->formatLocalDayOfWeek($count, $dateTimeZone),

            // Period
            FormatPatternSymbol::PERIOD => $this->formatPeriod($count, $dateTimeZone),
            FormatPatternSymbol::PERIOD_EXTENDED => $this->formatPeriodExtended($count, $dateTimeZone),
            FormatPatternSymbol::PERIOD_FLEXIBLE => $this->formatPeriodFlexible($count, $dateTimeZone),

            // Hour
            FormatPatternSymbol::HOUR_12 => $this->formatHour12($count, $dateTimeZone),
            FormatPatternSymbol::HOUR_23 => $this->formatHour23($count, $dateTimeZone),
            FormatPatternSymbol::HOUR_11 => $this->formatHour11($count, $dateTimeZone),
            FormatPatternSymbol::HOUR_24 => $this->formatHour24($count, $dateTimeZone),

            // Minute
            FormatPatternSymbol::MINUTE => $this->formatMinute($count, $dateTimeZone),

            // Second
            FormatPatternSymbol::SECOND => $this->formatSecond($count, $dateTimeZone),
            FormatPatternSymbol::FRACTIONAL_SECOND => $this->formatFractionalSecond($count, $dateTimeZone),
            FormatPatternSymbol::MILLISECONDS_IN_DAY => $this->formatMillisecondsInDay($count, $dateTimeZone),

            // Zone
            FormatPatternSymbol::ZONE_NAME => $this->formatZoneName($count, $dateTimeZone),
            FormatPatternSymbol::ZONE_GENERIC => $this->formatZoneGeneric($count, $dateTimeZone),
            FormatPatternSymbol::ZONE_OFFSET_GMT => $this->formatZoneOffsetGmt($count, $dateTimeZone),
            FormatPatternSymbol::ZONE_OFFSET_ISO_EXTENDED => $this->formatZoneOffsetIsoExtended($count, $dateTimeZone),
            FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC => $this->formatZoneOffsetIsoBasic($count, $dateTimeZone),
            FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC_LOCAL => $this->formatZoneOffsetIsoBasicLocal($count, $dateTimeZone),
            FormatPatternSymbol::ZONE_ID => $this->formatZoneId($dateTimeZone),

            // Literals
            FormatPatternSymbol::LITERAL_HYPHEN => '-',
            FormatPatternSymbol::LITERAL_COLON => ':',
            FormatPatternSymbol::LITERAL_DOT => '.',
            FormatPatternSymbol::LITERAL_SLASH => '/',
            FormatPatternSymbol::LITERAL_SPACE => ' ',
            FormatPatternSymbol::LITERAL_UNDERSCORE => '_',
            FormatPatternSymbol::LITERAL_COMMA => ',',
        };
    }

    /**
     * @throws InvalidValueException
     */
    private function requireDate(FormatPatternSymbol $symbol, Instanted|Date|Time|Zone|Zoned $dateTimeZone): Date
    {
        if (!$dateTimeZone instanceof Date) {
            throw new InvalidValueException("Format symbol '{$symbol->value}' ({$symbol->getDescription()}) requires a date");
        }

        return $dateTimeZone;
    }

    /**
     * @throws InvalidValueException
     */
    private function requireTime(FormatPatternSymbol $symbol, Instanted|Date|Time|Zone|Zoned $dateTimeZone): Time
    {
        if (!$dateTimeZone instanceof Time) {
            throw new InvalidValueException("Format symbol '{$symbol->value}' ({$symbol->getDescription()}) requires a time");
        }

        return $dateTimeZone;
    }

    /**
     * @throws InvalidValueException
     */
    private function requireZone(FormatPatternSymbol $symbol, Instanted|Date|Time|Zone|Zoned $dateTimeZone): Zone
    {
        if ($dateTimeZone instanceof Zone) {
            return $dateTimeZone;
        }

        if ($dateTimeZone instanceof Zoned) {
            return $dateTimeZone->zone;
        }

        throw new InvalidValueException("Format symbol '{$symbol->value}' ({$symbol->getDescription()}) requires a time zone");
    }

    /**
     * @throws InvalidValueException
     */
    private function requireOffset(FormatPatternSymbol $symbol, Instanted|Date|Time|Zone|Zoned $dateTimeZone): ZoneOffset
    {
        if ($dateTimeZone instanceof ZonedDateTime) {
            return $dateTimeZone->offset;
        }

        if ($dateTimeZone instanceof Zone) {
            if ($dateTimeZone->fixedOffset) {
                return $dateTimeZone->fixedOffset;
            }
        }

        if ($dateTimeZone instanceof Zoned) {
            if ($dateTimeZone->zone->fixedOffset) {
                return $dateTimeZone->zone->fixedOffset;
            }
        }

        throw new InvalidValueException("Format symbol '{$symbol->value}' ({$symbol->getDescription()}) requires a date+time+zone or a fixed time offset");
    }

    // === Era ===

    /**
     * @throws InvalidValueException
     */
    private function formatEra(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::ERA, $dateTimeZone);
        $isAD = $date->year > 0;

        return match (true) {
            $count <= 3 => $isAD ? 'AD' : 'BC',
            $count === 4 => $isAD ? 'Anno Domini' : 'Before Christ',
            default => $isAD ? 'A' : 'B',
        };
    }

    // === Year ===

    private function formatExtractedYear(int $year, int $count, bool $twoDigitsWhenCountIsTwo = false): string
    {
        if ($twoDigitsWhenCountIsTwo && $count === 2) {
            $twoDigit = \abs($year) % 100;
            $padded = \str_pad((string)$twoDigit, 2, '0', STR_PAD_LEFT);

            return $year < 0 ? '-' . $padded : $padded;
        }

        $yearStr = (string)\abs($year);
        if ($year < 0) {
            $yearStr = '-' . $yearStr;
        }

        if (\strlen($yearStr) < $count) {
            if ($year < 0) {
                $yearStr = '-' . \str_pad((string)\abs($year), $count, '0', STR_PAD_LEFT);
            } else {
                $yearStr = \str_pad($yearStr, $count, '0', STR_PAD_LEFT);
            }
        }

        return $yearStr;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatYear(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::YEAR, $dateTimeZone);

        return $this->formatExtractedYear($date->year, $count, twoDigitsWhenCountIsTwo: true);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatWeekBasedYear(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::WEEK_BASED_YEAR, $dateTimeZone);

        return $this->formatExtractedYear($date->yearOfWeek, $count, twoDigitsWhenCountIsTwo: true);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatExtendedYear(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::EXTENDED_YEAR, $dateTimeZone);

        return $this->formatExtractedYear($date->year, $count);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatRelatedGregorianYear(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::RELATED_GREGORIAN_YEAR, $dateTimeZone);
        $days = $date->calendar->getDaysSinceUnixEpochByYmd($date->year, $date->month, $date->dayOfMonth);
        $gregorianYmd = (new GregorianCalendar())->getYmdByDaysSinceUnixEpoch($days);

        return $this->formatExtractedYear($gregorianYmd[0], $count);
    }

    // === Quarter ===

    /**
     * @throws InvalidValueException
     */
    private function formatQuarter(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::QUARTER, $dateTimeZone);
        $quarter = (int)\ceil($date->month / 3);

        return match (true) {
            $count === 1 => (string)$quarter,
            $count === 2 => \str_pad((string)$quarter, 2, '0', STR_PAD_LEFT),
            $count === 3 => self::QUARTER_ABBR[$quarter],
            $count === 4 => self::QUARTER_WIDE[$quarter],
            default => self::QUARTER_NARROW[$quarter],
        };
    }

    // === Month ===

    /**
     * @throws InvalidValueException
     */
    private function formatMonth(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::MONTH, $dateTimeZone);
        $month = $date->month;
        $cal = $date->calendar;

        return match (true) {
            $count === 1 => (string)$month,
            $count === 2 => \str_pad((string)$month, 2, '0', STR_PAD_LEFT),
            $count === 3 => $cal->getMonthAbbreviation($date->year, $month),
            $count === 4 => $cal->getMonthName($date->year, $month),
            default => $cal->getMonthNarrow($date->year, $month),
        };
    }

    // === Week ===

    /**
     * @throws InvalidValueException
     */
    private function formatWeekOfYear(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::WEEK_OF_YEAR, $dateTimeZone);
        return \str_pad((string)$date->weekOfYear, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatWeekOfMonth(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::WEEK_OF_MONTH, $dateTimeZone);
        $daysInWeek = $date->calendar->getDaysInWeekByYmd($date->year, $date->month, $date->dayOfMonth);
        $weekOfMonth = \intdiv($date->dayOfMonth - 1, $daysInWeek) + 1;

        return (string)$weekOfMonth;
    }

    // === Day ===

    /**
     * @throws InvalidValueException
     */
    private function formatDayOfMonth(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::DAY_OF_MONTH, $dateTimeZone);
        return \str_pad((string)$date->dayOfMonth, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatDayOfYear(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::DAY_OF_YEAR, $dateTimeZone);
        return \str_pad((string)$date->dayOfYear, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatDayOfWeekInMonth(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::DAY_OF_WEEK_IN_MONTH, $dateTimeZone);
        $cal = $date->calendar;
        $y = $date->year;
        $m = $date->month;
        $d = $date->dayOfMonth;
        $daysInWeek = $cal->getDaysInWeekByYmd($y, $m, $d);
        $dow = $cal->getDayOfWeekByYmd($y, $m, $d);
        $firstOfMonthDow = $cal->getDayOfWeekByYmd($y, $m, 1);
        $offset = ($dow - $firstOfMonthDow + $daysInWeek) % $daysInWeek;
        $firstOccurrenceDay = 1 + $offset;
        $ordinal = \intdiv($d - $firstOccurrenceDay, $daysInWeek) + 1;

        return (string)$ordinal;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatModifiedJulianDay(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::MODIFIED_JULIAN_DAY, $dateTimeZone);
        $jdn = $date->calendar->getJdnByYmd($date->year, $date->month, $date->dayOfMonth);
        $mjd = $jdn - 2400001;
        return \str_pad((string)$mjd, $count, '0', STR_PAD_LEFT);
    }

    // === Weekday ===

    /**
     * @throws InvalidValueException
     */
    private function formatWeekday(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::WEEKDAY, $dateTimeZone);
        $dow = $date->dayOfWeek;
        $cal = $date->calendar;

        return match (true) {
            $count <= 3 => $cal->getDayOfWeekAbbreviation($dow),
            $count === 4 => $cal->getDayOfWeekName($dow),
            $count === 5 => $cal->getDayOfWeekNarrow($dow),
            default => $cal->getDayOfWeekShort($dow),
        };
    }

    /**
     * @throws InvalidValueException
     */
    private function formatLocalDayOfWeek(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $date = $this->requireDate(FormatPatternSymbol::LOCAL_DAY_OF_WEEK, $dateTimeZone);
        $dow = $date->dayOfWeek;
        $cal = $date->calendar;

        return match (true) {
            $count === 1 => (string)$dow,
            $count === 2 => \str_pad((string)$dow, 2, '0', STR_PAD_LEFT),
            $count === 3 => $cal->getDayOfWeekAbbreviation($dow),
            $count === 4 => $cal->getDayOfWeekName($dow),
            $count === 5 => $cal->getDayOfWeekNarrow($dow),
            default => $cal->getDayOfWeekShort($dow),
        };
    }

    // === Period ===

    /**
     * @throws InvalidValueException
     */
    private function formatPeriod(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::PERIOD, $dateTimeZone);
        return $time->hour < 12 ? 'AM' : 'PM';
    }

    /**
     * @throws InvalidValueException
     */
    private function formatPeriodExtended(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::PERIOD_EXTENDED, $dateTimeZone);

        if ($time->hour === 12 && $time->minute === 0 && $time->second === 0) {
            return match (true) {
                $count <= 3 => 'noon',
                $count === 4 => 'noon',
                default => 'n',
            };
        }

        if ($time->hour === 0 && $time->minute === 0 && $time->second === 0) {
            return match (true) {
                $count <= 3 => 'midnight',
                $count === 4 => 'midnight',
                default => 'mi',
            };
        }

        return $time->hour < 12 ? 'AM' : 'PM';
    }

    /**
     * @throws InvalidValueException
     */
    private function formatPeriodFlexible(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::PERIOD_FLEXIBLE, $dateTimeZone);
        $hour = $time->hour;

        $period = match (true) {
            $hour < 6 => 'at night',
            $hour < 12 => 'in the morning',
            $hour < 18 => 'in the afternoon',
            default => 'in the evening',
        };

        return $period;
    }

    // === Hour ===

    /**
     * @throws InvalidValueException
     */
    private function formatHour12(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::HOUR_12, $dateTimeZone);
        $hour = $time->hour % 12;
        if ($hour === 0) {
            $hour = 12;
        }
        return \str_pad((string)$hour, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatHour23(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::HOUR_23, $dateTimeZone);
        return \str_pad((string)$time->hour, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatHour11(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::HOUR_11, $dateTimeZone);
        $hour = $time->hour % 12;
        return \str_pad((string)$hour, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatHour24(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::HOUR_24, $dateTimeZone);
        $hour = $time->hour;
        if ($hour === 0) {
            $hour = 24;
        }
        return \str_pad((string)$hour, $count, '0', STR_PAD_LEFT);
    }

    // === Minute ===

    /**
     * @throws InvalidValueException
     */
    private function formatMinute(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::MINUTE, $dateTimeZone);
        return \str_pad((string)$time->minute, $count, '0', STR_PAD_LEFT);
    }

    // === Second ===

    /**
     * @throws InvalidValueException
     */
    private function formatSecond(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::SECOND, $dateTimeZone);
        return \str_pad((string)$time->second, $count, '0', STR_PAD_LEFT);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatFractionalSecond(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::FRACTIONAL_SECOND, $dateTimeZone);
        $nanos = \str_pad((string)$time->nanoOfSecond, 9, '0', STR_PAD_LEFT);
        return \substr($nanos, 0, $count);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatMillisecondsInDay(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $time = $this->requireTime(FormatPatternSymbol::MILLISECONDS_IN_DAY, $dateTimeZone);
        $millis = ($time->hour * 3600 + $time->minute * 60 + $time->second) * 1000 + $time->milliOfSecond;
        return \str_pad((string)$millis, $count, '0', STR_PAD_LEFT);
    }

    // === Zone ===

    /**
     * @throws InvalidValueException
     */
    private function formatZoneId(Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $zone = $this->requireZone(FormatPatternSymbol::ZONE_ID, $dateTimeZone);
        return $zone->identifier;
    }

    /**
     * @throws AmbiguousValueException
     * @throws InvalidValueException
     */
    private function formatZoneName(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $zone = $this->requireZone(FormatPatternSymbol::ZONE_NAME, $dateTimeZone);

        if ($dateTimeZone instanceof Date && $dateTimeZone instanceof Time) {
            $zdt = ZonedDateTime::fromDateTime($dateTimeZone, $dateTimeZone, zone: $zone);
            $abbr = ZoneAbbreviation::findAbbreviation($zone, $zdt->offset);

            if ($abbr !== null) {
                if ($count <= 3) {
                    return $abbr;
                }
                return $abbr;
            }
        }

        $offset = $this->requireOffset(FormatPatternSymbol::ZONE_NAME, $dateTimeZone);
        return $this->formatGmtOffset($offset, $count >= 4);
    }

    /**
     * @throws AmbiguousValueException
     * @throws InvalidValueException
     */
    private function formatZoneGeneric(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $zone = $this->requireZone(FormatPatternSymbol::ZONE_GENERIC, $dateTimeZone);

        if ($count === 1) {
            if ($dateTimeZone instanceof Date && $dateTimeZone instanceof Time) {
                $zdt = ZonedDateTime::fromDateTime($dateTimeZone, $dateTimeZone, zone: $zone);
                $abbr = ZoneAbbreviation::findAbbreviation($zone, $zdt->offset);
                if ($abbr !== null) {
                    return $abbr;
                }
            }
        }

        return $zone->identifier;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatZoneOffsetGmt(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $offset = $this->requireOffset(FormatPatternSymbol::ZONE_OFFSET_GMT, $dateTimeZone);

        if ($offset->totalSeconds === 0) {
            return 'GMT';
        }

        return $this->formatGmtOffset($offset, $count === 4);
    }

    private function formatGmtOffset(ZoneOffset $offset, bool $full): string
    {
        $totalSeconds = $offset->totalSeconds;
        $sign = $totalSeconds < 0 ? '-' : '+';
        $totalSeconds = \abs($totalSeconds);

        $hours = \intdiv($totalSeconds, 3600);
        $minutes = \intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        if ($full) {
            $result = \sprintf('GMT%s%02d:%02d', $sign, $hours, $minutes);
            if ($seconds !== 0) {
                $result .= \sprintf(':%02d', $seconds);
            }

            return $result;
        }

        $result = \sprintf('GMT%s%d', $sign, $hours);
        if ($minutes !== 0 || $seconds !== 0) {
            $result .= \sprintf(':%02d', $minutes);
            if ($seconds !== 0) {
                $result .= \sprintf(':%02d', $seconds);
            }
        }

        return $result;
    }

    /**
     * @throws InvalidValueException
     */
    private function formatZoneOffsetIsoBasic(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $offset = $this->requireOffset(FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC, $dateTimeZone);

        if ($offset->totalSeconds === 0) {
            return 'Z';
        }

        return $this->formatOffsetByCount($offset, $count);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatZoneOffsetIsoBasicLocal(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $offset = $this->requireOffset(FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC_LOCAL, $dateTimeZone);
        return $this->formatOffsetByCount($offset, $count);
    }

    /**
     * @throws InvalidValueException
     */
    private function formatZoneOffsetIsoExtended(int $count, Instanted|Date|Time|Zone|Zoned $dateTimeZone): string
    {
        $offset = $this->requireOffset(FormatPatternSymbol::ZONE_OFFSET_ISO_EXTENDED, $dateTimeZone);

        if ($count === 4) {
            return $this->formatGmtOffset($offset, true);
        }

        if ($count === 5 && $offset->totalSeconds === 0) {
            return 'Z';
        }

        $totalSeconds = $offset->totalSeconds;
        $sign = $totalSeconds < 0 ? '-' : '+';
        $totalSeconds = \abs($totalSeconds);

        $hours = \intdiv($totalSeconds, 3600);
        $minutes = \intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        if ($count <= 3) {
            return \sprintf('%s%02d%02d', $sign, $hours, $minutes);
        }

        $result = \sprintf('%s%02d:%02d', $sign, $hours, $minutes);
        if ($seconds !== 0) {
            $result .= \sprintf(':%02d', $seconds);
        }
        return $result;
    }

    private function formatOffsetByCount(ZoneOffset $offset, int $count): string
    {
        $totalSeconds = $offset->totalSeconds;
        $sign = $totalSeconds < 0 ? '-' : '+';
        $totalSeconds = \abs($totalSeconds);

        $hours = \intdiv($totalSeconds, 3600);
        $minutes = \intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return match ($count) {
            1 => $minutes === 0 && $seconds === 0
                ? \sprintf('%s%02d', $sign, $hours)
                : \sprintf('%s%02d%02d', $sign, $hours, $minutes),
            2 => \sprintf('%s%02d%02d', $sign, $hours, $minutes),
            3 => \sprintf('%s%02d:%02d', $sign, $hours, $minutes),
            4 => $seconds === 0
                ? \sprintf('%s%02d%02d', $sign, $hours, $minutes)
                : \sprintf('%s%02d%02d%02d', $sign, $hours, $minutes, $seconds),
            default => $seconds === 0
                ? \sprintf('%s%02d:%02d', $sign, $hours, $minutes)
                : \sprintf('%s%02d:%02d:%02d', $sign, $hours, $minutes, $seconds),
        };
    }
}
