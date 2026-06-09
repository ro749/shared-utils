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

    public function __construct(
        string $id="", 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        Closure $query_modifier = null,
        Closure|bool $required = false,
        bool $unique = false,
        array $rules=[], 
        string $message="", 
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
            message:$message, 
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
    }

    public function get_table(): string
    {
        return $this->model_class!="" ? (new $this->model_class)->getTable() : $this->table;
    }

    public function get_column(): string
    {
        return $this->get_table().".".$this->value_column;
    }

    public function search($search){
        $query = DB::table($this->get_table());
        if(!empty($this->query_modifier)){
            $query = ($this->query_modifier)($query);
        }
        DB::enableQueryLog();
        $ans = $query->
        where($this->label_column, 'like', '%'.$search.'%')->
        orderByRaw("
        CASE
            WHEN name = ? THEN 1
            WHEN name LIKE ? THEN 2
            WHEN name LIKE ? THEN 3
            WHEN name LIKE ? THEN 4
            WHEN name LIKE ? THEN 5
            ELSE 6
        END
        ", [$search, $search . ' %', $search . '%', '% ' . $search . ' %','% ' . $search . '%'])->
        limit(6)->
        pluck($this->label_column, $this->value_column);
        Log::info(DB::getQueryLog());
        return $ans;
    }


    public function render($name="")
    {
        return view('shared-utils::components.forms.selector-db',[
            'element' => $this,
            'name' => $this->name,
        ]);
    }
}