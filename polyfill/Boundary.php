<?php declare(strict_types=1);

namespace time;

enum Boundary {
    case InclusiveToExclusive;
    case InclusiveToInclusive;
    case ExclusiveToExclusive;
    case ExclusiveToInclusive;

    public function isStartInclusive(): bool
    {
        return $this === self::InclusiveToExclusive
            || $this === self::InclusiveToInclusive;
    }

    public function isStartExclusive(): bool
    {
        return !$this->isStartInclusive();
    }

    public function isEndInclusive(): bool
    {
        return $this === self::InclusiveToInclusive
            || $this === self::ExclusiveToInclusive;
    }

    public function isEndExclusive(): bool
    {
        return !$this->isEndInclusive();
    }
}
