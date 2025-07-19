<?php

namespace Ro749\SharedUtils\FormRequests;
use Closure;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\FormRequests\FormField;
use function DI\string;
use function Laravel\Prompts\search;

class Selector extends FormField
{
    public $options;
    public string $table;
    public string $label_column;
    public string $value_column;
    public bool $search = false;
    

    public function __construct(
        $options, 
        string $id="", 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",
        bool $search = false,
        string $table = "", 
        string $label_column = "", 
        string $value_column = "id"
    )    
    {
        parent::__construct(
            type: InputType::SELECTOR,
            label:$label, 
            placeholder:$placeholder, 
            icon:$icon,
            rules:$rules, 
            message:$message, 
            value:$value 
        );

        $this->id = $id;
        if(is_array($options)){
            $this->options = $options;
        }
        else{
            $this->options = config("options.{$options->value}") ?? [];
        }
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

        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",

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

        return new self(
            id: $id,
            value: $value,
            options: $options, 
            search: true, 
            table: $table, 
            label_column: $label_column, 
            value_column: $value_column,

            label:$label, 
            placeholder:$placeholder, 
            icon:$icon, 
            rules:$rules, 
            message:$message
        );
    }

    public function get_column(): string
    {
        return $this->table.".".$this->value_column;
    }

    public function render(string $name)
    {
        return view('shared-utils::components.forms.selector',["selector"=>$this,"name"=>$name]);
    }
}