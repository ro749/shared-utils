<?php

namespace Ro749\SharedUtils\Statistics;

class StatisticColumn {
    //if it the column is the same as the key, leavve blank
    public string $column = '';
    public StatisticType $type;

    public string $filter;

    public function __construct(StatisticType $type, string $filter='', string $label='' ) {
        $this->type = $type;
        $this->filter = $filter;
        $this->label = $label;
    }
}