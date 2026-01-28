<?php

namespace Ro749\SharedUtils\Charts;
use Ro749\SharedUtils\Getters\Getter;
use Ro749\SharedUtils\Statistics\ChartTime;

class Chart
{
    public Getter $getter;
    public string $data_column = '';
    public string $label_column = '';

    public array $categories = [];

    //if is inverted in the way of $inverted-x
    public $inverted = false;

    public function __construct(
        Getter $getter, 
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

    public function get(ChartGetData $data): array{
        return [];
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
