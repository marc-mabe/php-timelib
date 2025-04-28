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

    public function getPrevious(): DayOfWeek
    {
        return self::from(($this->value - 1) ?: 7);
    }

    public function getNext(): DayOfWeek
    {
        return self::from($this->value === 7 ? 1 : $this->value + 1);
    }

    /**
     * Returns the distance of this day-of-week to another day-of-week in days.
     *
     * @return int<-3,3>
     */
    public function distance(DayOfWeek $other): int
    {
        $distance = $other->value - $this->value;

        if ($distance > 3) {
            $distance -= 7;
        } elseif ($distance < -3) {
            $distance += 7;
        }

        return $distance;
    }
}
