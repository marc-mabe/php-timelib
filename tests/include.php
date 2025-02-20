<?php

use time\Duration;
use time\Period;
use time\LocalDate;
use time\LocalDateTime;
use time\LocalTime;
use time\Moment;
use time\MonotonicClock;
use time\StopWatch;
use time\WallClock;
use time\Zone;
use time\ZonedDateTime;

include __DIR__ . '/../vendor/autoload.php';

function stringifyEnum(UnitEnum $enum) {
    return $enum::class . "::" . $enum->name;
}

function stringifyMoment(Moment $moment) {
    $tuple = $moment->toUnixTimestampTuple();
    return "Moment('{$moment->format('D Y-m-d H:i:sf')}', {$tuple[0]}, {$tuple[1]})";
}

function stringifyLocalDate(LocalDate $date) {
    return "LocalDate('{$date->format('D Y-m-d')}')";
}

function stringifyLocalTime(LocalTime $time) {
    return "LocalTime('{$time->format('H:i:sf')}')";
}

function stringifyLocalDateTime(LocalDateTime $dt) {
    return "LocalDateTime('{$dt->format('D Y-m-d H:i:sf')}')";
}

function stringifyZonedDateTime(ZonedDateTime $dt) {
    return "ZonedDateTime('{$dt->format('D Y-m-d H:i:sf P [e]')}')";
}

function stringifyZone(Zone $zone) {
    return $zone::class . "('{$zone->format('e')}')";
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

function stringify(mixed $v) {
    return match (true) {
        is_int($v) => (string)$v,
        $v === null,
        is_scalar($v) => var_export($v, true),
        $v instanceof UnitEnum => stringifyEnum($v),
        $v instanceof Moment => stringifyMoment($v),
        $v instanceof LocalDate => stringifyLocalDate($v),
        $v instanceof LocalTime => stringifyLocalTime($v),
        $v instanceof LocalDateTime => stringifyLocalDateTime($v),
        $v instanceof ZonedDateTime => stringifyZonedDateTime($v),
        $v instanceof Zone => stringifyZone($v),
        $v instanceof Period => stringifyPeriod($v),
        $v instanceof Duration => stringifyDuration($v),
        $v instanceof WallClock => stringifyWallClock($v),
        $v instanceof MonotonicClock => stringifyMonotonicClock($v),
        $v instanceof StopWatch => stringifyStopWatch($v),
        is_array($v) && array_is_list($v) => '[' . implode(', ', array_map('stringify', $v)) . ']',
    };
}
