<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
use Ro749\SharedUtils\Models\StatisticType;
use Ro749\SharedUtils\Filters\BackendFilters\BasicFilter;
use Closure;
class Statistic extends LogicModifier
{
    public string $table;
    public string $group_column;
    public string $data_column;
    public string $type = 'statistic';
    public StatisticType $statistic_type;
    public string $filter;
    public function __construct(string $table, StatisticType $statistic_type, string $group_column, string $data_column = "",  string $filter = "")
    {
        $this->table = $table;
        $this->group_column = $group_column;
        $this->data_column = $data_column;
        $this->statistic_type = $statistic_type;
        $this->filter = $filter;
    }

    public function get_value($key):string{
        return "";
    }
}