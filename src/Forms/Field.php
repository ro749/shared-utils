<?php

namespace Ro749\SharedUtils\Forms;
use Illuminate\Validation\Rule;
use Closure;
class Field
{
    public InputType $type;
    public string $label;
    public string $placeholder;
    public string $icon;
    /* @var Rule[] $buttons*/
    public array $rules;
    public string $message;
    public string $value;
    /**
     * @var Closure(): bool|bool
     */
    public Closure|bool $required = false;
    public bool $unique = false;
    public ?int $max;
    public ?int $min;
    public bool $encrypt = false;
    public bool $autosave = false;
    public bool $sufficient = false;
    public string $field_class = "";

    public function __construct(
        InputType $type, 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",
        Closure|bool $required = false,
        bool $unique = false,
        int $max = null,
        int $min = null,
        bool $encrypt = false,
        bool $autosave = false,
        bool $sufficient = false,
        string $field_class = ""
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
        $this->max = $max;
        $this->min = $min;
        $this->encrypt = $encrypt;
        $this->autosave = $autosave;
        $this->sufficient = $sufficient;
        $this->field_class = $field_class;
    }

    public function is_required(): bool
    {
        return is_bool($this->required) && $this->required;
    }

    public function rules(&$rules,$key,$table,$request){
        $rules[$key] = $this->get_rules($key,$table,$request);
    }

    public function get_rules($key,$table,$request): array
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            $rules[] = $rule;
        }
        if (is_bool($this->required)) {
            if($this->required){
                $rules[] = 'required';
            }
        }
        else{
            if(($this->required)($request)){
                $rules[] = 'required';
            }        
        }
        if($this->unique){
            if($request->filled('id')){
                $rules[] = Rule::unique($table, $key)->ignore($request->input('id'));
            }
            else{
                $rules[] = 'unique:' . $table . ',' . $key;
            }
        }
        if($this->max!=0){
            $rules[] = 'max:' . $this->max;
        }
        if($this->min!=0){
            $rules[] = 'min:' . $this->min;
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
        if ($this->type === InputType::ID_NUMBER||$this->type === InputType::PERCENTAGE || $this->type === InputType::MONEY) {
            return InputType::TEXT; // ID_NUMBER is treated as TEXT for input purposes
        }
        return $this->type; // default type
    }

    public function render(string $name,string $push,string $data)
    {
        return view('shared-utils::components.forms.field', ["field"=>$this,"name"=>$name,"data"=>$data]);
    }
}