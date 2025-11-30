<?php declare(strict_types=1);

namespace time;

final class Period {
    public bool $isZero {
        get => $this->years || $this->months || $this->weeks || $this->days;
    }

    public function __construct(
        public readonly bool $isNegative = false,
        public readonly int $years = 0,
        public readonly int $months = 0,
        public readonly int $weeks = 0,
        public readonly int $days = 0,
    ) {}

    public function equals(self $other): bool
    {
        if ($this->isNegative !== $other->isNegative) {
            $other = $other->allInverted();
        }

        return $this->years === $other->years
            && $this->months === $other->months
            && $this->weeks === $other->weeks
            && $this->days === $other->days;
    }

    public function add(self $other): self
    {
        if ($this->isNegative !== $other->isNegative) {
            $other = $other->allInverted();
        }

        return new self(
            years: $this->years + $other->years,
            months: $this->months + $other->months,
            weeks: $this->weeks + $other->weeks,
            days: $this->days + $other->days,
        );
    }

    public function diff(self $other): self
    {
        $self  = $this->isNegative ? $this->allInverted() : $this;
        $other = $other->isNegative ? $other->allInverted() : $other;

        return new self(
            years: \abs($self->years - $other->years),
            months: \abs($self->months - $other->months),
            weeks: \abs($self->weeks - $other->weeks),
            days: \abs($self->days - $other->days),
        );
    }

    /**
     * Inverts all fields incl. the "isNegative" flag resulting in the same meaning of this period.
     *
     * E.g. "P1Y" will result in "-P-1Y" and vise versa.
     */
    public function allInverted(): self
    {
        return new self(
            isNegative: !$this->isNegative,
            years: $this->years * -1,
            months: $this->months * -1,
            weeks: $this->weeks * -1,
            days: $this->days * -1,
        );
    }

    public function inverted(): self
    {
        return new self(
            isNegative: !$this->isNegative,
            years: $this->years,
            months: $this->months,
            weeks: $this->weeks,
            days: $this->days,
        );
    }

    public function abs(): self {
        return $this->isNegative ? $this->inverted() : $this;
    }

    public function negated(): self {
        return $this->isNegative ? $this : $this->inverted();
    }

    /**
     * The months unit is adjusted to have an absolute value less than +/-11,
     * with the years unit being adjusted to compensate.
     *
     * The sign of the years and months units will be the same after normalization.
     * E.g, a period of "1 year and -25 months" will be normalized to "-1 year and -1 month".
     *
     * All other units will be unaffected by this.
     */
    public function withNormalizedMonthsIntoYears(): self
    {
        if ($this->months < 12 && $this->months > -12) {
            return $this;
        }

        return new self(
            isNegative: $this->isNegative,
            years: $this->years + \intdiv($this->months, 12),
            months: $this->months % 12,
            weeks: $this->weeks,
            days: $this->days,
        );
    }

    /**
     * The days unit is adjusted to have an absolute value less than +/-6,
     * with the weeks unit being adjusted to compensate.
     *
     * The sign of the days and weeks units will be the same after normalization.
     * E.g, a period of "1 week and -15 days" will be normalized to "-1 week and -1 days".
     *
     * All other units will be unaffected by this.
     */
    public function withNormalizedDaysIntoWeeks(): self
    {
        if ($this->days < 7 && $this->days > -7) {
            return $this;
        }

        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            weeks: $this->weeks + \intdiv($this->days, 7),
            days: $this->days % 7,
        );
    }

    public function withYears(int $years): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $years,
            months: $this->months,
            weeks: $this->weeks,
            days: $this->days,
        );
    }

    public function withMonths(int $months): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $months,
            weeks: $this->weeks,
            days: $this->days,
        );
    }

    public function withWeeks(int $weeks): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            weeks: $weeks,
            days: $this->days,
        );
    }

    public function withDays(int $days): self
    {
        return new self(
            isNegative: $this->isNegative,
            years: $this->years,
            months: $this->months,
            weeks: $this->weeks,
            days: $days,
        );
    }

    public function toIso(): string {
        $dateIso = '';
        $timeIso = '';

        if ($this->years) {
            $dateIso .= $this->years . 'Y';
        }
        if ($this->months) {
            $dateIso .= $this->months . 'M';
        }
        if ($this->weeks) {
            $dateIso .= $this->weeks . 'W';
        }
        if ($this->days) {
            $dateIso .= $this->days . 'D';
        }

        // An ISO period must have defined at least one value
        if ($dateIso === '') {
            $dateIso = '0D';
        }

        return $this->isNegative ? '-P' . $dateIso : 'P' . $dateIso;
    }
}
