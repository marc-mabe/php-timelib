<?php declare(strict_types=1);

namespace time;

abstract class ZoneInfo
{
    abstract public ?ZoneOffset $fixedOffset {
        get;
    }

    /**
     * @param Moment|null $from  Get transitions from this moment (inclusive)
     * @param Moment|null $until Get transitions until this moment (exclusive)
     * @param int|null    $limit Limits the number of transitions, negative value takes the items from the end
     * @return \Iterator<ZoneTransition>
     */
    abstract public function getTransitions(
        ?Moment $from = null,
        ?Moment $until = null,
        ?int $limit = null,
    ): \Iterator;

    public function getPrevTransition(Moment $moment): ?ZoneTransition
    {
        $transitions = $this->getTransitions(until: $moment, limit: -1);
        return $transitions->current();
    }

    public function getTransitionAt(Moment $moment): ?ZoneTransition
    {
        $transitions = $this->getTransitions(until: $moment->add(new Duration(seconds: 1)), limit: -1);
        return $transitions->current();
    }

    public function getNextTransition(Moment $moment): ?ZoneTransition
    {
        $transitions = $this->getTransitions(from: $moment->add(new Duration(seconds: 1)), limit: 1);
        return $transitions->current();
    }

    abstract public function getOffsetAt(Moment $moment): ZoneOffset;
}
