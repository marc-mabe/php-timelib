<?php declare(strict_types=1);

namespace time;

interface Instanted
{
    public Instant $instant {
        get;
    }

    public function add(Duration|Period $durationOrPeriod): self;
    public function sub(Duration|Period $durationOrPeriod): self;
}
