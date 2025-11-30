<?php declare(strict_types=1);

namespace time;

interface Date {
    public Calendar $calendar { get; }

    public int $year { get; }

    /** @var int<1,99> $month */
    public int $month { get; }

    /** @var int<1,31> $dayOfMonth */
    public int $dayOfMonth { get; }

    /** @var int<1,366> $dayOfYear */
    public int $dayOfYear { get; }

    /** @var int<1,max> */
    public int $dayOfWeek { get; }

    /** @var int<1,max> */
    public int $weekOfYear { get; }

    public int $yearOfWeek { get; }

    public function add(Period $period): self;
    public function sub(Period $period): self;
}
