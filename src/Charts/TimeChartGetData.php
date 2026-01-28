<?php

namespace Ro749\SharedUtils\Charts;
use Ro749\SharedUtils\Statistics\ChartTime;

class TimeChartGetData extends ChartGetData
{
    public ChartTime $interval;
    public int $number;

    public function __construct(ChartTime $interval, int $number)
    {
        $this->interval = $interval;
        $this->number = $number;
    }
}