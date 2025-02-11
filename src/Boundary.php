<?php

namespace time;

enum Boundary
{
    case InclusiveStartExclusiveEnd;
    case InclusiveAll;
    case ExclusiveStartInclusiveEnd;
    case ExclusiveAll;
}
