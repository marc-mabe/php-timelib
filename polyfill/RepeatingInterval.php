<?php declare(strict_types=1);

namespace time;

use Traversable;

final class RepeatingInterval implements \IteratorAggregate
{
    public ?Instanted $end  {
        get {
            if ($this->repetitions === null) {
                return null;
            }

            $end = $this->start;
            if ($this->repetitions > 1 && $this->durationOrPeriod instanceof Duration) {
                $bias = $this->repetitions * ($this->durationOrPeriod->isNegative ? -1 : 1);
                $end  = $end->add(new Duration(
                    seconds: $this->durationOrPeriod->totalSeconds * $bias,
                    nanoseconds: $this->durationOrPeriod->nanosOfSecond * $bias,
                ));
            } else {
                for ($i = 0; $i < $this->repetitions; ++$i) {
                    $end = $end->add($this->durationOrPeriod);
                }
            }

            return $end;
        }
    }

    /** @param null|int<0,max> $repetitions */
    public function __construct(
        public readonly ?int $repetitions,
        public readonly Instanted $start,
        public readonly Duration|Period $durationOrPeriod,
    ) {}

    public function getIterator(): Traversable
    {
        yield $this->start;

        $curr = $this->start;
        $i    = 0;
        while ($this->repetitions !== null && $i++ < $this->repetitions) {
            $curr = $curr->add($this->durationOrPeriod);
            yield $curr;
        }
    }

    /**
     * Stringifies this interval using the ISO-8601 specification.
     *
     * - The start will be formatted with the format "Y-m-d\\TH:i:sfp".
     *
     * @param "/"|"--" $separator
     * @return non-empty-string
     */
    public function toIso8601(string $separator = '/'): string
    {
        $fmt = new DateTimeFormatter('Y-m-d\\TH:i:sfp');

        $iso = 'R' . $this->repetitions;
        if ($this->durationOrPeriod->isNegative) {
            $iso .= $separator . $this->durationOrPeriod->inverted()->toIso();
            $iso .= $separator . $fmt->format($this->start);
        } else {
            $iso .= $separator . $fmt->format($this->start);
            $iso .= $separator . $this->durationOrPeriod->toIso();
        }

        return $iso;
    }
}
