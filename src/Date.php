<?php

namespace dt;

interface Date {
    public int $year { get; }
    public Month $month { get; }

    /** @var int<1,12> $dayOfMonth */
    public int $dayOfMonth { get; }

    /** @var int<1,366> $dayOfYear */
    public int $dayOfYear { get; }
}
