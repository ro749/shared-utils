<?php

namespace Ro749\SharedUtils\Forms;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelectorDB extends Field
{
    public string $component = 'sharedutils::selector-db';
    public string $table;
    public string $model_class;
    public string $label_column;
    public string $value_column;
    public string $hot_reload = '';
    public int $length = 0;
    public string $name;
    public string $form_id;
    public string $data;
    public string $class;
    public float $max_length;
    public ?Closure $query_modifier;
    public $options;

    public bool $accept_new_values = false;

    public SelectorType $selector_type = SelectorType::Static;

    public function __construct(
        string $id="", 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        Closure $query_modifier = null,
        Closure|bool $required = false,
        bool $unique = false,
        array $rules=[], 
        array $error_messages=[], 
        string $value = "",
        string $table = "", 
        string $model_class = "",
        string $label_column = "", 
        string $value_column = "id",
        bool $autosave = false,
        string $hot_reload = '',
        int $max_length = 0,
        string $name = "",
        string $form_id = "",
        string $data = "",
        string $class = "",
        SelectorType $selector_type = SelectorType::Smart
    )    
    {
        parent::__construct(
            type: InputType::SELECTOR_DB,
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
        $this->table = $table;
        $this->label_column = $label_column;
        $this->value_column = $value_column;
        $this->hot_reload = $hot_reload;
        $this->max_length = $max_length;
        $this->name = $name;
        $this->form_id = $form_id;
        $this->data = $data;
        $this->class = $class;
        $this->model_class = $model_class;
        $this->query_modifier = $query_modifier;
        $this->selector_type = $selector_type;
    }

    public function get_table(): string
    {
        return $this->model_class!="" ? (new $this->model_class)->getTable() : $this->table;
    }

    public function get_column(): string
    {
        return $this->get_table().".".$this->value_column;
    }

    public function generate_options()
    {
        $query = DB::table($this->get_table());
        if(!empty($this->query_modifier)){
            $query = ($this->query_modifier)($query);
        }
        $this->options = $query->pluck($this->label_column, $this->value_column)->toArray();
        $this->search = true;
    }

    public function search($search, $request){
        $query = DB::table($this->get_table());
        if(!empty($this->query_modifier)){
            $query = ($this->query_modifier)($query, $request);
        }
        $ans = $query->
        where($this->label_column, 'like', '%'.$search.'%')->
        orderByRaw("
        CASE
            WHEN ".$this->label_column." = ? THEN 1
            WHEN ".$this->label_column." LIKE ? THEN 2
            WHEN ".$this->label_column." LIKE ? THEN 3
            WHEN ".$this->label_column." LIKE ? THEN 4
            WHEN ".$this->label_column." LIKE ? THEN 5
            ELSE 6
        END
        ", [$search, $search . ' %', $search . '%', '% ' . $search . ' %','% ' . $search . '%'])->
        limit(6)->
        pluck($this->label_column, $this->value_column);
        return $ans;
    }
    //if smart decide if its static or dynamic
    public function decide(){
        $query = DB::table($this->get_table());
        if(!empty($this->query_modifier)){
            $query = ($this->query_modifier)($query);
        }
        $count =$query->count();
        $this->selector_type = $count > 216 ? SelectorType::Dynamic : SelectorType::Static;
    }


    public function render($name="")
    {
        return view('shared-utils::components.forms.selector-db',[
            'element' => $this,
            'name' => $this->name,
        ]);
    }

    public function get_info(){
        return [
            'type' => $this->type,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'icon' => $this->icon,
            'required' => $this->required,
            'unique' => $this->unique,
            'value' => $this->value,
            'hot_reload' => $this->hot_reload,
        ];
    }
}