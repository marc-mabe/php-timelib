<?php declare(strict_types=1);

namespace time;

/**
 * Behavior on disambiguate if the date and time does not exist in the time zone, or exists more than once.
 */
enum Disambiguation
{
    /** Acts as 'EARLIER' for backward transitions and 'LATER' for forward transitions. */
    case COMPATIBLE;

    /** The earlier variant of two possible date-times. */
    case EARLIER;

    /** The later variant of two possible date-times. */
    case LATER;

    /** Reject with an error. */
    case REJECT;
}
