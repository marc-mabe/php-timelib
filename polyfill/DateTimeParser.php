<?php declare(strict_types=1);

namespace time;

/**
 * @phpstan-import-type FormatPatternToken from FormatPattern
 *
 * @phpstan-type ParsedFields array{
 *     year?: int,
 *     yearOfEra?: int,
 *     weekBasedYear?: int,
 *     era?: string,
 *     month?: int,
 *     dayOfMonth?: int,
 *     dayOfYear?: int,
 *     dayOfWeek?: int,
 *     weekOfYear?: int,
 *     weekOfMonth?: int,
 *     quarter?: int,
 *     jdn?: int,
 *     hour?: int,
 *     minute?: int,
 *     second?: int,
 *     nanoOfSecond?: int,
 *     amPm?: string,
 *     zone?: Zone,
 *     offset?: ZoneOffset,
 * }
 */
class DateTimeParser
{
    public readonly FormatPattern $formatPattern;

    private readonly Calendar $calendar;

    public function __construct(
        FormatPattern|string $pattern,
        ?Calendar $calendar = null,
    ) {
        $this->calendar = $calendar ?? IsoCalendar::getInstance();
        $this->formatPattern = \is_string($pattern) ? FormatPattern::parse($pattern) : $pattern;
    }

    /**
     * Parse text into a ZonedDateTime
     *
     * @throws InvalidValueException
     */
    public function parseToZonedDateTime(string $text, ?Zone $defaultZone = null): ZonedDateTime
    {
        $parsed = $this->parse($text);

        $zone = $parsed['zone'] ?? $defaultZone ?? new ZoneOffset(0);
        $year = $this->extractYear($parsed);
        $month = $this->extractMonth($parsed);
        $dayOfMonth = $this->extractDayOfMonth($parsed);
        $hour = $this->extractHour($parsed);
        $minute = $this->extractMinute($parsed);
        $second = $this->extractSecond($parsed);
        $nanoOfSecond = $this->extractNanoOfSecond($parsed);
        $dayOfYear = $this->extractDayOfYear($parsed);

        if (isset($parsed['era']) && $parsed['era'] === 'BC' && $year > 0) {
            $year = -$year + 1;
        }

        if (isset($parsed['amPm'])) {
            if ($parsed['amPm'] === 'PM' && $hour < 12) {
                $hour += 12;
            } elseif ($parsed['amPm'] === 'AM' && $hour === 12) {
                $hour = 0;
            }
        }

        if (isset($parsed['dayOfYear']) && !isset($parsed['month']) && !isset($parsed['dayOfMonth'])) {
            return ZonedDateTime::fromYd(
                year: $year,
                dayOfYear: $dayOfYear,
                hour: $hour,
                minute: $minute,
                second: $second,
                nanoOfSecond: $nanoOfSecond,
                zone: $zone,
                calendar: $this->calendar,
            );
        }

        return ZonedDateTime::fromYmd(
            year: $year,
            month: $month,
            dayOfMonth: $dayOfMonth,
            hour: $hour,
            minute: $minute,
            second: $second,
            nanoOfSecond: $nanoOfSecond,
            zone: $zone,
            calendar: $this->calendar,
        );
    }

    /**
     * Parse text into a PlainDateTime
     *
     * @throws InvalidValueException
     */
    public function parseToPlainDateTime(string $text): PlainDateTime
    {
        $parsed = $this->parse($text);

        $year = $this->extractYear($parsed);
        $month = $this->extractMonth($parsed);
        $dayOfMonth = $this->extractDayOfMonth($parsed);
        $hour = $this->extractHour($parsed);
        $minute = $this->extractMinute($parsed);
        $second = $this->extractSecond($parsed);
        $nanoOfSecond = $this->extractNanoOfSecond($parsed);
        $dayOfYear = $this->extractDayOfYear($parsed);

        if (isset($parsed['era']) && $parsed['era'] === 'BC' && $year > 0) {
            $year = -$year + 1;
        }

        if (isset($parsed['amPm'])) {
            if ($parsed['amPm'] === 'PM' && $hour < 12) {
                $hour += 12;
            } elseif ($parsed['amPm'] === 'AM' && $hour === 12) {
                $hour = 0;
            }
        }

        if (isset($parsed['dayOfYear']) && !isset($parsed['month']) && !isset($parsed['dayOfMonth'])) {
            return PlainDateTime::fromYd(
                year: $year,
                dayOfYear: $dayOfYear,
                hour: $hour,
                minute: $minute,
                second: $second,
                nanoOfSecond: $nanoOfSecond,
                calendar: $this->calendar,
            );
        }

        return PlainDateTime::fromYmd(
            year: $year,
            month: $month,
            dayOfMonth: $dayOfMonth,
            hour: $hour,
            minute: $minute,
            second: $second,
            nanoOfSecond: $nanoOfSecond,
            calendar: $this->calendar,
        );
    }

