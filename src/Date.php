<?php

namespace time;

interface Date {
    public int $year { get; }
    public bool $isLeapYear { get; }
    public Month $month { get; }

    /** @var int<1,12> $dayOfMonth */
    public int $dayOfMonth { get; }

    /** @var int<1,366> $dayOfYear */
    public int $dayOfYear { get; }
}
