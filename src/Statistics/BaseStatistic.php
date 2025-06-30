<?php
namespace Ro749\SharedUtils\Statistics;

use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BaseStatistic
{
    public string $id;
    public string $category_table;
    public string $category_column;
    public StatisticType $type;
    public string $data_table;
    public string $data_column;
    public bool $show_zeroes; 

    public string $label;

    public float $row_height;

    public ?Collection $data = null;

    public bool $debug;
    

    public function __construct(string $id, string $category_table, string $category_column, StatisticType $type, string $data_table="", string $data_column="", bool $show_zeroes = false, string $label ="",$row_height = 100, $debug = false)
    {
        $this->id = $id;
        $this->category_table = $category_table;
        $this->category_column = $category_column;
        $this->type = $type;
        $this->data_table = $data_table;
        $this->data_column = $data_column;
        $this->show_zeroes = $show_zeroes;
        $this->label = $label;
        $this->row_height = $row_height;
        $this->debug = $debug;
    }

    public function get_query(){
        $query = DB::table($this->category_table);
        if($this->data_table == "" || $this->data_table == $this->category_table){
            $query->select($this->category_column);
            $query->groupBy($this->category_column);
            $other_column = $this->data_column;
        }
        else{
            $query->select($this->category_table.".".$this->category_column);
            $query->groupBy($this->category_table.".".$this->category_column);
            $query->join($this->data_table, "{$this->category_table}.id", '=', "{$this->data_table}.{$this->data_column}");
            $other_column = "{$this->data_table}.{$this->data_column}";
        }
        if($this->type == StatisticType::COUNT){
            $query->selectRaw("count({$other_column}) as {$this->data_column}");
        }
        else if($this->type == StatisticType::AVERAGE){
            $query->selectRaw("avg({$other_column}) as {$this->data_column}");
        }
        else if($this->type == StatisticType::TOTAL){
            $query->selectRaw("sum({$other_column}) as {$this->data_column}");
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