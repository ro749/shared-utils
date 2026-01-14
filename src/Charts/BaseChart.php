<?php

namespace Ro749\SharedUtils\Charts;
use Ro749\SharedUtils\Getters\TimeGetter;
use Illuminate\Support\Facades\Log;
use Ro749\SharedUtils\Statistics\ChartTime;
class BaseChart
{
    public TimeGetter $getter;
    public string $data_column = '';
    public string $label_column = '';

    private array $categories = [];

    //if is inverted in the way of $inverted-x
    public $inverted = false;

    public function __construct(
        TimeGetter $getter, 
        string $data_column,
        string $label_column,
        $inverted = false
    )
    {
        $this->getter = $getter;
        $this->data_column = $data_column;
        $this->label_column = $label_column;
        $this->inverted = $inverted;
    }

    public function get(ChartTime $interval, int $number): array
    {
        $data = $this->getter->get($interval, $number);
        $this->categories = $data[$this->label_column];
        $ans = [];
        if(!empty($this->getter->statistics[array_key_first($this->getter->statistics)]->cumulative)){
            $ans = $data[$this->data_column.'_cumulative'];
        }
        else{
            $ans = $data[$this->data_column];
        }
        if($this->inverted){
            foreach ($ans as $key => $value) {
                $ans[$key] = $this->inverted - $value;
            }
        }
        
        return $ans;
    }

    public function get_series_name(): string
    {
        return array_values($this->getter->columns)[0]->display;
    }

    public function get_categories(): array
    {
        return $this->categories;
    }

    public static function instance(): BaseChart
    {
        $basename = class_basename(static::class);
        return new (config('overrides.charts.'.$basename));
    }

    function get_id(){
        return class_basename($this);
    }
}
