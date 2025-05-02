<?php declare(strict_types=1);

namespace time;

interface Momented
{
    public Moment $moment {
        get;
    }

    public function add(Duration|Period $durationOrPeriod): Momented;
    public function sub(Duration|Period $durationOrPeriod): Momented;
}
