<?php declare(strict_types=1);

namespace time;

abstract class ZoneInfo
{
    abstract public ?ZoneOffset $fixedOffset {
        get;
    }

    /**
     * @param Instant|null $from  Get transitions from this instant (inclusive)
     * @param Instant|null $until Get transitions until this instant (exclusive)
     * @param int|null    $limit Limits the number of transitions, negative value takes the items from the end
     * @return \Iterator<ZoneTransition>
     */
    abstract public function getTransitions(
        ?Instant $from = null,
        ?Instant $until = null,
        ?int $limit = null,
    ): \Iterator;

    public function getPrevTransition(Instant $instant): ?ZoneTransition
    {
        $transitions = $this->getTransitions(until: $instant, limit: -1);
        return $transitions->current();
    }

    public function getTransitionAt(Instant $instant): ?ZoneTransition
    {
        $transitions = $this->getTransitions(until: $instant->add(new Duration(seconds: 1)), limit: -1);
        return $transitions->current();
    }

    public function getNextTransition(Instant $instant): ?ZoneTransition
    {
        $transitions = $this->getTransitions(from: $instant->add(new Duration(seconds: 1)), limit: 1);
        return $transitions->current();
    }

    /**
     * Returns a ZoneOffset for the given Instant.
     *
     * While `fixedOffset` and `getTransitionAt($instant)` both can be NULL,
     * this will return a valid offset in all cases as follows:
     *
     * - If `fixedOffset` is not NULL              -> return `fixedOffset`
     * - If `getTransitionAt($instant)` is not NULL -> return the offset of the transition
     * - Else                                      -> return a zero offset
     */
    public function getOffsetAt(Instant $instant): ZoneOffset
    {
        return $this->fixedOffset
            ?? $this->getTransitionAt($instant)->offset
            ?? new ZoneOffset(totalSeconds: 0);
    }
}