    /**
     * Parse text into a PlainDate
     *
     * @throws InvalidValueException
     */
    public function parseToPlainDate(string $text): PlainDate
    {
        $parsed = $this->parse($text);

        $year = $this->extractYear($parsed);
        $month = $this->extractMonth($parsed);
        $dayOfMonth = $this->extractDayOfMonth($parsed);
        $dayOfYear = $this->extractDayOfYear($parsed);

        if (isset($parsed['era']) && $parsed['era'] === 'BC' && $year > 0) {
            $year = -$year + 1;
        }

        if (isset($parsed['dayOfYear']) && !isset($parsed['month']) && !isset($parsed['dayOfMonth'])) {
            return PlainDate::fromYd(
                year: $year,
                dayOfYear: $dayOfYear,
                calendar: $this->calendar,
            );
        }

        return PlainDate::fromYmd(
            year: $year,
            month: $month,
            dayOfMonth: $dayOfMonth,
            calendar: $this->calendar,
        );
    }

    /**
     * Parse text into a PlainTime
     *
     * @throws InvalidValueException
     */
    public function parseToPlainTime(string $text): PlainTime
    {
        $parsed = $this->parse($text);

        $hour = $this->extractHour($parsed);
        $minute = $this->extractMinute($parsed);
        $second = $this->extractSecond($parsed);
        $nanoOfSecond = $this->extractNanoOfSecond($parsed);

        if (isset($parsed['amPm'])) {
            if ($parsed['amPm'] === 'PM' && $hour < 12) {
                $hour += 12;
            } elseif ($parsed['amPm'] === 'AM' && $hour === 12) {
                $hour = 0;
            }
        }

        return PlainTime::fromHms(
            hour: $hour,
            minute: $minute,
            second: $second,
            nanoOfSecond: $nanoOfSecond,
        );
    }

    /**
     * Parse text into an Instant
     *
     * @throws InvalidValueException
     */
    public function parseToInstant(string $text, ?Zone $defaultZone = null): Instant
    {
        return $this->parseToZonedDateTime($text, $defaultZone)->instant;
    }

    /**
     * Parse text and return raw parsed values
     *
     * @return ParsedFields
     * @throws InvalidValueException
     */
    public function parse(string $text): array
    {
        $result = [];
        $pos = $this->parseTokensInto($this->formatPattern->tokens, $text, 0, $result);

        if ($pos < \strlen($text)) {
            throw new InvalidValueException("Unparsed text '{$text}' at position {$pos}");
        }

        return $result;
    }

    /**
     * @param ParsedFields $parsed
     * @return int
     */
    private function extractYear(array $parsed): int
    {
        return (int)($parsed['year'] ?? $parsed['yearOfEra'] ?? $parsed['weekBasedYear'] ?? 1970);
    }

    /**
     * @param ParsedFields $parsed
     * @return int<1,99>
     */
    private function extractMonth(array $parsed): int
    {
        $month = (int)($parsed['month'] ?? 1);
        if ($month < 1 || $month > 99) {
            throw new InvalidValueException("Invalid month value '{$month}'");
        }

        return $month;
    }

    /**
     * @param ParsedFields $parsed
     * @return int<1,31>
     */
    private function extractDayOfMonth(array $parsed): int
    {
        $dayOfMonth = (int)($parsed['dayOfMonth'] ?? 1);
        if ($dayOfMonth < 1 || $dayOfMonth > 31) {
            throw new InvalidValueException("Invalid day of month value '{$dayOfMonth}'");
        }

        return $dayOfMonth;
    }

    /**
     * @param ParsedFields $parsed
     * @return int<1,366>
     */
    private function extractDayOfYear(array $parsed): int
    {
        $dayOfYear = (int)($parsed['dayOfYear'] ?? 1);
        if ($dayOfYear < 1 || $dayOfYear > 366) {
            throw new InvalidValueException("Invalid day of year value '{$dayOfYear}'");
        }

        return $dayOfYear;
    }

    /**
     * @param ParsedFields $parsed
     * @return int<0,23>
     */
    private function extractHour(array $parsed): int
    {
        $hour = (int)($parsed['hour'] ?? 0);
        if ($hour < 0 || $hour > 23) {
            throw new InvalidValueException("Invalid hour value '{$hour}'");
        }

        return $hour;
    }

    /**
     * @param ParsedFields $parsed
     * @return int<0,59>
     */
    private function extractMinute(array $parsed): int
    {
        $minute = (int)($parsed['minute'] ?? 0);
        if ($minute < 0 || $minute > 59) {
            throw new InvalidValueException("Invalid minute value '{$minute}'");
        }

        return $minute;
    }

    /**
     * @param ParsedFields $parsed
     * @return int<0,59>
     */
    private function extractSecond(array $parsed): int
    {
        $second = (int)($parsed['second'] ?? 0);
        if ($second < 0 || $second > 59) {
            throw new InvalidValueException("Invalid second value '{$second}'");
        }

        return $second;
    }

