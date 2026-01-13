<?php

namespace Ro749\SharedUtils\Statistics;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
class Chart extends Statistic
{

    public ChartTime $interval = ChartTime::DAY;

    public int $number = 0;

    public bool $cumulative = false;

    public function __construct(
        string $model_class, 
        array $columns, 
        array $filters = [], 
        array $backend_filters = [],
        StatisticLink $link = null,
        ChartTime $interval = ChartTime::DAY,
        int $number = 0,
        bool $cumulative = false
    )
    {
        parent::__construct(
            $model_class, 
            'created_at', 
            $columns, 
            $filters, 
            $backend_filters,
            $link
        );
        $this->interval = $interval;
        $this->number = $number;
        $this->cumulative = $cumulative;
    }

    public function get_query()
    {
        if(empty($this->link)){
            $subquery = 
                ($this->model_class)::query()->
                groupBy($this->group_column.'_label');
        }
        else{
            $subquery = 
                ($this->link->model_class)::query()->
                select($this->link->column)->
                join(($this->model_class)::make()->getTable(), $this->link->get_table().'.id', '=', $this->get_table().'.'.$this->group_column);
        }
        return $subquery;
    }
    public function extra_process($query)
    {
        switch ($this->interval) {
            case ChartTime::DAY:
                $query->addSelect(DB::raw('DATE_FORMAT('.$this->group_column.', \'%Y-%m-%d\') as '.$this->group_column.'_label'));
                $startDate = Carbon::now()->subDays($this->number)->startOfDay();
                break;
            case ChartTime::WEEK:
                $query->addSelect(DB::raw('DATE_FORMAT('.$this->group_column.', \'%Y-%u\') as '.$this->group_column.'_label'));
                $startDate = Carbon::now()->subWeeks($this->number)->startOfWeek();
                break;
            case ChartTime::MONTH:
                $query->addSelect(DB::raw('DATE_FORMAT('.$this->group_column.', \'%Y-%m\') as '.$this->group_column.'_label'));
                $startDate = Carbon::now()->subMonths($this->number)->startOfMonth();
                break;
            case ChartTime::YEAR:
                $query->addSelect(DB::raw('YEAR('.$this->group_column.') as '.$this->group_column.'_label'));
                $startDate = Carbon::now()->subYears($this->number)->startOfYear();
                break;
        }
        $query->whereRaw($this->group_column.' >= \''.$startDate.'\'');
        $this->group_column = $this->group_column.'_label';
        return $query;
    }

    public function apply_join($query,$subquery,$table,$name){
        parent::apply_join($query,$subquery,$table,$name);
        if($this->cumulative){
            foreach($this->columns as $key=>$column){
                $query->addSelect(DB::raw(
                    'SUM(COALESCE('.$name.'.'.$key.',0)) OVER (ORDER BY last_dates.id ROWS UNBOUNDED PRECEDING) as '.$key.'_cumulative'
                ));
            }
        }
    }
}
