<?php
namespace Ro749\SharedUtils\Statistics;

use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BaseStatistic
{
    public string $id;
    //the table of the categories
    public string $category_table;
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

    public string $label;

    public float $row_height;

    public ?Collection $data = null;

    public bool $debug;
    

    public function __construct(string $id, string $category_table, string $category_column, StatisticType $type, string $data_table="", string $data_column="", string $value_column="",array $joins = [], bool $show_zeroes = false, string $label ="",$row_height = 100, $debug = false)
    {
        $this->id = $id;
        $this->category_table = $category_table;
        $this->category_column = $category_column;
        $this->type = $type;
        $this->data_table = $data_table;
        $this->data_column = $data_column;
        $this->value_column = $value_column;
        if($data_table != ""){
            $this->joins = [];
            $this->joins[] = ["table" => $category_table, "column" => $category_column];
            foreach($joins as $join){
                $this->joins[] = $join;
            }
            $this->joins[] = ["table" => $data_table, "column" => $data_column];
        }
        $this->show_zeroes = $show_zeroes;
        $this->label = $label;
        $this->row_height = $row_height;
        $this->debug = $debug;
    }

    public function get_query(){
        $query = DB::table($this->category_table);
        if($this->data_table == "" || $this->data_table == $this->category_table){
            $query->select("id");
            $query->select($this->category_column);
            $query->groupBy($this->category_column);
            $other_column = $this->value_column;
        }
        else{
            $query->select($this->category_table.".".$this->category_column);
            $query->groupBy($this->category_table.".".$this->category_column);
            //$joins = 
            for($i = 0; $i < count($this->joins)-1; $i++){
                $query->join($this->joins[$i+1]['table'], "{$this->joins[$i]["table"]}.id", '=', "{$this->joins[$i+1]['table']}.{$this->joins[$i+1]["column"]}");
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
            $query->selectRaw("sum({$other_column}) as {$this->data_column}");
        }
        if(!($this->data_table == "" || $this->data_table == $this->category_table)){
            $query->selectRaw($this->category_table.".id as id");
            $query->groupBy($this->category_table.".id");
        }
        if(!$this->show_zeroes){
            //$query->having($this->data_column, " >", "0");
        }
        if($this->debug){
            echo "query: " . $query->toSql() . "\n";
        }
        return $query;
    }

    public function get(): mixed
    {
        if($this->data!=null){
            return $this->data;
        }
        $query=$this->get_query();
        $this->data = $query->get();
        return $this->data;//$this->data;
    }

    public function get_labels(): array
    {
        $labels = [];
        $data = $this->get();
        foreach ($data as $key => $item) {
            $labels[] = $item->{$this->category_column};
        }
        return $labels;
    }

    public function get_colors(): array
    {
        $colors = [];
        $data = $this->get();

        foreach ($data as $key => $item) {
            $colors[] = "white";
        }
        return $colors;
    }

    public function get_data(): array
    {
        $data = [];
        $data_items = $this->get();
        foreach ($data_items as $key => $item) {
            $data[] = $item->{$this->data_column};
        }
        return $data;
    }
    
    public function get_height(): float
    {
        return $this->row_height * count($this->get());
    }
}