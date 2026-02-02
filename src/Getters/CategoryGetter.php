<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//for when there is no main table, just statistics
class CategoryGetter extends Getter{
    public string $option_name;
    public function __construct(
        string $option_name,
        array $columns = [],
        array $statistics = [],
        array $filters = [], 
        array $backend_filters = [],
        bool $debug = false
    ){
        parent::__construct(
            columns: $columns,
            statistics: $statistics,
            filters: $filters,
            backend_filters: $backend_filters,
            debug: $debug
        );
        $this->option_name = $option_name;
    }
    public function get($start=null, $length=null){
        $caregory_table = "(";
        $options = config('options.'.$this->option_name);
        $options_formated = [];
        foreach($options as $key => $option){
            $options_formated[] = "SELECT ".$key." as id, '".$option."' as label";
        }
        $caregory_table .= implode(" UNION ALL ", $options_formated);
        $caregory_table .= ") as categories";

        $query = DB::table(DB::raw($caregory_table));
        $query->select('categories.label');
        //generate the subqueries of the statistics
        $this->apply_statistics($query,'categories');
        //generates the selects
        $this->prosses_columns($query,'categories',$joins,'');
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

        return $ans;
    }

}