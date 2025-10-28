<?php

namespace Ro749\SharedUtils\Forms;
use Closure;
use Illuminate\Support\Facades\DB;

class Selector extends FormField
{
    public $options;
    public string $table;
    public string $label_column;
    public string $value_column;
    public bool $search = false;
    public string $hot_reload = '';

    public function __construct(
        $options, 
        string $id="", 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        Closure|bool $required = false,
        bool $unique = false,
        array $rules=[], 
        string $message="", 
        string $value = "",
        bool $search = false,
        string $table = "", 
        string $label_column = "", 
        string $value_column = "id",
        bool $autosave = false,
        string $hot_reload = ''
    )    
    {
        parent::__construct(
            type: InputType::SELECTOR,
            label:$label, 
            placeholder:$placeholder, 
            icon:$icon,
            required:$required,
            unique:$unique,
            rules:$rules, 
            message:$message, 
            value:$value,
            autosave: $autosave
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
        $this->hot_reload = $hot_reload;
    }

    public static function fromDB(
        string $id, 
        string $table,
        string $label_column,
        string $value_column = "id",
        Closure $query_modifier = null,
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        Closure|bool $required = false,
        bool $unique = false,
        array $rules=[], 
        string $message="", 
        string $value = "",
        bool $autosave = false,
        string $hot_reload = ''
    ):self
    {
        $query = DB::table($table)->select($value_column, $label_column);
        if ($query_modifier) {
            $query_modifier($query);
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
            required:$required,
            unique:$unique,
            rules:$rules, 
            message:$message,
            autosave: $autosave,
            hot_reload: $hot_reload
        );
    }

    public function get_column(): string
    {
        return $this->table.".".$this->value_column;
    }

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.forms.selector',[
            "selector"=>$this,
            "name"=>$name,
            "push_init"=>$push,
            "push_reset"=>$push.'_reset',
            "hot_reload"=>$this->hot_reload,
            "value"=>$data
        ]);
    }
}