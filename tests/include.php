<?php

use time\Duration;
use time\Period;
use time\PlainDate;
use time\PlainDateTime;
use time\PlainTime;
use time\Instant;
use time\MonotonicClock;
use time\StopWatch;
use time\WallClock;
use time\Zone;
use time\ZonedDateTime;
use time\ZoneInfo;
use time\ZoneTransition;
use time\Interval;
use time\RepeatingInterval;

include __DIR__ . '/../vendor/autoload.php';

function stringifyEnum(UnitEnum $enum) {
    return $enum::class . "::" . $enum->name;
}

function stringifyInstant(Instant $instant) {
    $fmt   = new time\DateTimeFormatter('D Y-m-d H:i:sf');
    $tuple = $instant->toUnixTimestampTuple();
    return "Instant('{$fmt->format($instant)}', {$tuple[0]}, {$tuple[1]})";
}

function stringifyPlainDate(PlainDate $date) {
    $fmt = new time\DateTimeFormatter('D Y-m-d');
    return "PlainDate('{$fmt->format($date)}')";
}

function stringifyPlainTime(PlainTime $time) {
    $fmt = new time\DateTimeFormatter('H:i:sf');
    return "PlainTime('{$fmt->format($time)}')";
}

function stringifyPlainDateTime(PlainDateTime $dt) {
    $fmt = new time\DateTimeFormatter('D Y-m-d H:i:sf');
    return "PlainDateTime('{$fmt->format($dt)}')";
}

function stringifyZonedDateTime(ZonedDateTime $dt) {
    $fmt = new time\DateTimeFormatter('D Y-m-d H:i:sf P [e]');
    return "ZonedDateTime('{$fmt->format($dt)}')";
}

function stringifyZone(Zone $zone) {
    return $zone::class . "('{$zone->identifier}')";
}

function stringifyZoneInfo(ZoneInfo $zoneInfo) {
    $fixedOffset = stringify($zoneInfo->fixedOffset);
    return "ZoneInfo(fixedOffset={$fixedOffset})";
}

function stringifyZoneTransition(ZoneTransition $transition) {
    $offset = stringify($transition->offset);
    $instant = stringify($transition->instant);
    return "ZoneTransition(offset={$offset}, instant={$instant})";
}

function stringifyDuration(Duration $duration) {
    return "Duration('{$duration->toIso()}')";
}

function stringifyPeriod(Period $period) {
    return "Period('{$period->toIso()}')";
}

function stringifyWallClock(WallClock $clock) {
    return "WallClock(resolution: " . stringify($clock->resolution) . ", modifier: " . stringify($clock->modifier) . ")";
}

function stringifyMonotonicClock(MonotonicClock $clock) {
    return "MonotonicClock(resolution: " . stringify($clock->resolution) . ", modifier: " . stringify($clock->modifier) . ")";
}

function stringifyStopWatch(StopWatch $watch) {
    $isRunning = stringify($watch->isRunning);
    $elapsedNs = stringify($watch->getElapsedTime());

    return $watch::class . "(elapsed: {$elapsedNs}ns, isRunning: {$isRunning})";
}

function stringifyInterval(Interval $interval) {
    return "Interval('{$interval->toIso80000()}')";
}

function stringifyRepeatingInterval(RepeatingInterval $interval) {
    return "RepeatingInterval('{$interval->toIso8601()}')";
}

function stringify(mixed $v) {
    return match (true) {
        is_int($v) => (string)$v,
        $v === null,
        is_scalar($v) => var_export($v, true),
        $v instanceof UnitEnum => stringifyEnum($v),
        $v instanceof Instant => stringifyInstant($v),
        $v instanceof PlainDate => stringifyPlainDate($v),
        $v instanceof PlainTime => stringifyPlainTime($v),
        $v instanceof PlainDateTime => stringifyPlainDateTime($v),
        $v instanceof ZonedDateTime => stringifyZonedDateTime($v),
        $v instanceof Zone => stringifyZone($v),
        $v instanceof ZoneInfo => stringifyZoneInfo($v),
        $v instanceof ZoneTransition => stringifyZoneTransition($v),
        $v instanceof Period => stringifyPeriod($v),
        $v instanceof Duration => stringifyDuration($v),
        $v instanceof WallClock => stringifyWallClock($v),
        $v instanceof MonotonicClock => stringifyMonotonicClock($v),
        $v instanceof StopWatch => stringifyStopWatch($v),
        $v instanceof Interval => stringifyInterval($v),
        $v instanceof RepeatingInterval => stringifyRepeatingInterval($v),
        is_array($v) && array_is_list($v) => '[' . implode(', ', array_map('stringify', $v)) . ']',
    };
}