    /**
     * @param ParsedFields $parsed
     * @return int<0,999999999>
     */
    private function extractNanoOfSecond(array $parsed): int
    {
        $nanoOfSecond = (int)($parsed['nanoOfSecond'] ?? 0);
        if ($nanoOfSecond < 0 || $nanoOfSecond > 999999999) {
            throw new InvalidValueException("Invalid nanosecond value '{$nanoOfSecond}'");
        }

        return $nanoOfSecond;
    }

    /**
     * @param list<FormatPatternToken> $tokens
     * @param ParsedFields $result
     * @param-out ParsedFields $result
     * @return int
     * @throws InvalidValueException
     */
    private function parseTokensInto(array $tokens, string $text, int $pos, array &$result): int
    {
        $textLen = \strlen($text);

        foreach ($tokens as $token) {
            if ($token['type'] === 'optional') {
                try {
                    $optionalResult = [];
                    /** @var list<FormatPatternToken> $optionalTokens */
                    $optionalTokens = $token['value'];
                    $pos = $this->parseTokensInto($optionalTokens, $text, $pos, $optionalResult);
                    $result = [...$result, ...$optionalResult];
                } catch (InvalidValueException) {
                    // skip invalid value for optional section
                }
            } elseif ($token['type'] === 'literal') {
                $literal = $token['value'];
                $literalLen = \strlen($literal);

                if (\substr($text, $pos, $literalLen) !== $literal) {
                    throw new InvalidValueException("Expected literal '{$literal}' at position {$pos} in '{$text}'");
                }

                $pos += $literalLen;
            } else {
                // token type is 'symbol'
                $pos += $this->parseSymbolInto($token['value'], $token['count'], $text, $pos, $result);
            }

            \assert($pos <= $textLen, "Unexpected end of text while parsing '{$text}' with pattern '{$this->formatPattern->pattern}'");
        }

        return $pos;
    }

