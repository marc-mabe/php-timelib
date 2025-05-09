<?php declare(strict_types=1);

namespace time;

interface Date {
    public Calendar $calendar { get; }

    public int $year { get; }
    public Month $month { get; }

    /** @var int<1,31> $dayOfMonth */
    public int $dayOfMonth { get; }

    /** @var int<1,366> $dayOfYear */
    public int $dayOfYear { get; }

    public DayOfWeek $dayOfWeek { get; }

    public WeekInfo $weekInfo { get; }

    /** @var int<1,max> */
    public int $weekOfYear { get; }

    public int $yearOfWeek { get; }
}
