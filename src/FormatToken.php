<?php

namespace dt;

enum FormatToken:string
{
    // Day
    case DayOfMonth = 'j';
    case DayOfMonthWithLeadingZeros = 'd';
    case DayOfMonthOrdinalSuffix = 'S';
    case DayOfYear = 'z';
    case DayOfWeekName3Letter = 'D';
    case DayOfWeekName = 'l';
    case DayOfWeekNumber = 'w';
    case DayOfWeekNumberIso = 'N';

    // Year
    case IsLeapYear = 'L';
    case Year = 'Y';
    case Year2Digit = 'y';
    case YearExtended = 'X';
    case YearExtendedPlus = 'x';
    case YearOfWeekIso = 'o';

    // Week
    case WeekOfYearIso = 'W';

    // Month
    case MonthName = 'F';
    case MonthName3Letter = 'M';
    case MonthNumber = 'n';
    case MonthNumberWithLeadingZeros = 'm';
    case DaysInMonth = 't';

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
