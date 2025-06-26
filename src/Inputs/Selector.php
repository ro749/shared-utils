<?php

namespace Ro749\SharedUtils\Inputs;
use Closure;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\search;

class Selector extends Component
{
    public string $id;
    public array $options;
    public int $value;
    public string $table;

    public string $label_column;

    public string $value_column;

    public bool $search = false;
    

    public function __construct(string $id, array $options, int $value=-1,bool $search = false,string $table = "", string $label_column = "", string $value_column = "id")    
    {
        $this->id = $id;
        $this->value = $value;
        $this->options = $options;
        $this->search = $search;
        $this->table = $table;
        $this->label_column = $label_column;
        $this->value_column = $value_column;
    }

    public static function fromDB(
        string $id, 
        string $table,
        string $label_column,
        string $value_column = "id",
        Closure $queryModifier = null,
        int $value=-1
    ):self
    {
        $query = DB::table($table)->select($value_column, $label_column);

        if ($queryModifier) {
            $query = $queryModifier($query);
        }

        $rows = $query->get();

        $options = [];
        foreach ($rows as $row) {
            $options[$row->$value_column] = $row->$label_column;
        }

        return new self(id: $id,value: $value, options: $options, search: true, table: $table, label_column: $label_column, value_column: $value_column);
    }

    public function get_column(): string
    {
        return $this->table.".".$this->value_column;
    }

    public function render()
    {
        return view('shared-utils::components.selector');
    }
}