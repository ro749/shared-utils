<?php

namespace Ro749\SharedUtils\Statistics;
//link of this model to the bottom model bia column
class StatisticLink{
    //this is the "middle" model, the bottom one goes in the model of the stat
    public string $model_class;
    //this is the bottom column that joind the middle model with the bottom one
    public string $column;

    public function __construct(string $model_class, string $column) {
        $this->model_class = $model_class;
        $this->column = $column;
    }
    public function get_table(){
        return ($this->model_class)::make()->getTable();
    }
}