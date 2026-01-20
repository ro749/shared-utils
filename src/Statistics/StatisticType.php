<?php

namespace Ro749\SharedUtils\Statistics;

enum StatisticType: string
{
    case COUNT = 'count';
    case AVERAGE = 'average';
    case SUM = 'sum';
    case PERCENTAGE = 'percentage';
}
?>