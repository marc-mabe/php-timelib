<?php declare(strict_types=1);

namespace time;

interface Calendar
{
    /**
     * Calculates if the given year is a leap year.
     *
     * A leap year differs in the number of days in a year.
     * Depending on the calendar, it may differ in the number of days in a specific month of the year
     * or in the number of months of the year.
     */
    public function isLeapYear(int $year): bool;

    /**
     * Calculates the number of days in the given year.
     */
    public function getDaysInYear(int $year): int;

    /**
     * Indicates whether the calendar defines a year zero.
     */
    public function hasYearZero(): bool;

    /**
     * Calculates the number of days in the given month.
     *
     * @param int<1,99> $month
     */
    public function getDaysInMonth(int $year, int $month): int;

    /**
     * Calculates the number of months in the given year.
     *
     * @return int<1,99>
     */
    public function getMonthsInYear(int $year): int;

    /**
     * Returns the english name of the given month.
     *
     * @param int<1,99> $month
     * @return non-empty-string
     */
    public function getMonthName(int $year, int $month): string;

    /**
     * Returns the english abbreviation of the given month.
     *
     * @param int<1,99> $month
     * @return non-empty-string
     */
    public function getMonthAbbreviation(int $year, int $month): string;

    /**
     * Returns a narrow english month label (CLDR-style; letters may repeat).
     *
     * @param int<1,99> $month
     * @return non-empty-string
     */
    public function getMonthNarrow(int $year, int $month): string;

    /**
     * Calculates the year, month and day of month from the given number of days since unix epoch.
     *
     * @return array{int, int<1,99>, int<1,31>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array;

    /**
     * Calculates the number of days since 1970-01-01 for the given year, month and day of month.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * @param int $year
     * @param int<1,366> $dayOfYear
     * @return int
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int;

    /**
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,366>
     */
    public function getDayOfYearByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the number of days in the week of the given date.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,max>
     */
    public function getDaysInWeekByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the week-of-year of the given date.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,max>
     */
    public function getWeekOfYearByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the week-of-month of the given date.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int<0,max>
     */
    public function getWeekOfMonthByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the year-of-week of the given date.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getYearOfWeekByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the day-of-week of the given date.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,max>
     */
    public function getDayOfWeekByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Returns the english name of the given day-of-week.
     *
     * @param int<1,max> $dayOfWeek
     * @return non-empty-string
     */
    public function getDayOfWeekName(int $dayOfWeek): string;

    /**
     * Returns the english abbreviation of the given day-of-week.
     *
     * @param int<1,max> $dayOfWeek
     * @return non-empty-string
     */
    public function getDayOfWeekAbbreviation(int $dayOfWeek): string;

    /**
     * Returns a short english weekday label (e.g. Mo, Tu).
     *
     * @param int<1,max> $dayOfWeek
     * @return non-empty-string
     */
    public function getDayOfWeekShort(int $dayOfWeek): string;

    /**
     * Returns a narrow single-letter weekday label (CLDR-style; not unique).
     *
     * @param int<1,max> $dayOfWeek
     * @return non-empty-string
     */
    public function getDayOfWeekNarrow(int $dayOfWeek): string;

    /**
     * Calculates the day of week from the given days since unix epoch.
     *
     * @return int<1,max>
     */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int;

    /**
     * Converts a Julian day number into a date [year, month, dayOfMonth].
     *
     * Calendar APIs are date-based (integer days at midnight), while
     * Julian day numbers are traditionally anchored at noon. This conversion
     * follows the project convention used by this interface's existing JDN methods,
     * where a calendar date corresponds to the same integer boundary expected by
     * {@see Calendar::getJdnByYmd()}.
     *
     * @return array{int, int<1,99>, int<1,31>}
     */
    public function getYmdByJdn(int|float $julianDay): array;

    /**
     * Converts the date [year, month, dayOfMonth] into a Julian day number.
     *
     * The returned value follows this implementation's calendar-day convention:
     * it represents the calendar day boundary with the astronomical noon
     * convention shifted by 0.5 (i.e. midnight aligns to the "JDN-0.5" boundary).
     * For example, 1970-01-01 maps to 2440588 in this API.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int
     */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Converts a date [year, month, dayOfMonth] into the real modified Julian day.
     *
     * The real modified Julian day is `JD - 2400000.5`, which means that
     * midnight corresponds to an integer value and noon to the next half step.
     * This method follows that convention, so midnight for 1970-01-01 returns 40587.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getMjdByYmd(int $year, int $month, int $dayOfMonth): float;

    /**
     * Converts a real modified Julian day into a date [year, month, dayOfMonth].
     *
     * A real MJD value is accepted here; when a fractional value is supplied,
     * the underlying date conversion works from the represented boundary and treats
     * the value as date-focused input.
     *
     * @return array{int, int<1,99>, int<1,31>}
     */
    public function getYmdByMjd(int|float $modifiedJulianDay): array;

    /**
     * Full month names (wide style) keyed by calendar month index (1 … getMonthsInYear).
     * `referenceYear` selects a year for calendars where month metadata can depend on year.
     *
     * @return array<int<1,max>, non-empty-string>
     */
    public function getMonthNameMap(int $referenceYear): array;

    /**
     * @return array<int<1,max>, non-empty-string>
     */
    public function getMonthAbbreviationMap(int $referenceYear): array;

    /**
     * @return array<int<1,max>, non-empty-string>
     */
    public function getMonthNarrowMap(int $referenceYear): array;

    /**
     * Weekday abbreviations keyed by this calendar’s local day-of-week index (same as {@see getDayOfWeekAbbreviation}).
     *
     * @return array<int<1,max>, non-empty-string>
     */
    public function getDayOfWeekAbbreviationMap(): array;

    /**
     * @return array<int<1,max>, non-empty-string>
     */
    public function getDayOfWeekNameMap(): array;

    /**
     * @return array<int<1,max>, non-empty-string>
     */
    public function getDayOfWeekNarrowMap(): array;

    /**
     * @return array<int<1,max>, non-empty-string>
     */
    public function getDayOfWeekShortMap(): array;

    /**
     * Adds the given period to the given calendar date field values.
     * 
     * @param \time\Period $period
     * @param int $year
     * @param int $month
     * @param int $dayOfMonth
     * @return array{int, int<1,99>, int<1,31>}
     */
    public function addPeriodToYmd(
        Period $period,
        int $year,
        int $month,
        int $dayOfMonth,
    ): array;
}
