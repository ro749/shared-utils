<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

abstract class BaseGetter
{
    public string $table;
    public array $filters;
    public array $backend_filters;
    public array $columns;

    function __construct(array $filters = [], array $backend_filters = [],array $columns = [],string $table = '')
    {
        $this->filters = $filters;
        $this->backend_filters = $backend_filters;
        $this->columns = $columns;
        $this->table = $table;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        $search = $search==null?"":$search;
        $ans = [];
        $query = $this->get_query($ans,$search);
        foreach ($this->backend_filters as $filter) {
            $filter->filter($query, $filters);
        }
        $ans['recordsTotal'] = DB::query()->fromSub($query, 'grouped')->count();;
        
        if ($search!="") {
            $this->search($query,$search);
        }
        foreach ($this->filters as $filter) {
            $filter->filter($query, $filters);
        }
        $ans['recordsFiltered'] = DB::query()->fromSub($query, 'grouped')->count();
        $query->orderBy(array_keys($this->columns)[$order['column']], $order['dir']);
        $query->offset($start);
        $query->limit($length);
        $ans['data'] = $query->get();
        
        
        return $ans;
    }

    abstract function get_query(array &$ans,string $search): Builder;

    public function search(Builder $query,string $search){}
}