<?php

namespace Ro749\SharedUtils\Charts;
use Ro749\SharedUtils\Statistics\ChartTime;
use Illuminate\Support\Facades\Log;
class TimeChart extends Chart
{

    public function get(ChartGetData $data = null, $filters = [])
    {
        if($data == null){
            $data = new TimeChartGetData(ChartTime::MONTH, 12);
        }
        $ans = $this->getter->get($data->interval, $data->number, $filters);
        return [
            'data' => $ans,
            'label_column' => $this->label_column,
            'data_column' => $this->data_column

        ];
    }
}
