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

    public function __construct(
        TimeGetter $getter, 
        string $data_column,
        string $label_column
    )
    {
        $this->getter = $getter;
        $this->data_column = $data_column;
        $this->label_column = $label_column;
    }

    public function get(ChartTime $interval, int $number): array
    {
        $data = $this->getter->get($interval, $number);
        $this->categories = $data[$this->label_column];
        return $data[$this->data_column];
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
