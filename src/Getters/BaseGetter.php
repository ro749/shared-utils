<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Database\Eloquent\Builder;
use Ro749\SharedUtils\Tables\Column;
use Ro749\SharedUtils\Filters\BaseFilter;
use Ro749\SharedUtils\Statistics\Statistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ro749\SharedUtils\Filters\BaseFilters;
use Ro749\SharedUtils\Models\Modifier;
//for when getting data normally from a table
class BaseGetter extends Getter{
    public string $model_class = '';
    public string $query = '';
    function __construct(
        string $query = '',
        string $model_class = '',
        array $columns = [],
        array $statistics = [],
        BaseFilters $filters = null, 
        array $backend_filters = [],
        DynamicAttributes $dynamic_attributes = null,
        bool $debug = false
    )
    {
        parent::__construct(
            $columns, 
            $statistics, 
            $filters, 
            $backend_filters,
            $debug,
            $dynamic_attributes
        );
        $this->model_class = $model_class;
        $this->query = $query;
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
        $end_date = null,
        $editables = []
        )
    {
        if(!empty($this->query)){
            $ans['data'] = DB::select($this->query);
            return $ans;
        } 
        $search = $search==null?"":$search;
        $ans = [];
        $query = $this->get_query($ans,$search,$filters,$editables);
        foreach ($this->backend_filters as $filter) {
            $filter->filter($query, $filters);
        }
        if(!empty($start_date) && !empty($end_date)) {
            $query->whereDateBetween($this->get_table().'.created_at', $start_date, $end_date);
        }
        $ans['recordsTotal'] = $query->get()->count();
        $this->apply_filters($query, $filters);
        
        if ($search!="") {
            $query = $this->search($query,$search);
        }
        $ans['recordsFiltered'] = $query->get()->count();
        if(!empty($order)){
            $column = array_keys($this->columns)[$order['column']];
            if(($this->columns[$column]->modifier == Modifier::METERS ||
                $this->columns[$column]->modifier == Modifier::FOOT ||
                $this->columns[$column]->modifier == Modifier::MONEY ||
                $this->columns[$column]->modifier == Modifier::DOLARS) &&
                $this->columns[$column]->dynamic){
                $query = DB::table($query->toBase(), 'sub')->orderByRaw("CAST(".$column." AS DECIMAL) ".$order['dir']);
                
            }
            else{
                $query->orderBy($column,$order['dir']);
            }
        }
        if(!empty($start)){
            $query->offset($start);
        }
        if($length != -1 ){
            $query->limit($length);
        }
        if($this->debug){
            DB::enableQueryLog();
            $ans['data'] = $query->get();
            Log::debug(DB::getQueryLog());
        }
        else{
            $ans['data'] = $query->get();
        }
        
        return $ans;
    }

    function get_query(array &$ans,string $search,array $filters, array $editables = []): Builder{
        $table = $this->get_table();
        $query = $this->model_class::query()->without(['dynamicAttributes'])->select($table.'.id');
        $joins = [];
        $this->apply_statistics($query,$table,$filters);
        $this->prosses_columns($query,$table,$joins,$search,$editables);
        $this->apply_personalized_joins($query);
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
                $foreign_column = DB::table($modifier->get_table())->select('id',$modifier->column)->get();
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
        //foreach ($this->filters as $filter) {
        //    $filter->filter($query, $filters);
        //}
    }

}