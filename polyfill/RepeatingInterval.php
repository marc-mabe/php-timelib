<?php declare(strict_types=1);

namespace time;

use Traversable;

/** @implements \IteratorAggregate<int, Instanted> */
final class RepeatingInterval implements \IteratorAggregate
{
    public ?Instanted $end  {
        get {
            if ($this->repetitions === null) {
                return null;
            }

            $end = $this->start;
            if ($this->repetitions > 1 && $this->durationOrPeriod instanceof Duration) {
                $totalSeconds  = $this->durationOrPeriod->totalSeconds;
                $nanosOfSecond = $this->durationOrPeriod->nanosOfSecond;

                if ($totalSeconds > \intdiv(PHP_INT_MAX, $this->repetitions)
                    || $totalSeconds < \intdiv(PHP_INT_MIN, $this->repetitions)
                ) {
                    throw new RangeError(\sprintf(
                        'Repeating %s seconds %s times is out-of-range of PHP integer',
                        $totalSeconds,
                        $this->repetitions,
                    ));
                }

                if ($nanosOfSecond > \intdiv(PHP_INT_MAX, $this->repetitions)) {
                    // Use floating point to prevent integer overflow
                    $totalSeconds *= $this->repetitions;
                    $nanosOfSecond = ($nanosOfSecond / 1_000_000_000) * $this->repetitions;
                    $nanosOverflow = (int)\floor($nanosOfSecond);
                    $nanosOfSecond = (int)\ceil(\fmod($nanosOfSecond, 1) * 1_000_000_000);

                    if ($totalSeconds > PHP_INT_MAX - $nanosOverflow
                        || $totalSeconds < PHP_INT_MIN + $nanosOverflow
                    ) {
                        throw new RangeError(\sprintf(
                            'Adding the overflow of seconds of nanos %s to the total seconds %s is out-of-range of PHP integer',
                            $nanosOverflow,
                            $totalSeconds,
                        ));
                    }

                    $totalSeconds  += $nanosOverflow;
                } else {
                    $totalSeconds  *= $this->repetitions;
                    $nanosOfSecond *= $this->repetitions;
                }

                $end = $end->add(new Duration(
                    seconds: $totalSeconds,
                    nanoseconds: $nanosOfSecond,
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
        public readonly Instanted $start,
        public readonly Duration|Period $durationOrPeriod,
        public readonly ?int $repetitions,
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
