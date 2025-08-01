<?php

namespace Ro749\SharedUtils\Models;

enum StatisticType: string
{
    case COUNT = 'count';
    case AVERAGE = 'average';

    case TOTAL = 'sum';
}
?>