<?php declare(strict_types=1);

/**
 * Extracts the unix timestamp for the given Date/Time object.
 *
 * @param time\Date|time\Time $dateTime
 * @return int|float
 */
function timestampForIntl(time\Date|time\Time $dateTime): int|float
{
    if ($dateTime instanceof time\Instanted) {
        try {
            return $dateTime->toUnixTimestampTuple()[0];
        } catch (Throwable) {
            // Fallback to calendar-based conversion for non-32-bit-safe inputs.
        }
    }

    $timestamp = 0;

    if ($dateTime instanceof time\Date) {
        $days = $dateTime->calendar->getDaysSinceUnixEpochByYmd($dateTime->year, $dateTime->month, $dateTime->dayOfMonth);
        $timestamp += $days * 86400;
    }

    if ($dateTime instanceof time\Time) {
        $timestamp += ($dateTime->hour * 3600) + ($dateTime->minute * 60) + $dateTime->second;
    }

    if ($dateTime instanceof time\ZonedDateTime) {
        $timestamp -= $dateTime->offset->totalSeconds;
    }

    return $timestamp;
}

/**
 * Extracts the IntlTimeZone for the given Zone|Zoned object.
 * Use UTC for objects without timezone information.
 *
 * @param time\Zone|time\Zoned|time\Date|time\Time $date
 * @return IntlTimeZone
 */
function timezoneForIntl(time\Zone|time\Zoned|time\Date|time\Time $date): IntlTimeZone
{
    $tzIdentifier = match (true) {
        $date instanceof time\Zoned => $date->zone->identifier,
        $date instanceof time\Zone => $date->identifier,
        default => 'UTC',
    };

    return IntlTimeZone::createTimeZone(match (true) {
        str_starts_with($tzIdentifier, '+'),
        str_starts_with($tzIdentifier, '-') => "GMT{$tzIdentifier}",
        default => $tzIdentifier,
    });
}
