<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use Ro749\SharedUtils\Statistics\StatisticType;
use Ro749\SharedUtils\Tables\Column;

class StatisticsGetter extends BaseGetter
{
    //the labels of the categories
    public string $category_column;
    public StatisticType $type;
    //the table of the data
    public string $data_table;
    //the column for joining with the category table
    public string $data_column;
    //the value to sum/average
    public string $value_column;
    //for when the data needs various tables in between
    //needs table and column
    //from category to data
    public array $joins;

    public bool $show_zeroes; 
    public bool $debug;
    function __construct(
        string $category_table,
        string $category_column, 
        StatisticType $type, 
        Column $category_column_desc,
        Column $data_column_desc,
        string $data_table="",
        string $data_column="", 
        string $value_column="",
        array $joins = [], 
        bool $show_zeroes = false,
        bool $debug = false, 
        array $filters = [], 
        array $backend_filters = []
    )
    {
        parent::__construct(
            filters:$filters, 
            backend_filters:$backend_filters,
            columns:[
                $category_column => $category_column_desc,
                $data_column => $data_column_desc
            ],
            table: $category_table
        );
        $this->category_column = $category_column;
        $this->type = $type;
        $this->data_table = $data_table;
        $this->data_column = $data_column;
        $this->value_column = $value_column;
        $this->show_zeroes = $show_zeroes;
        $this->debug = $debug;
        $this->joins = [];
        if($data_table != ""){
            $this->joins[] = ["table" => $category_table, "column" => $category_column];
            foreach($joins as $join){
                $this->joins[] = $join;
            }
            $this->joins[] = ["table" => $data_table, "column" => $data_column];
        }
    }

    public function get_query(array &$ans,string $search)  :Builder{
        $query = DB::table($this->table);
        if($this->data_table == "" || $this->data_table == $this->table){
            $query->select("id");
            $query->select($this->category_column);
            $query->groupBy($this->category_column);
            $other_column = $this->value_column;
        }
        else{
            $query->select($this->table.".".$this->category_column);
            $query->groupBy($this->table.".".$this->category_column);
            for($i = 0; $i < count($this->joins)-1; $i++){
                $query->leftJoin($this->joins[$i+1]['table'], "{$this->joins[$i]["table"]}.id", '=', "{$this->joins[$i+1]['table']}.{$this->joins[$i+1]["column"]}");
            }
            $other_column = "{$this->data_table}.{$this->value_column}";
        }
        if($this->type == StatisticType::COUNT){
            $query->selectRaw("count(*) as {$this->data_column}");
        }
        else if($this->type == StatisticType::AVERAGE){
            $query->selectRaw("avg({$other_column}) as {$this->data_column}");
        }
        else if($this->type == StatisticType::TOTAL){
            $query->selectRaw("COALESCE(sum({$other_column}),0) as {$this->data_column}");
        }
        if(!($this->data_table == "" || $this->data_table == $this->table)){
            $query->selectRaw($this->table.".id as id");
            $query->groupBy($this->table.".id");
        }
        if(!$this->show_zeroes){
            //$query->having($this->data_column, " >", "0");
        }
        if($this->debug){
            echo "query: " . $query->toSql() . "\n";
        }
        return $query;
    }
}