    /**
     * @param ParsedFields $result
     * @param-out ParsedFields $result
     * @throws InvalidValueException
     */
    private function parseSymbolInto(FormatPatternSymbol $symbol, int $count, string $text, int $pos, array &$result): int
    {
        $consumed = 0;

        switch ($symbol) {
            // Era
            case FormatPatternSymbol::ERA:
                [$result['era'], $consumed] = $this->parseEra($count, $text, $pos);
                break;

            // Year
            case FormatPatternSymbol::YEAR:
                [$result['yearOfEra'], $consumed] = $this->parseYear($count, $text, $pos);
                break;

            case FormatPatternSymbol::WEEK_BASED_YEAR:
                [$result['weekBasedYear'], $consumed] = $this->parseYear($count, $text, $pos);
                break;

            case FormatPatternSymbol::EXTENDED_YEAR:
                [$result['year'], $consumed] = $this->parseYear($count, $text, $pos);
                break;

            case FormatPatternSymbol::RELATED_GREGORIAN_YEAR:
                // FIXME: This needs to handle Gregorian Calendar conversion into $this->calendar
                [$result['year'], $consumed] = $this->parseYear($count, $text, $pos);
                break;

            // Quarter
            case FormatPatternSymbol::QUARTER:
            case FormatPatternSymbol::QUARTER_STANDALONE:
                [$result['quarter'], $consumed] = $this->parseQuarter($count, $text, $pos);
                break;

            // Month
            case FormatPatternSymbol::MONTH:
            case FormatPatternSymbol::MONTH_STANDALONE:
                [$result['month'], $consumed] = $this->parseMonth($count, $text, $pos);
                break;

            // Week
            case FormatPatternSymbol::WEEK_OF_YEAR:
                [$result['weekOfYear'], $consumed] = $this->parseNumber($count, 2, $text, $pos);
                break;

            case FormatPatternSymbol::WEEK_OF_MONTH:
                [$result['weekOfMonth'], $consumed] = $this->parseNumber(1, 1, $text, $pos);
                break;

            // Day
            case FormatPatternSymbol::DAY_OF_MONTH:
                [$result['dayOfMonth'], $consumed] = $this->parseNumber($count, 2, $text, $pos);
                break;

            case FormatPatternSymbol::DAY_OF_YEAR:
                [$result['dayOfYear'], $consumed] = $this->parseNumber($count, 3, $text, $pos);
                break;

            case FormatPatternSymbol::DAY_OF_WEEK_IN_MONTH:
                [$result['weekOfMonth'], $consumed] = $this->parseNumber(1, 1, $text, $pos);
                break;

            case FormatPatternSymbol::MODIFIED_JULIAN_DAY:
                // Intl documents this as "modified Julian day", but this project parses it as a JDN for compatibility.
                [$result['jdn'], $consumed] = $this->parseJulianDayNumber($count, $text, $pos);
                break;

            // Weekday
            case FormatPatternSymbol::WEEKDAY:
                [$result['dayOfWeek'], $consumed] = $this->parseDayOfWeek($count, $text, $pos);
                break;

            case FormatPatternSymbol::LOCAL_DAY_OF_WEEK:
            case FormatPatternSymbol::LOCAL_DAY_OF_WEEK_STANDALONE:
                [$result['dayOfWeek'], $consumed] = $this->parseLocalizedDayOfWeek($count, $text, $pos);
                break;

            // Period
            case FormatPatternSymbol::PERIOD:
                [$result['amPm'], $consumed] = $this->parsePeriod($text, $pos);
                break;

            case FormatPatternSymbol::PERIOD_EXTENDED:
                [$result['amPm'], $consumed] = $this->parsePeriodExtended($text, $pos);
                break;

            case FormatPatternSymbol::PERIOD_FLEXIBLE:
                [$result['amPm'], $consumed] = $this->parsePeriodFlexible($text, $pos);
                break;

            // Hour
            case FormatPatternSymbol::HOUR_23:
            case FormatPatternSymbol::HOUR_11:
                [$result['hour'], $consumed] = $this->parseNumber($count, 2, $text, $pos);
                break;

            case FormatPatternSymbol::HOUR_12:
                [$result['hour'], $consumed] = $this->parseClockHour12($count, $text, $pos);
                break;

            case FormatPatternSymbol::HOUR_24:
                [$result['hour'], $consumed] = $this->parseClockHour24($count, $text, $pos);
                break;

            // Minute
            case FormatPatternSymbol::MINUTE:
                [$result['minute'], $consumed] = $this->parseNumber($count, 2, $text, $pos);
                break;

            // Second
            case FormatPatternSymbol::SECOND:
                [$result['second'], $consumed] = $this->parseNumber($count, 2, $text, $pos);
                break;

            case FormatPatternSymbol::FRACTIONAL_SECOND:
                [$result['nanoOfSecond'], $consumed] = $this->parseFraction($count, $text, $pos);
                break;

            case FormatPatternSymbol::MILLISECONDS_IN_DAY:
                [$value, $consumed] = $this->parseNumber($count, 8, $text, $pos);
                $result['hour'] = \intdiv($value, 3600000);
                $result['minute'] = \intdiv($value % 3600000, 60000);
                $result['second'] = \intdiv($value % 60000, 1000);
                $result['nanoOfSecond'] = ($value % 1000) * 1_000_000;
                break;

            // Zone
            case FormatPatternSymbol::ZONE_ID:
                [$result['zone'], $consumed] = $this->parseTimeZoneId($text, $pos);
                break;

            case FormatPatternSymbol::ZONE_NAME:
                [$result['zone'], $consumed] = $this->parseTimeZoneName($count, $text, $pos);
                break;

            case FormatPatternSymbol::ZONE_GENERIC:
                [$result['zone'], $consumed] = $this->parseZoneGeneric($count, $text, $pos);
                break;

            case FormatPatternSymbol::ZONE_OFFSET_GMT:
                [$value, $consumed] = $this->parseZoneOffsetGmt($count, $text, $pos);
                $result['zone'] = $value;
                $result['offset'] = $value;
                break;

            case FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC:
                [$value, $consumed] = $this->parseZoneOffsetIsoBasic($count, $text, $pos);
                $result['zone'] = $value;
                $result['offset'] = $value;
                break;

            case FormatPatternSymbol::ZONE_OFFSET_ISO_BASIC_LOCAL:
                [$value, $consumed] = $this->parseZoneOffset($count, $text, $pos);
                $result['zone'] = $value;
                $result['offset'] = $value;
                break;

            case FormatPatternSymbol::ZONE_OFFSET_ISO_EXTENDED:
                [$value, $consumed] = $this->parseZoneOffsetIsoExtended($count, $text, $pos);
                $result['zone'] = $value;
                $result['offset'] = $value;
                break;

            // Literals
            case FormatPatternSymbol::LITERAL_HYPHEN:
            case FormatPatternSymbol::LITERAL_COLON:
            case FormatPatternSymbol::LITERAL_COMMA:
            case FormatPatternSymbol::LITERAL_DOT:
            case FormatPatternSymbol::LITERAL_SLASH:
            case FormatPatternSymbol::LITERAL_SPACE:
            case FormatPatternSymbol::LITERAL_UNDERSCORE:
                [, $consumed] = $this->parseLiteralChar($symbol->value, $text, $pos);
                break;
        }

        return $consumed;
    }

    /**
     * @return array{null, int}
     */
    private function parseLiteralChar(string $char, string $text, int $pos): array
    {
        if (($text[$pos] ?? '') !== $char) {
            throw new InvalidValueException("Expected '{$char}' at position {$pos} in '{$text}'");
        }
        return [null, 1];
    }

