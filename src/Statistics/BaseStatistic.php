<?php
namespace Ro749\SharedUtils\Statistics;

use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Ro749\SharedUtils\Getters\StatisticsGetter;

class BaseStatistic
{
    public string $id;
    //the table of the categories
    public StatisticsGetter $getter;
    public string $label;

    public float $row_height;

    public int $max_elements;

    //the filters to aply as it is in php
    public array $filters;

    public array $backend_filters = [];
    public ?Collection $data = null;
    

    public function __construct(
        string $id, 
        StatisticsGetter $getter,
        string $label ="",
        $row_height = 100,
        $max_elements = 10,
        array $filters = [],
        array $backend_filters = []
    )
    {
        $this->id = $id;
        $this->getter = $getter;
        $this->label = $label;
        $this->row_height = $row_height;
        $this->max_elements = $max_elements;
        $this->filters = $filters;
        $this->backend_filters = $backend_filters;
    }

    public function get(): mixed
    {
        if($this->data!=null){
            return $this->data;
        }
        $this->data =$this->getter->get(
            0,
            $this->max_elements,
            "",
            ["column"=>1,"dir"=>"desc"],
            $this->filters
        )["data"];
        return $this->data;
    }

    public function get_labels(): array
    {
        $labels = [];
        $data = $this->get();
        foreach ($data as $key => $item) {
            $labels[] = $item->{$this->getter->category_column};
        }
        return $labels;
    }

    public function get_colors(): array
    {
        $colors = [];
        $data = $this->get();

        foreach ($data as $key => $item) {
            $colors[] = "black";
        }
        return $colors;
    }

    public function get_data(): array
    {
        $data = [];
        $data_items = $this->get();
        foreach ($data_items as $key => $item) {
            $data[] = $item->{$this->getter->data_column};
        }
        return $data;
    }
    
    public function get_height(): float
    {
        return $this->row_height * count($this->get());
    }
}