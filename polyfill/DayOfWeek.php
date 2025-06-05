<?php declare(strict_types=1);

namespace time;

enum DayOfWeek
{
    case Monday;
    case Tuesday;
    case Wednesday;
    case Thursday;
    case Friday;
    case Saturday;
    case Sunday;

    /** @return int<1,7> */
    public function getIsoNumber(): int
    {
        return match ($this) {
            self::Monday    => 1,
            self::Tuesday   => 2,
            self::Wednesday => 3,
            self::Thursday  => 4,
            self::Friday    => 5,
            self::Saturday  => 6,
            self::Sunday    => 7,
        };
    }

    /** @param int<1,7> $isoNumber */
    public static function fromIsoNumber(int $isoNumber): self
    {
        return match ($isoNumber) {
            1 => self::Monday,
            2 => self::Tuesday,
            3 => self::Wednesday,
            4 => self::Thursday,
            5 => self::Friday,
            6 => self::Saturday,
            7 => self::Sunday,
        };
    }

    public function isWeekday() : bool
    {
        return $this !== self::Saturday && $this !== self::Sunday;
    }

    public function isWeekend() : bool
    {
        return $this === self::Saturday || $this === self::Sunday;
    }

    public function getPrevious(): DayOfWeek
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

    public function getNext(): DayOfWeek
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
    public function distance(DayOfWeek $other): int
    {
        $distance = $other->getIsoNumber() - $this->getIsoNumber();

        if ($distance > 3) {
            $distance -= 7;
        } elseif ($distance < -3) {
            $distance += 7;
        }

        return $distance;
    }
}