    /**
     * @return array{string, int}
     */
    private function parseEra(int $count, string $text, int $pos): array
    {
        if ($count <= 3) {
            if (\strtoupper(\substr($text, $pos, 2)) === 'AD') {
                return ['AD', 2];
            }
            if (\strtoupper(\substr($text, $pos, 2)) === 'BC') {
                return ['BC', 2];
            }
        } elseif ($count === 4) {
            if (\strtolower(\substr($text, $pos, 11)) === 'anno domini') {
                return ['AD', 11];
            }
            if (\strtolower(\substr($text, $pos, 13)) === 'before christ') {
                return ['BC', 13];
            }
        } else {
            if (\strtoupper($text[$pos] ?? '') === 'A') {
                return ['AD', 1];
            }
            if (\strtoupper($text[$pos] ?? '') === 'B') {
                return ['BC', 1];
            }
        }

        throw new InvalidValueException("Unable to parse era at position {$pos} in '{$text}'");
    }

    /**
     * @return array{int, int}
     */
    private function parseYear(int $count, string $text, int $pos): array
    {
        $negative = false;
        $consumed = 0;

        if (($text[$pos] ?? '') === '-') {
            $negative = true;
            $pos++;
            $consumed++;
        } elseif (($text[$pos] ?? '') === '+') {
            $pos++;
            $consumed++;
        }

        if ($count === 2) {
            $match = '';
            for ($i = 0; $i < 2 && ($pos + $i) < \strlen($text) && \ctype_digit($text[$pos + $i]); $i++) {
                $match .= $text[$pos + $i];
            }

            if (\strlen($match) < 2) {
                throw new InvalidValueException("Unable to parse 2-digit year at position {$pos} in '{$text}'");
            }

            $year = (int)$match;
            $year = $year + 2000;
            if ($year >= 2100) {
                $year -= 100;
            }

            return [$negative ? -$year : $year, $consumed + 2];
        }

        $match = '';
        $maxDigits = \max($count, 10);
        for ($i = 0; $i < $maxDigits && ($pos + $i) < \strlen($text) && \ctype_digit($text[$pos + $i]); $i++) {
            $match .= $text[$pos + $i];
        }

        if (\strlen($match) < $count) {
            throw new InvalidValueException("Unable to parse year with {$count} digits at position {$pos} in '{$text}'");
        }

        $year = (int)$match;
        return [$negative ? -$year : $year, $consumed + \strlen($match)];
    }

    /**
     * @return array{int, int}
     */
    private function parseNumber(int $minDigits, int $maxDigits, string $text, int $pos): array
    {
        $match = '';
        for ($i = 0; $i < $maxDigits && ($pos + $i) < \strlen($text) && \ctype_digit($text[$pos + $i]); $i++) {
            $match .= $text[$pos + $i];
        }

        if (\strlen($match) < $minDigits) {
            throw new InvalidValueException("Unable to parse number with at least {$minDigits} digits at position {$pos} in '{$text}'");
        }

        return [(int)$match, \strlen($match)];
    }

    /**
     * @return list<array{month: int<1,max>, consumed: int<1,max>}>
     */
    private function englishMonthPrefixMatches(string $text, int $patternWidth): array
    {
        $refYear = 1972;
        $cal     = $this->calendar;
        $map     = match (true) {
            $patternWidth === 3 => $cal->getMonthAbbreviationMap($refYear),
            $patternWidth === 4 => $cal->getMonthNameMap($refYear),
            default => $cal->getMonthNarrowMap($refYear),
        };

        $matches = [];
        foreach ($map as $month => $label) {
            $label = strtolower($label);
            if (\str_starts_with($text, $label)) {
                $matches[] = ['month' => $month, 'consumed' => \strlen($label)];
            }
        }

        return $matches;
    }

    /**
     * @return list<array{dayOfWeek: int<1,max>, consumed: int<1,max>}>
     */
    private function englishDayOfWeekPrefixMatches(string $text, int $patternWidth): array
    {
        $cal = $this->calendar;
        $map = match (true) {
            $patternWidth <= 3 => $cal->getDayOfWeekAbbreviationMap(),
            $patternWidth === 4 => $cal->getDayOfWeekNameMap(),
            $patternWidth === 5 => $cal->getDayOfWeekNarrowMap(),
            default => $cal->getDayOfWeekShortMap(),
        };

        $matches = [];
        foreach ($map as $dayOfWeek => $label) {
            $label = strtolower($label);
            if (\str_starts_with($text, $label)) {
                $matches[] = ['dayOfWeek' => $dayOfWeek, 'consumed' => \strlen($label)];
            }
        }

        return $matches;
    }

