<?php declare(strict_types=1);

namespace time;

enum Month:int
{
    case January = 1;
    case February = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case August = 8;
    case September = 9;
    case October = 10;
    case November = 11;
    case December = 12;

    public function getAbbreviation():string {
        return \substr($this->name, 0, 3);
    }

    public function getPrevious(): Month
    {
        return self::from(($this->value - 1) ?: 12);
    }

    public function getNext(): Month
    {
        return self::from($this->value === 12 ? 1 : $this->value + 1);
    }

    /**
     * Returns the distance of this month to another month in months.
     *
     * @return int<-5,6>
     */
    public function distance(Month $other): int
    {
        $distance = $other->value - $this->value;

        if ($distance > 6) {
            $distance -= 12;
        } elseif ($distance <= -6) {
            $distance += 12;
        }

        \assert($distance >= -5 && $distance <= 6);
        return $distance;
    }
}
