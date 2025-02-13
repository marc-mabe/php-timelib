<?php

namespace time;

interface Zoned extends Date, Time
{
    public Zone $zone { get; }
}
