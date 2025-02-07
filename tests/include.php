<?php

include __DIR__ . '/../vendor/autoload.php';

function stringifyMoment(\dt\Moment $moment) {
    $tuple = $moment->toUnixTimestampTuple();

    return "Moment('{$moment->format('Y-m-d H:i:sf')}', {$tuple[0]}, {$tuple[1]})";
}
