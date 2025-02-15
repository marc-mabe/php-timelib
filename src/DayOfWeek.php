<?php declare(strict_types=1);

namespace time;

enum DayOfWeek:int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public function isWeekday() : bool
    {
        return $this->value < 6;
    }

    public function isWeekend() : bool
    {
        return $this->value > 5;
    }
}