    /**
     * @return array{int, int}
     */
    private function parseMonth(int $count, string $text, int $pos): array
    {
        if ($count <= 2) {
            return $this->parseNumber($count, 2, $text, $pos);
        }

        $remaining = \strtolower(\substr($text, $pos));

        $matches = $this->englishMonthPrefixMatches($remaining, $count);
        if ($matches !== []) {
            return [$matches[0]['month'], $matches[0]['consumed']];
        }

        throw new InvalidValueException("Unable to parse month at position {$pos} in '{$text}'");
    }

    /**
     * @return array{int, int}
     */
    private function parseQuarter(int $count, string $text, int $pos): array
    {
        if ($count <= 2) {
            return $this->parseNumber($count, 2, $text, $pos);
        }

        $remaining = \strtolower(\substr($text, $pos));

        if ($count === 3) {
            if (\preg_match('/^q([1-4])/i', $remaining, $m)) {
                return [(int)$m[1], 2];
            }
        } else {
            $quarters = ['1st quarter' => 1, '2nd quarter' => 2, '3rd quarter' => 3, '4th quarter' => 4];
            foreach ($quarters as $name => $num) {
                if (\str_starts_with($remaining, $name)) {
                    return [$num, \strlen($name)];
                }
            }
        }

        throw new InvalidValueException("Unable to parse quarter at position {$pos} in '{$text}'");
    }

    /**
     * @return array{int, int}
     */
    private function parseDayOfWeek(int $count, string $text, int $pos): array
    {
        $remaining = \strtolower(\substr($text, $pos));

        $matches = $this->englishDayOfWeekPrefixMatches($remaining, $count);
        if ($matches !== []) {
            return [$matches[0]['dayOfWeek'], $matches[0]['consumed']];
        }

        throw new InvalidValueException("Unable to parse day of week at position {$pos} in '{$text}'");
    }

    /**
     * @return array{int, int}
     */
    private function parseLocalizedDayOfWeek(int $count, string $text, int $pos): array
    {
        if ($count <= 2) {
            return $this->parseNumber($count, 2, $text, $pos);
        }

        return $this->parseDayOfWeek($count, $text, $pos);
    }

    /**
     * @return array{int, int}
     */
    private function parseJulianDayNumber(int $count, string $text, int $pos): array
    {
        return $this->parseNumber($count, 10, $text, $pos);
    }

    /**
     * @return array{string, int}
     */
    private function parsePeriod(string $text, int $pos): array
    {
        $upper = \strtoupper(\substr($text, $pos, 2));

        if ($upper === 'AM') {
            return ['AM', 2];
        }
        if ($upper === 'PM') {
            return ['PM', 2];
        }

        throw new InvalidValueException("Unable to parse AM/PM at position {$pos} in '{$text}'");
    }

    /**
     * @return array{string, int}
     */
    private function parsePeriodExtended(string $text, int $pos): array
    {
        $remaining = \strtolower(\substr($text, $pos));

        if (\str_starts_with($remaining, 'midnight')) {
            return ['AM', 8];
        }
        if (\str_starts_with($remaining, 'noon')) {
            return ['PM', 4];
        }
        if (\str_starts_with($remaining, 'mi')) {
            return ['AM', 2];
        }
        if (\str_starts_with($remaining, 'n')) {
            return ['PM', 1];
        }

        return $this->parsePeriod($text, $pos);
    }

    /**
     * @return array{string, int}
     */
    private function parsePeriodFlexible(string $text, int $pos): array
    {
        $remaining = \strtolower(\substr($text, $pos));

        if (\str_starts_with($remaining, 'at night')) {
            return ['AM', 8];
        }
        if (\str_starts_with($remaining, 'in the morning')) {
            return ['AM', 14];
        }
        if (\str_starts_with($remaining, 'in the afternoon')) {
            return ['PM', 16];
        }
        if (\str_starts_with($remaining, 'in the evening')) {
            return ['PM', 14];
        }

        throw new InvalidValueException("Unable to parse flexible day period at position {$pos} in '{$text}'");
    }

    /**
     * @return array{int, int}
     */
    private function parseClockHour12(int $count, string $text, int $pos): array
    {
        [$hour, $consumed] = $this->parseNumber($count, 2, $text, $pos);
        if ($hour === 12) {
            $hour = 0;
        }
        return [$hour, $consumed];
    }

    /**
     * @return array{int, int}
     */
    private function parseClockHour24(int $count, string $text, int $pos): array
    {
        [$hour, $consumed] = $this->parseNumber($count, 2, $text, $pos);
        if ($hour === 24) {
            $hour = 0;
        }
        return [$hour, $consumed];
    }

    /**
     * @return array{int, int}
     */
    private function parseFraction(int $count, string $text, int $pos): array
    {
        $match = '';
        for ($i = 0; $i < 9 && ($pos + $i) < \strlen($text) && \ctype_digit($text[$pos + $i]); $i++) {
            $match .= $text[$pos + $i];
        }

        if (\strlen($match) < $count) {
            throw new InvalidValueException("Unable to parse fraction with {$count} digits at position {$pos} in '{$text}'");
        }

        $nanos = \str_pad($match, 9, '0', STR_PAD_RIGHT);
        return [(int)$nanos, \strlen($match)];
    }

