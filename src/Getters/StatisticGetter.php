<?php

namespace Ro749\SharedUtils\Getters;

//for when there is no main table, just statistics
class StatisticGetter extends Getter{
    public function __construct(
        array $columns = [],
        array $statistic = null,
        array $filters = [], 
        array $backend_filters = [],
        bool $debug = false
    ){
        parent::__construct(
            columns: $columns,
            statistics: ['stats_table' => $statistic],
            filters: $filters,
            backend_filters: $backend_filters,
            debug: $debug
        );
    }
    public function get(){
        
    }

}