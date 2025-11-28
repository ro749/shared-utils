<?php

namespace Ro749\SharedUtils\Getters;

use Ro749\SharedUtils\Statistics\ChartTime;
use Illuminate\Support\Facades\DB;

class TimeGetter extends Getter{

    public function __construct(
        array $columns = [],
        array $statistics = [],
        array $filters = [], 
        array $backend_filters = []
    )
    {
        parent::__construct(
            columns: $columns,
            statistics: $statistics,
            filters: $filters,
            backend_filters: $backend_filters
        );
    }

    public function get(ChartTime $interval, int $number) {
        $query = DB::table(DB::raw("
            (WITH RECURSIVE last_dates AS (
                SELECT CURDATE() AS date
                UNION ALL
                SELECT date - INTERVAL 1 ".$interval->value."
                FROM last_dates
                WHERE date > CURDATE() - INTERVAL ".$number." ".$interval->value."
            )
            SELECT DATE_FORMAT(date, '".$interval->format()."') as id FROM last_dates) as last_dates
        "));
        $this->apply_statistics($query,'last_dates');
        $this->prosses_columns($query,'last_dates',$joins,'');
        return array_reverse($query->pluck(array_keys($this->columns)[0])->toArray());
    }

}