<?php

namespace Ro749\SharedUtils\Charts;

class TimeChart extends Chart
{

    public function get(ChartGetData $data): array
    {
        $data = $this->getter->get($data->interval, $data->number);
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
}
