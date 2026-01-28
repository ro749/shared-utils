<?php

namespace Ro749\SharedUtils\Charts;

class BaseChartGetData extends ChartGetData
{
    public int $start = 0;
    public ?int $length = null;

    public function __construct(int $start = 0, int $length = null)
    {
        $this->start = $start;
        $this->length = $length;
    }
}