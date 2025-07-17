<?php

namespace Ro749\SharedUtils\FormRequests;

class FormField
{
    public InputType $type;
    public string $label;
    public string $placeholder;
    public string $icon;
    public array $rules;
    public string $message;
    public string $value;
    public int $max_length;

    public function __construct(
        InputType $type, 
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",
        int $max_length = 0
    )
    {
        $this->type = $type;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->rules = $rules;
        $this->message = $message;
        $this->value = $value;
        $this->max_length = $max_length;
    }

    public function is_required(): bool
    {
        return in_array('required', $this->rules);
    }

    public function get_rules(): array
    {
        $rules = $this->rules;
        switch ($this->type) {
            case InputType::EMAIL:
                $rules[] = 'email';
                break;
            case InputType::PHONE:
                $rules[] = 'phone:MX';
                break;
        }
        if (empty($rules)) {
            return ['nullable'];
        }
        return $rules;
    }

    public function get_type(): InputType
    {
        return $this->type; // default type
    }

    public function render(string $name)
    {
        return view('shared-utils::components.forms.field', ["field"=>$this,"name"=>$name]);
    }
}