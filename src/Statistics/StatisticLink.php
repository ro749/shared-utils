<?php

namespace Ro749\SharedUtils\Statistics;

class StatisticLink{
    public string $model_class;
    public string $column;

    public function __construct(string $model_class, string $column) {
        $this->model_class = $model_class;
        $this->column = $column;
    }
    public function get_table(){
        return ($this->model_class)::make()->getTable();
    }
}