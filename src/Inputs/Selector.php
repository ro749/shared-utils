<?php

namespace Ro749\SharedUtils\Inputs;
use Closure;
use Illuminate\View\Component;
use function Laravel\Prompts\search;

class Selector extends Component
{
    public string $id;
    public array $options;
    public int $value;

    public bool $search = false;
    

    public function __construct(string $id, array $options, int $value=-1,bool $search = false)    
    {
        $this->id = $id;
        $this->value = $value;
        $this->options = $options;
        $this->search = $search;
    }

    public static function fromDB(
        string $id, 
        string $modelClass,
        string $labelColumn,
        string $valueColumn = "id",
        Closure $queryModifier = null,
        int $value=-1
    ):self
    {
        $query = $modelClass::query()->select($valueColumn, $labelColumn);

        if ($queryModifier) {
            $query = $queryModifier($query);
        }

        $rows = $query->get();

        $options = [];
        foreach ($rows as $row) {
            $options[$row->$valueColumn] = $row->$labelColumn;
        }

        return new self(id: $id,value: $value, options: $options, search: true);
    }

    public function render()
    {
        return view('shared-utils::components.selector');
    }
}