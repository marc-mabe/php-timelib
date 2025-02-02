<?php

namespace dt;

interface Time {

    /** @var int<0,23> $hour */
    public int $hour { get; }

    /** @var int<0,59> $minute */
    public int $minute { get; }

    /** @var int<0,59> $second */
    public int $second { get; }

    /** @var int<0,999> $milliOfSecond */
    public int $milliOfSecond { get; }

    /** @var int<0,999999> $microOfSecond */
    public int $microOfSecond { get; }

    /** @var int<0,999999999> $nanoOfSecond */
    public int $nanoOfSecond { get; }
}
