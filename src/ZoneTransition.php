<?php

namespace time;

class ZoneTransition
{
    public function __construct(
        public readonly Moment $moment,
        public readonly ZoneOffset $offset,
    ) {}
}
