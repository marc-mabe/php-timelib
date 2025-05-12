<?php declare(strict_types=1);

namespace time;

enum TimeUnit {
    case Hour;
    case Minute;
    case Second;
    case Millisecond;
    case Microsecond;
    case Nanosecond;
}
