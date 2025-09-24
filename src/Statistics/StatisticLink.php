<?php

namespace Ro749\SharedUtils\Statistics;

class StatisticLink{
    public string $table;
    public string $column;

    public function __construct(string $table, string $column) {
        $this->table = $table;
        $this->column = $column;
    }
}