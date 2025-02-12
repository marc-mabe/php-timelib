<?php

include __DIR__ . '/../vendor/autoload.php';

function stringifyMoment(\time\Moment $moment) {
    $tuple = $moment->toUnixTimestampTuple();
    return "Moment('{$moment->format('D Y-m-d H:i:sf')}', {$tuple[0]}, {$tuple[1]})";
}

function stringifyLocalDate(\time\LocalDate $date) {
    return "LocalDate('{$date->format('D Y-m-d')}')";
}

function stringifyLocalTime(\time\LocalTime $time) {
    return "LocalTime('{$time->format('H:i:sf')}')";
}

function stringifyLocalDateTime(\time\LocalDateTime $dt) {
    return "LocalDateTime('{$dt->format('D Y-m-d H:i:sf')}')";
}

function stringifyZonedDateTime(\time\ZonedDateTime $dt) {
    return "ZonedDateTime('{$dt->format('D Y-m-d H:i:sf P [e]')}')";
}

function stringifyZoneOffset(\time\ZoneOffset $zoneOffset) {
    return "ZoneOffset('{$zoneOffset->format('e')}')";
}

function stringify(\time\Moment|\time\LocalDate|\time\LocalTime|\time\LocalDateTime|\time\ZonedDateTime|\time\ZoneOffset $t) {
    return match (true) {
        $t instanceof \time\Moment => stringifyMoment($t),
        $t instanceof \time\LocalDate => stringifyLocalDate($t),
        $t instanceof \time\LocalTime => stringifyLocalTime($t),
        $t instanceof \time\LocalDateTime => stringifyLocalDateTime($t),
        $t instanceof \time\ZonedDateTime => stringifyZonedDateTime($t),
        $t instanceof \time\ZoneOffset => stringifyZoneOffset($t),
    };
}
