<?php declare(strict_types=1);

namespace time;

class ZoneTransition
{
    public function __construct(
        public readonly Instant $instant,
        public readonly ZoneOffset $offset,
    ) {}
}