    /**
     * @return array{Zone, int}
     */
    private function parseTimeZoneId(string $text, int $pos): array
    {
        if (\substr($text, $pos, 1) === 'Z') {
            return [new ZoneOffset(0), 1];
        }

        $remaining = \substr($text, $pos);

        if (\preg_match('/^([A-Za-z_]+\/[A-Za-z_\/]+)/', $remaining, $m)) {
            try {
                $zone = Zone::fromIdentifier($m[1]);
                return [$zone, \strlen($m[1])];
            } catch (\Throwable) {
            }
        }

        if (\preg_match('/^([+-])(\d{2}):(\d{2})(?::(\d{2}))?/', $remaining, $m)) {
            $totalSeconds = ((int)$m[2]) * 3600 + ((int)$m[3]) * 60 + ((int)($m[4] ?? 0));
            if ($m[1] === '-') {
                $totalSeconds = -$totalSeconds;
            }
            return [new ZoneOffset($totalSeconds), \strlen($m[0])];
        }

        throw new InvalidValueException("Unable to parse time zone ID at position {$pos} in '{$text}'");
    }

    /**
     * @return array{Zone, int}
     */
    private function parseTimeZoneName(int $count, string $text, int $pos): array
    {
        $remaining = \substr($text, $pos);

        if (\preg_match('/^GMT([+-])(\d{1,2})(?::(\d{2})(?::(\d{2}))?)?/', $remaining, $m)) {
            $totalSeconds = ((int)$m[2]) * 3600 + ((int)($m[3] ?? 0)) * 60 + ((int)($m[4] ?? 0));
            if ($m[1] === '-') {
                $totalSeconds = -$totalSeconds;
            }
            return [new ZoneOffset($totalSeconds), \strlen($m[0])];
        }

        if (\str_starts_with($remaining, 'GMT')) {
            return [new ZoneOffset(0), 3];
        }

        $abbrs = ZoneAbbreviation::findZoneByAbbreviation($remaining);
        if ($abbrs !== null) {
            foreach ($abbrs as $abbr => $zoneId) {
                return [Zone::fromIdentifier($zoneId), \strlen($abbr)];
            }
        }

        if (\preg_match('/^([A-Z]{2,5})/', $remaining, $m)) {
            return [new ZoneOffset(0), \strlen($m[1])];
        }

        throw new InvalidValueException("Unable to parse time zone name at position {$pos} in '{$text}'");
    }

