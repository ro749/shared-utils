<?php

namespace Ro749\SharedUtils\Getters;

use Ro749\SharedUtils\Statistics\ChartTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimeGetter extends Getter{

    public function __construct(
        array $columns = [],
        array $statistics = [],
        array $filters = [], 
        array $backend_filters = [],
        bool $debug = false
    )
    {
        parent::__construct(
            columns: $columns,
            statistics: $statistics,
            filters: $filters,
            backend_filters: $backend_filters,
            debug: $debug
        );
    }

    public function get(ChartTime $interval, int $number) {
        //generates the temporal dates table, with the id as the dates
        $query = DB::table(DB::raw("
            (WITH RECURSIVE last_dates AS (
                SELECT CURDATE() AS date
                UNION ALL
                SELECT date - INTERVAL 1 ".$interval->value."
                FROM last_dates
                WHERE date > CURDATE() - INTERVAL ".$number." ".$interval->value."
            )
            SELECT DATE_FORMAT(date, '".$interval->format()."') as id, DATE_FORMAT(date, '".$interval->label_format()."') as label_date FROM last_dates) as last_dates
        "));
        //generate the subqueries of the statistics
        $this->apply_statistics($query,'last_dates');
        //generates the selects
        $this->prosses_columns($query,'last_dates',$joins,'');
        $query = $query->orderBy('id');
        if($this->debug){
            DB::enableQueryLog();
        }
        $ans = $query->get();
        if($this->debug){
            Log::debug(DB::getQueryLog());
        }
        
        $ans = collect($ans)->reduce(function ($carry, $item) {
            foreach ($item as $key => $value) {
                $carry[$key][] = $value;
            }
            return $carry;
        }, []);
//
        //$ans = $query->pluck(array_keys($this->columns)[0])->toArray();
        //if(!$this->statistics[array_key_first($this->statistics)]->cumulative){
        //    $ans = array_reverse($ans);
        //}
        return $ans;
    }

}