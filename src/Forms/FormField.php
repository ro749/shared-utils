<?php

namespace Ro749\SharedUtils\Forms;
use Illuminate\Validation\Rule;
class FormField
{
    public InputType $type;
    public string $label;
    public string $placeholder;
    public string $icon;
    /* @var Rule[] $buttons*/
    public array $rules;
    public string $message;
    public string $value;
    public bool $required = false;
    public bool $unique = false;
    public int $max_length;
    public int $min_length;
    public bool $encrypt = false;
    public bool $autosave = false;

    public function __construct(
        InputType $type, 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",
        bool $required = false,
        bool $unique = false,
        int $max_length = 0,
        int $min_length = 0,
        bool $encrypt = false,
        bool $autosave = false
    )
    {
        $this->type = $type;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->rules = $rules;
        $this->message = $message;
        $this->value = $value;
        $this->required = $required;
        $this->unique = $unique;
        $this->max_length = $max_length;
        $this->min_length = $min_length;
        $this->encrypt = $encrypt;
        $this->autosave = $autosave;
    }

    public function is_required(): bool
    {
        return $this->required;
    }

    public function get_rules($key,$table,$request): array
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            $rules[] = $rule->value;
        }
        if($this->required){
            $rules[] = 'required';
        }
        if($this->unique){
            if($request->filled('id')){
                $rules[] = Rule::unique($table, $key)->ignore($$request->input('id'));
            }
            else{
                $rules[] = 'unique:' . $table . ',' . $key;
            }
        }
        if($this->max_length!=0){
            $rules[] = 'max:' . $this->max_length;
        }
        if($this->min_length!=0){
            $rules[] = 'min:' . $this->min_length;
        }
        switch ($this->type) {
            case InputType::EMAIL:
                $rules[] = 'nullable';
                $rules[] = 'email';
                break;
            case InputType::PHONE:
                $rules[] = 'phone:MX';
                break;
            case InputType::ID_NUMBER:
                $rules[] = 'regex:/^\d+$/'; // ID_NUMBER should only contain digits
                break;
        }
        
        if (empty($rules)) {
            return ['nullable'];
        }
        return $rules;
    }

    public function get_type(): InputType
    {
        if ($this->type === InputType::ID_NUMBER) {
            return InputType::TEXT; // ID_NUMBER is treated as TEXT for input purposes
        }
        return $this->type; // default type
    }

    public function render(string $name,string $push,string $data)
    {
        return view('shared-utils::components.forms.field', ["field"=>$this,"name"=>$name,"data"=>$data]);
    }
}