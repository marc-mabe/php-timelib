<?php

include __DIR__ . '/../vendor/autoload.php';

function stringifyMoment(\dt\Moment $moment) {
    $tuple = $moment->toUnixTimestampTuple();
    return "Moment('{$moment->format('Y-m-d H:i:sf')}', {$tuple[0]}, {$tuple[1]})";
}

function stringifyLocalDate(\dt\LocalDate $date) {
    return "LocalDate('{$date->format('Y-m-d')}')";
}

function stringifyLocalTime(\dt\LocalTime $time) {
    return "LocalTime('{$time->format('H:i:sf')}')";
}

function stringifyLocalDateTime(\dt\LocalDateTime $dt) {
    return "LocalDateTime('{$dt->format('Y-m-d H:i:sf')}')";
}

function stringifyZonedDateTime(\dt\ZonedDateTime $dt) {
    return "ZonedDateTime('{$dt->format('Y-m-d H:i:sf P [e]')}')";
}

function stringifyZoneOffset(\dt\ZoneOffset $zoneOffset) {
    return "ZoneOffset('{$zoneOffset->format('e')}')";
}

function stringify(\dt\Moment|\dt\LocalDate|\dt\LocalTime|\dt\LocalDateTime|\dt\ZonedDateTime|\dt\ZoneOffset $t) {
    return match (true) {
        $t instanceof \dt\Moment => stringifyMoment($t),
        $t instanceof \dt\LocalDate => stringifyLocalDate($t),
        $t instanceof \dt\LocalTime => stringifyLocalTime($t),
        $t instanceof \dt\LocalDateTime => stringifyLocalDateTime($t),
        $t instanceof \dt\ZonedDateTime => stringifyZonedDateTime($t),
        $t instanceof \dt\ZoneOffset => stringifyZoneOffset($t),
    };
}
