<?php

namespace dt;

enum Boundary
{
    case InclusiveStartExclusiveEnd;
    case InclusiveAll;
    case ExclusiveStartInclusiveEnd;
    case ExclusiveAll;
}
