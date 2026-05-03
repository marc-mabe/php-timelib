<?php declare(strict_types=1);

namespace time;

enum IsoDayOfWeek: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public function getPrevious(): IsoDayOfWeek
    {
        return match ($this) {
            self::Monday    => self::Sunday,
            self::Tuesday   => self::Monday,
            self::Wednesday => self::Tuesday,
            self::Thursday  => self::Wednesday,
            self::Friday    => self::Thursday,
            self::Saturday  => self::Friday,
            self::Sunday    => self::Saturday,
        };
    }

    public function getNext(): IsoDayOfWeek
    {
        return match ($this) {
            self::Monday    => self::Tuesday,
            self::Tuesday   => self::Wednesday,
            self::Wednesday => self::Thursday,
            self::Thursday  => self::Friday,
            self::Friday    => self::Saturday,
            self::Saturday  => self::Sunday,
            self::Sunday    => self::Monday,
        };
    }

    /**
     * Returns the distance of this day-of-week to another day-of-week in days.
     *
     * @return int<-3,3>
     */
    public function distance(IsoDayOfWeek $other): int
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
