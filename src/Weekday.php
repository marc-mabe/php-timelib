<?php

namespace time;

enum Weekday
{
    case Monday;
    case Tuesday;
    case Wednesday;
    case Thursday;
    case Friday;
    case Saturday;
    case Sunday;

    public function isWeekday() : bool {
        return !$this->isWeekend();
    }

    public function isWeekend() : bool {
        return $this === Weekday::Saturday || $this === Weekday::Sunday;
    }
}
