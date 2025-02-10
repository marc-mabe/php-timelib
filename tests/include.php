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
