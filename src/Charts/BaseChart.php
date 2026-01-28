<?php

namespace Ro749\SharedUtils\Charts;
use Ro749\SharedUtils\Getters\Getter;
use Ro749\SharedUtils\Statistics\ChartTime;

class BaseChart extends Chart
{

    public function get(ChartGetData $data = null): array
    {
        $data = $data==null?new ChartGetData():$data;
        $data = $this->getter->get($data->start, $data->length);
        $this->categories = $data[$this->label_column];
        $ans = [];
        $ans = $data[$this->data_column];
        if($this->inverted){
            foreach ($ans as $key => $value) {
                $ans[$key] = $this->inverted - $value;
            }
        }
        
        return $ans;
    }
}
