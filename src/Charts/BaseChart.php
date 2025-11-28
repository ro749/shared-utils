<?php

namespace Ro749\SharedUtils\Charts;
use Ro749\SharedUtils\Getters\TimeGetter;
use Illuminate\Support\Facades\Log;
use Ro749\SharedUtils\Statistics\ChartTime;
class BaseChart
{
    public TimeGetter $getter;

    public function __construct(TimeGetter $getter)
    {
        $this->getter = $getter;
    }

    public function get(ChartTime $interval, int $number): array
    {
        $data = $this->getter->get($interval, $number);
        Log::debug($data);
        return $data;
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
