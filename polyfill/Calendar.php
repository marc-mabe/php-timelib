<?php declare(strict_types=1);

namespace time;

interface Calendar
{
    /**
     * Calculates if the given year is a leap year.
     *
     * A leap year differs in the number of days in a year.
     * Depending on the calendar it may differ in the number of days in a specific month of the year
     * or in the number of months of the year.
     */
    public function isLeapYear(int $year): bool;

    /**
     * Calculates the number of days in the given year.
     */
    public function getDaysInYear(int $year): int;

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
     * Calculates the day of week from the given days since unix epoch.
     *
     * @return int<1,max>
     */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): int;

    /**
     * Converts a julian day number into a date [year, month, dayOfMonth].
     *
     * @return array{int, int<1,99>, int<1,31>}
     */
    public function getYmdByJdn(int|float $julianDay): array;

    /**
     * Converts the date [year, month, dayOfMonth] into a julian day number.
     *
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     */
    public function getJdnByYmd(int $year, int $month, int $dayOfMonth): int;

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
