<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Database\Eloquent\Builder;
use Ro749\SharedUtils\Tables\Column;
use Ro749\SharedUtils\Filters\BaseFilter;
use Ro749\SharedUtils\Statistics\Statistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class BaseGetter extends Getter{
    public string $model_class = '';

    function __construct(
        string $model_class = '',
        array $columns = [],
        array $statistics = [],
        array $filters = [], 
        array $backend_filters = []
    )
    {
        parent::__construct(
            $columns, 
            $statistics, 
            $filters, 
            $backend_filters
        );
        $this->model_class = $model_class;
    }

    function get_table(): string {
        $model_class = $this->model_class;
        $model = new $model_class();
        return $model->getTable();
    }

    public function get(
        $start=null, 
        $length=null, 
        $search = '',
        $order = [],
        $filters = [], 
        $start_date = null, 
        $end_date = null)
    {
        $search = $search==null?"":$search;
        $ans = [];
        $query = $this->get_query($ans,$search,$filters);
        foreach ($this->backend_filters as $filter) {
            $filter->filter($query, $filters);
        }
        if(!empty($start_date) && !empty($end_date)) {
            $query->whereDateBetween($this->get_table().'.created_at', $start_date, $end_date);
        }
        $ans['recordsTotal'] = $query->count();
        $this->apply_filters($query, $filters);
        
        if ($search!="") {
            $query = $this->search($query,$search);
        }
        $ans['recordsFiltered'] = $query->count();
        if(!empty($order)){
            $query->orderBy(array_keys($this->columns)[$order['column']], $order['dir']);
        }
        if($length != -1){
            $query->offset($start);
            $query->limit($length);
        }
        $ans['data'] = $query->get();
        return $ans;
    }

    function get_query(array &$ans,string $search,array $filters): Builder{
        $table = $this->get_table();
        $query = $this->model_class::query()->select($table.'.id');//DB::table($table)->select($table.'.id');
        $joins = [];
        $this->apply_statistics($query,$table,$filters);
        $this->prosses_columns($query,$table,$joins,$search);
        return $query;
    }

    public function search(Builder $query,string $search): Builder{
        $table = $this->get_table();
        $query->where(function ($query) use ($search,$table) {
            foreach ($this->columns as $key => $column) {
                if ($column->is_foreign()) {
                    $modifier = $column->logic_modifier;
                    $query->orWhereRaw($modifier->get_value($table ,$key)." LIKE ?", ["%{$search}%"]);
                }
                else {
                    $query->orWhere($table . '.' . $key, 'like', '%' . $search . '%');
                }
            }
        });
        return $query;
    }

    public function get_selectors(){
        $ans = [];
        foreach ($this->columns as $key => $column){
            if ($column->is_foreign() && $column->editable) {
                $modifier = $column->logic_modifier;
                $foreign_column = DB::table($modifier->table)->select('id',$modifier->column)->get();
                foreach($foreign_column as $foreign_column_key => $foreign_column_value) {
                    $ans[$key][$foreign_column_value->id] = $foreign_column_value->{$modifier->column};
                }
            }
        }
        return $ans;
    }

    public function needs_selectors(): bool{
        foreach ($this->columns as $key => $column){
            if ($column->is_foreign() && $column->editable) {
                return true;
            }
        }
        return false;
    }

    
    public function apply_filters($query, $filters){
        foreach ($this->filters as $filter) {
            $filter->filter($query, $filters);
        }
    }

}