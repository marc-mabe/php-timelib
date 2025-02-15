<?php declare(strict_types=1);

namespace time;

enum FormatToken:string
{
    // Year
    case IsLeapYear = 'L';
    case Year = 'Y';
    case Year2Digit = 'y';
    case YearExtended = 'x';
    case YearExtendedSign = 'X';
    case DayOfYear = 'z';

    // Week
    case WeekOfYearIso = 'W';
    case YearOfWeekIso = 'o';
    case DayOfWeekName = 'l';
    case DayOfWeekName3Letter = 'D';
    case DayOfWeekNumber = 'w';
    case DayOfWeekNumberIso = 'N';

    // Month
    case MonthName = 'F';
    case MonthAbbreviation = 'M';
    case Month = 'n';
    case MonthWithLeadingZeros = 'm';
    case DaysInMonth = 't';
    case DayOfMonth = 'j';
    case DayOfMonthWithLeadingZeros = 'd';
    case DayOfMonthOrdinalSuffix = 'S';

    // Time
    case MeridiemAbbrLower = 'a';
    case MeridiemAbbrUpper = 'A';
    // case SwatchInternetTime = 'B';

    case Hour12 = 'g';
    case Hour12WithLeadingZeros = 'G';
    case Hour24 = 'h';
    case Hour24WithLeadingZeros = 'H';

    case MinuteWithLeadingZeros = 'i';
    case SecondWithLeadingZeros = 's';
    case SecondsSinceUnixEpoch = 'U';

    case MilliOfSecond = 'v';
    case MicroOfSecond = 'u';
    case OptionalFractionOfSecondWithLeadingSeparator = 'f';

    // Time zone and offset
    case TimezoneIdentifier = 'e';
    case IsDaylightSavingTime = 'I';
    case OffsetWithoutColon = 'O';
    case OffsetWithColon = 'P';
    case OffsetWithColonOrZ = 'p';
    case TimezoneAbbrOrOffset = 'T';
    case OffsetInSeconds = 'Z';

    // Other
    case ESCAPE = '\\';
}
