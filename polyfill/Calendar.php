<?php declare(strict_types=1);

namespace time;

interface Calendar
{
    public function isLeapYear(int $year): bool;

    public function getDaysInYear(int $year): int;

    /** @param int<1,99> $month */
    public function getDaysInMonth(int $year, int $month): int;

    /** @return int<1,99> */
    public function getMonthsInYear(int $year): int;

    /**
     * @param int<1,99> $month
     * @return non-empty-string
     */
    public function getNameOfMonth(int $month): string;

    /**
     * @param int<1,99> $month
     * @return non-empty-string
     */
    public function getAbbreviationOfMonth(int $month): string;

    /**
     * Calculates the year, month and day of month from the given number of days since unix epoch.
     *
     * @return array{int, int<1,99>, int<1,31>}
     */
    public function getYmdByDaysSinceUnixEpoch(int $days): array;

    /**
     * Calculates the year, month and day of month from the given unix timestamp (in seconds).
     *
     * @return array{int, int<1,99>, int<1, 31>}
     */
    public function getYmdByUnixTimestamp(int $ts): array;

    /**
     * Calculates the number of days since 1970-01-01 for the given year, month and day of month.
     *
     * @param int $year
     * @param int<1,99> $month
     * @param int<1, 31> $dayOfMonth
     * @return int
     */
    public function getDaysSinceUnixEpochByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the number of seconds since 1970-01-01 00:00:00 UTC for the given year, month and day of month.
     *
     * @param int $year
     * @param int<1,99> $month
     * @param int<1, 31> $dayOfMonth
     * @return int
     */
    public function getUnixTimestampByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * @param int $year
     * @param int<1, 366> $dayOfYear
     * @return int
     */
    public function getDaysSinceUnixEpochByYd(int $year, int $dayOfYear): int;

    /**
     * @param int $year
     * @param int<1, 366> $dayOfYear
     * @return int
     */
    public function getUnixTimestampByYd(int $year, int $dayOfYear): int;

    /**
     * @param int<1,99> $month
     * @param int<1,31> $dayOfMonth
     * @return int<1,366>
     */
    public function getDayOfYearByYmd(int $year, int $month, int $dayOfMonth): int;

    /**
     * Calculates the day of week from the given days since unix epoch.
     *
     * @return DayOfWeek
     */
    public function getDayOfWeekByDaysSinceUnixEpoch(int $days): DayOfWeek;

    /**
     * Calculates the day of week from the given unix timestamp (in seconds).
     *
     * @return DayOfWeek
     */
    public function getDayOfWeekByUnixTimestamp(int $ts): DayOfWeek;
}
