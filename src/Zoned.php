<?php declare(strict_types=1);

namespace time;

interface Zoned extends Date, Time
{
    public Zone $zone { get; }
}
