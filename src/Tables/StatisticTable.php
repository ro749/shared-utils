<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Statistics\BaseStatistic;

use Illuminate\Support\Facades\DB;

class StatisticTable extends BaseTableDefinition
{
    public BaseStatistic $statistic;
    public function __construct(
        string $id,
        BaseStatistic $statistic,
        Column $category_column_desc,
        Column $data_column_desc,
        View $view = null, 
        Delete $delete = null, 
        array $filters = [],
        array $backend_filters = []
    ){
        $this->statistic = $statistic;
        parent::__construct(
            id: $id,
            table: $statistic->category_table,
            columns: [
                $statistic->category_column => $category_column_desc,
                $statistic->data_column => $data_column_desc
            ],
            view: $view,
            delete: $delete,
            filters: $filters,
            backend_filters: $backend_filters
        );
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        $query = $this->statistic->get_query();
        foreach ($this->backend_filters as $filter) {
            $filter->filter($query, $filters);
        }
        $ans['recordsTotal'] = DB::query()->fromSub($query, 'grouped')->count();
        
        if ($search) {
            $query->where(function ($query) use ($search) {
                foreach ($this->columns as $key => $column) {
                    if ($column->is_foreign()) {
                        $query->orWhere($column->table . '.' . $column->column, 'like', '%' . $search . '%');
                    }
                    else {
                        $query->orWhere($this->table . '.' . $key, 'like', '%' . $search . '%');
                    }
                }
                
            });
        }
        foreach ($this->filters as $filter) {
            $filter->filter($query, $filters);
        }
        $ans['recordsFiltered'] = DB::query()->fromSub($query, 'grouped')->count();
        
        $ans['data'] = $query->get(); //$query->orderBy(array_keys($this->columns)[$order['column']], $order['dir'])->offset($start)->limit($length)->get();
        
        
        return $ans;
    }
}