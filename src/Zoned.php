<?php

namespace dt;

interface Zoned extends Date, Time
{
    public ZoneOffset $zoneOffset { get; }
}
