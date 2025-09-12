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
    public bool $debug;

    function __construct(array $filters = [], array $backend_filters = [],array $columns = [],string $table = '',$debug = false)
    {
        $this->filters = $filters;
        $this->backend_filters = $backend_filters;
        $this->columns = $columns;
        $this->table = $table;
        $this->debug = $debug;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        $search = $search==null?"":$search;
        $ans = [];
        $query = $this->get_query($ans,$search,$filters);
        foreach ($this->backend_filters as $filter) {
            $filter->filter($query, $filters);
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
        $query->offset($start);
        $query->limit($length);
        if($this->debug){
            $ans['query'] = $query->toSql();
        }
        $ans['data'] = $query->get();
        return $ans;
    }

    abstract function get_query(array &$ans,string $search,array $filters): Builder;

    abstract function search(Builder $query,string $search): Builder;

    public function get_selectors(){
        return [];
    }

    public function needs_selectors(): bool{
        return false;
    }

    abstract function apply_filters($query, $filters);
}