<?php

namespace Ro749\SharedUtils\Charts;
use Illuminate\Support\Facades\Log;

class BaseChart extends Chart
{

    public function get(ChartGetData $data = null, $filters = []): array
    {
        $data = $data==null?new ChartGetData():$data;
        if(empty($data) || empty($data->start)){
            $data = $this->getter->get();
        }
        else{
            $data = $this->getter->get($data->start, $data->length);
        }
        Log::info(json_encode($data, JSON_PRETTY_PRINT));
        if($this->inverted){
            foreach ($data[$this->data_column] as $key => $value) {
                $data[$this->data_column][$key] = $this->inverted - $value;
            }
        }

        return [
            'data' => $data,
            'label_column' => $this->label_column,
            'data_column' => $this->data_column
        ];
    }
}
