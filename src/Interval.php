<?php declare(strict_types=1);

namespace time;

final readonly class Interval
{
    public const string ISO80000_SIGN_START_INCLUSIVE = '[';

    public const string ISO80000_SIGN_START_EXCLUSIVE = '(';

    public const string ISO80000_SIGN_END_INCLUSIVE = ']';

    public const string ISO80000_SIGN_END_EXCLUSIVE = ')';

    public function __construct(
        public Moment $start,
        public Moment $end,
        public Boundary $boundary = Boundary::InclusiveToExclusive,
    ) {}

    public function withStart(Moment $start): self
    {
        return new self($start, $this->end, $this->boundary);
    }

    public function withEnd(Moment $end): self
    {
        return new self($this->start, $end, $this->boundary);
    }

    public function withBoundarySameMoment(Boundary $boundary): self
    {
        return new self($this->start, $this->end, $boundary);
    }

    public function withBoundaryAdjustedMoment(Boundary $boundary): self
    {
        if ($this->boundary === $boundary) {
            return $this;
        }

        $start = $this->start;
        if ($this->boundary->isStartInclusive() && $boundary->isStartExclusive()) {
            $start = $start->sub(new Duration(nanoseconds: 1));
        } elseif ($this->boundary->isStartExclusive() && $boundary->isStartInclusive()) {
            $start = $start->add(new Duration(nanoseconds: 1));
        }

        $end = $this->end;
        if ($this->boundary->isEndExclusive() && $boundary->isEndInclusive()) {
            $end = $end->sub(new Duration(nanoseconds: 1));
        } elseif ($this->boundary->isEndInclusive() && $boundary->isEndExclusive()) {
            $end = $end->add(new Duration(nanoseconds: 1));
        }

        return new self($start, $end, $boundary);
    }

    public function contains(self|Momented $other): bool
    {
        if ($other instanceof Momented) {
            $thisInToEx = $this->withBoundaryAdjustedMoment(Boundary::InclusiveToExclusive);
            $thisStart  = $thisInToEx->start->toUnixTimestampTuple();
            $thisEnd    = $thisInToEx->end->toUnixTimestampTuple();

            $moment = $other->moment;
            $tuple  = $moment->toUnixTimestampTuple();

            return ($thisStart[0] < $tuple[0] || ($thisStart[0] === $tuple[0] && $thisStart[1] <= $tuple[1]))
                && ($thisEnd[0] > $tuple[0] || ($thisEnd[0] === $tuple[0] && $thisEnd[1] > $tuple[1]));
        }

        $thisStart  = $this->start->toUnixTimestampTuple();
        $thisEnd    = $this->end->toUnixTimestampTuple();
        $other      = $other->withBoundaryAdjustedMoment($this->boundary);
        $otherStart = $other->start->toUnixTimestampTuple();
        $otherEnd   = $other->end->toUnixTimestampTuple();

        return ($thisStart[0] < $otherStart[0] || ($thisStart[0] === $otherStart[0] && $thisStart[1] <= $otherStart[1]))
            && ($thisEnd[0] > $otherEnd[0] || ($thisEnd[0] === $otherEnd[0] && $thisEnd[1] >= $otherEnd[1]));
    }

    /**
     * Stringifies this interval using the ISO-8601 specification.
     *
     * - This will modify the start/end to be from inclusive to exclusive as defined in ISO-8601.
     * - The start and end will be formatted with the format "Y-m-d\\TH:i:sfp".
     *
     * @param "/"|"--" $separator
     * @return non-empty-string
     */
    public function toIso8601(string $separator = '/'): string
    {
        $inToEx = $this->withBoundaryAdjustedMoment(Boundary::InclusiveToExclusive);
        $fmt = new DateTimeFormatter('Y-m-d\\TH:i:sfp');

        $iso = $fmt->format($inToEx->start);
        $iso .= $separator;
        $iso .= $fmt->format($inToEx->end);

        return $iso;
    }

    public function toIso80000(DateTimeFormatter $formatter = new DateTimeFormatter('Y-m-d\\TH:i:sfp')): string
    {
        $iso = $this->boundary->isStartInclusive() ? self::ISO80000_SIGN_START_INCLUSIVE : self::ISO80000_SIGN_START_EXCLUSIVE;
        $iso .= $formatter->format($this->start);
        $iso .= ', ';
        $iso .= $formatter->format($this->end);
        $iso .= $this->boundary->isEndInclusive() ? self::ISO80000_SIGN_END_INCLUSIVE : self::ISO80000_SIGN_END_EXCLUSIVE;

        return $iso;
    }

    public static function fromRelativeStart(
        Duration|Period $start,
        Moment $end,
        Boundary $boundary = Boundary::InclusiveToExclusive,
    ): self {
        return new self($end->sub($start), $end, $boundary);
    }

    public static function fromRelativeEnd(
        Moment $start,
        Duration|Period $end,
        Boundary $boundary = Boundary::InclusiveToExclusive,
    ): self {
        return new self($start, $start->add($end), $boundary);
    }
}
