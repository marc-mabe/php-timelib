<?php declare(strict_types=1);

namespace time;

/**
 * Date/time format pattern symbols based on Unicode CLDR Date Field Symbol Table.
 * @see https://www.unicode.org/reports/tr35/tr35-dates.html#Date_Field_Symbol_Table
 */
enum FormatPatternSymbol: string
{
    // Era
    case ERA = 'G';

    // Year
    case YEAR = 'y';
    case WEEK_BASED_YEAR = 'Y';
    case EXTENDED_YEAR = 'u';
    case RELATED_GREGORIAN_YEAR = 'r';

    // Quarter
    case QUARTER = 'Q';
    case QUARTER_STANDALONE = 'q';

    // Month
    case MONTH = 'M';
    case MONTH_STANDALONE = 'L';

    // Week
    case WEEK_OF_YEAR = 'w';
    case WEEK_OF_MONTH = 'W';

    // Day
    case DAY_OF_MONTH = 'd';
    case DAY_OF_YEAR = 'D';
    case DAY_OF_WEEK_IN_MONTH = 'F';
    case MODIFIED_JULIAN_DAY = 'g';

    // Weekday
    case WEEKDAY = 'E';
    case LOCAL_DAY_OF_WEEK = 'e';
    case LOCAL_DAY_OF_WEEK_STANDALONE = 'c';

    // Period (AM/PM)
    case PERIOD = 'a';
    case PERIOD_EXTENDED = 'b';
    case PERIOD_FLEXIBLE = 'B';

    // Hour
    case HOUR_12 = 'h';
    case HOUR_23 = 'H';
    case HOUR_11 = 'K';
    case HOUR_24 = 'k';

    // Minute
    case MINUTE = 'm';

    // Second
    case SECOND = 's';
    case FRACTIONAL_SECOND = 'S';
    case MILLISECONDS_IN_DAY = 'A';

    // Zone
    case ZONE_NAME = 'z';
    case ZONE_GENERIC = 'v';
    case ZONE_OFFSET_GMT = 'O';
    case ZONE_OFFSET_ISO_EXTENDED = 'Z';
    case ZONE_OFFSET_ISO_BASIC = 'X';
    case ZONE_OFFSET_ISO_BASIC_LOCAL = 'x';
    case ZONE_ID = 'V';

    // Literal symbols (self-representing in patterns without quoting)
    case LITERAL_HYPHEN = '-';
    case LITERAL_COLON = ':';
    case LITERAL_DOT = '.';
    case LITERAL_SLASH = '/';
    case LITERAL_SPACE = ' ';
    case LITERAL_UNDERSCORE = '_';
    case LITERAL_COMMA = ',';

    public function getDescription(): string
    {
        return match ($this) {
            // Era
            self::ERA => 'era',

            // Year
            self::YEAR => 'calendar year',
            self::WEEK_BASED_YEAR => 'week-based year',
            self::EXTENDED_YEAR => 'extended year',
            self::RELATED_GREGORIAN_YEAR => 'related Gregorian year',

            // Quarter
            self::QUARTER => 'quarter',
            self::QUARTER_STANDALONE => 'quarter (stand-alone)',

            // Month
            self::MONTH => 'month',
            self::MONTH_STANDALONE => 'month (stand-alone)',

            // Week
            self::WEEK_OF_YEAR => 'week of year',
            self::WEEK_OF_MONTH => 'week of month',

            // Day
            self::DAY_OF_MONTH => 'day of month',
            self::DAY_OF_YEAR => 'day of year',
            self::DAY_OF_WEEK_IN_MONTH => 'day of week in month',
            self::MODIFIED_JULIAN_DAY => 'modified Julian day',

            // Weekday
            self::WEEKDAY => 'weekday',
            self::LOCAL_DAY_OF_WEEK => 'local day of week',
            self::LOCAL_DAY_OF_WEEK_STANDALONE => 'local day of week (stand-alone)',

            // Period
            self::PERIOD => 'AM/PM',
            self::PERIOD_EXTENDED => 'AM/PM/noon/midnight',
            self::PERIOD_FLEXIBLE => 'flexible day period',

            // Hour
            self::HOUR_12 => 'hour (1-12)',
            self::HOUR_23 => 'hour (0-23)',
            self::HOUR_11 => 'hour (0-11)',
            self::HOUR_24 => 'hour (1-24)',

            // Minute
            self::MINUTE => 'minute',

            // Second
            self::SECOND => 'second',
            self::FRACTIONAL_SECOND => 'fractional second',
            self::MILLISECONDS_IN_DAY => 'milliseconds in day',

            // Zone
            self::ZONE_NAME => 'time zone name',
            self::ZONE_GENERIC => 'generic time zone',
            self::ZONE_OFFSET_GMT => 'localized GMT offset',
            self::ZONE_OFFSET_ISO_EXTENDED => 'ISO 8601 zone offset',
            self::ZONE_OFFSET_ISO_BASIC => 'ISO 8601 basic zone offset (Z for zero)',
            self::ZONE_OFFSET_ISO_BASIC_LOCAL => 'ISO 8601 basic zone offset (local)',
            self::ZONE_ID => 'time zone ID',

            // Literals
            self::LITERAL_HYPHEN => 'literal hyphen',
            self::LITERAL_COLON => 'literal colon',
            self::LITERAL_DOT => 'literal dot',
            self::LITERAL_SLASH => 'literal slash',
            self::LITERAL_SPACE => 'literal space',
            self::LITERAL_UNDERSCORE => 'literal underscore',
            self::LITERAL_COMMA => 'literal comma',
        };
    }
}
