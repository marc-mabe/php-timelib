<?php

namespace time;

interface Zoned extends Date, Time
{
    public ZoneOffset $zoneOffset { get; }
}
