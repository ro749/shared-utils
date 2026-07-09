<?php

namespace Ro749\SharedUtils\Forms;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Selector extends Field
{
    public string $component = 'sharedutils::selector';
    public $options;
    public string $table;
    public string $label_column;
    public string $value_column;
    public bool $search = false;
    public string $hot_reload = '';
    public int $length = 0;

    public string $name;
    public string $form_id;
    public string $data;
    public string $class;
    public float $max_length;
    public bool $accept_new_values = false;

    public function __construct(
        $options, 
        string $id="", 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        Closure|bool $required = false,
        bool $unique = false,
        array $rules=[], 
        array $error_messages=[], 
        string $value = "",
        bool $search = false,
        string $table = "", 
        string $label_column = "", 
        string $value_column = "id",
        bool $autosave = false,
        string $hot_reload = '',
        int $max_length = 0,
        string $name = "",
        string $form_id = "",
        string $data = "",
        string $class = ""
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
            error_messages:$error_messages, 
            value:$value,
            autosave: $autosave
        );

        $this->id = $id;
        if(is_array($options) || $options instanceof \Illuminate\Support\Collection){
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
        $this->max_length = $max_length;
        $this->name = $name;
        $this->form_id = $form_id;
        $this->data = $data;
        $this->class = $class;
    }

    public function get_column(): string
    {
        return $this->table.".".$this->value_column;
    }

    public function render($name="")
    {
        return view('shared-utils::components.forms.selector',[
            'element' => $this,
            'name' => $this->name,
        ]);
    }
}