    /**
     * @return array{Zone, int}
     */
    private function parseZoneGeneric(int $count, string $text, int $pos): array
    {
        $remaining = \substr($text, $pos);

        if (\preg_match('/^([A-Za-z_]+\/[A-Za-z_\/]+)/', $remaining, $m)) {
            try {
                $zone = Zone::fromIdentifier($m[1]);
                return [$zone, \strlen($m[1])];
            } catch (\Throwable) {
            }
        }

        $abbrs = ZoneAbbreviation::findZoneByAbbreviation($remaining);
        if ($abbrs !== null) {
            foreach ($abbrs as $abbr => $zoneId) {
                return [Zone::fromIdentifier($zoneId), \strlen($abbr)];
            }
        }

        throw new InvalidValueException("Unable to parse generic time zone at position {$pos} in '{$text}'");
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseZoneOffsetGmt(int $count, string $text, int $pos): array
    {
        $remaining = \substr($text, $pos);

        if (\str_starts_with($remaining, 'GMT')) {
            $offset = \substr($remaining, 3);

            if (\preg_match('/^([+-])(\d{1,2})(?::(\d{2})(?::(\d{2}))?)?/', $offset, $m)) {
                $totalSeconds = ((int)$m[2]) * 3600 + ((int)($m[3] ?? 0)) * 60 + ((int)($m[4] ?? 0));
                if ($m[1] === '-') {
                    $totalSeconds = -$totalSeconds;
                }
                return [new ZoneOffset($totalSeconds), 3 + \strlen($m[0])];
            }

            return [new ZoneOffset(0), 3];
        }

        throw new InvalidValueException("Unable to parse localized zone offset at position {$pos} in '{$text}'");
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseZoneOffsetIsoBasic(int $count, string $text, int $pos): array
    {
        if (($text[$pos] ?? '') === 'Z') {
            return [new ZoneOffset(0), 1];
        }

        return $this->parseZoneOffset($count, $text, $pos);
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseZoneOffset(int $count, string $text, int $pos): array
    {
        $remaining = \substr($text, $pos);

        $sign = 1;
        if (\str_starts_with($remaining, '+')) {
            $remaining = \substr($remaining, 1);
            $consumed = 1;
        } elseif (\str_starts_with($remaining, '-')) {
            $sign = -1;
            $remaining = \substr($remaining, 1);
            $consumed = 1;
        } else {
            throw new InvalidValueException("Unable to parse zone offset at position {$pos} in '{$text}'");
        }

        return match ($count) {
            1 => $this->parseOffsetHourOrHourMinute($remaining, $consumed, $sign, $pos, $text),
            2 => $this->parseOffsetHourMinute($remaining, $consumed, $sign, false, $pos, $text),
            3 => $this->parseOffsetHourMinute($remaining, $consumed, $sign, true, $pos, $text),
            4 => $this->parseOffsetHourMinuteSecond($remaining, $consumed, $sign, false, $pos, $text),
            default => $this->parseOffsetHourMinuteSecond($remaining, $consumed, $sign, true, $pos, $text),
        };
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseOffsetHourOrHourMinute(string $remaining, int $consumed, int $sign, int $pos, string $text): array
    {
        if (\preg_match('/^(\d{2})(\d{2})/', $remaining, $m)) {
            $totalSeconds = ((int)$m[1]) * 3600 + ((int)$m[2]) * 60;
            return [new ZoneOffset($sign * $totalSeconds), $consumed + 4];
        }

        if (\preg_match('/^(\d{2})/', $remaining, $m)) {
            $totalSeconds = ((int)$m[1]) * 3600;
            return [new ZoneOffset($sign * $totalSeconds), $consumed + 2];
        }

        throw new InvalidValueException("Unable to parse zone offset at position {$pos} in '{$text}'");
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseOffsetHourMinute(string $remaining, int $consumed, int $sign, bool $withColon, int $pos, string $text): array
    {
        $pattern = $withColon ? '/^(\d{2}):(\d{2})/' : '/^(\d{2})(\d{2})/';

        if (\preg_match($pattern, $remaining, $m)) {
            $totalSeconds = ((int)$m[1]) * 3600 + ((int)$m[2]) * 60;
            return [new ZoneOffset($sign * $totalSeconds), $consumed + \strlen($m[0])];
        }

        throw new InvalidValueException("Unable to parse zone offset at position {$pos} in '{$text}'");
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseOffsetHourMinuteSecond(string $remaining, int $consumed, int $sign, bool $withColon, int $pos, string $text): array
    {
        $sep = $withColon ? ':' : '';
        $patternWithSeconds = '/^(\d{2})' . $sep . '(\d{2})' . $sep . '(\d{2})/';
        $patternNoSeconds = '/^(\d{2})' . $sep . '(\d{2})/';

        if (\preg_match($patternWithSeconds, $remaining, $m)) {
            $totalSeconds = ((int)$m[1]) * 3600 + ((int)$m[2]) * 60 + ((int)$m[3]);
            return [new ZoneOffset($sign * $totalSeconds), $consumed + \strlen($m[0])];
        }

        if (\preg_match($patternNoSeconds, $remaining, $m)) {
            $totalSeconds = ((int)$m[1]) * 3600 + ((int)$m[2]) * 60;
            return [new ZoneOffset($sign * $totalSeconds), $consumed + \strlen($m[0])];
        }

        throw new InvalidValueException("Unable to parse zone offset at position {$pos} in '{$text}'");
    }

    /**
     * @return array{ZoneOffset, int}
     */
    private function parseZoneOffsetIsoExtended(int $count, string $text, int $pos): array
    {
        $remaining = \substr($text, $pos);

        if ($count === 5 && \str_starts_with($remaining, 'Z')) {
            return [new ZoneOffset(0), 1];
        }

        if ($count === 4) {
            return $this->parseZoneOffsetGmt($count, $text, $pos);
        }

        $sign = 1;
        if (\str_starts_with($remaining, '+')) {
            $remaining = \substr($remaining, 1);
            $consumed = 1;
        } elseif (\str_starts_with($remaining, '-')) {
            $sign = -1;
            $remaining = \substr($remaining, 1);
            $consumed = 1;
        } else {
            throw new InvalidValueException("Unable to parse zone offset Z at position {$pos} in '{$text}'");
        }

        if ($count <= 3) {
            if (\preg_match('/^(\d{2})(\d{2})/', $remaining, $m)) {
                $totalSeconds = ((int)$m[1]) * 3600 + ((int)$m[2]) * 60;
                return [new ZoneOffset($sign * $totalSeconds), $consumed + 4];
            }
        } else {
            if (\preg_match('/^(\d{2}):(\d{2})(?::(\d{2}))?/', $remaining, $m)) {
                $totalSeconds = ((int)$m[1]) * 3600 + ((int)$m[2]) * 60 + ((int)($m[3] ?? 0));
                return [new ZoneOffset($sign * $totalSeconds), $consumed + \strlen($m[0])];
            }
        }

        throw new InvalidValueException("Unable to parse zone offset Z at position {$pos} in '{$text}'");
    }
}
