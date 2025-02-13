<?php

use time\Duration;
use time\LocalDate;
use time\LocalDateTime;
use time\LocalTime;
use time\Moment;
use time\Zone;
use time\ZonedDateTime;

include __DIR__ . '/../vendor/autoload.php';

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
    return "Zone('{$zone->format('e')}')";
}

function stringifyDuration(Duration $duration) {
    return "Duration('{$duration->toIso()}')";
}

function stringify(null|Moment|LocalDate|LocalTime|LocalDateTime|ZonedDateTime|Zone|Duration $v) {
    return match (true) {
        $v === null => 'null',
        $v instanceof Moment => stringifyMoment($v),
        $v instanceof LocalDate => stringifyLocalDate($v),
        $v instanceof LocalTime => stringifyLocalTime($v),
        $v instanceof LocalDateTime => stringifyLocalDateTime($v),
        $v instanceof ZonedDateTime => stringifyZonedDateTime($v),
        $v instanceof Zone => stringifyZone($v),
        $v instanceof Duration => stringifyDuration($v),
    };
}
