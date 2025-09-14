<?php

namespace Ro749\SharedUtils\FormRequests;
use Closure;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\FormRequests\FormField;

class TextArea extends FormField
{
    public function __construct(
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",
        int $max_length = 0,
        int $min_length = 0,
        bool $encrypt = false,
        bool $autosave = false,
        int $rows = 3,
        int $cols = 50
    ){
        parent::__construct(
            type: InputType::TEXTAREA,
            label:$label, 
            placeholder:$placeholder, 
            icon:$icon,
            rules:$rules, 
            message:$message, 
            value:$value,
            autosave: $autosave
        );

        $this->rows = $rows;
        $this->cols = $cols;
    }

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.forms.textarea', [
            "field"=>$this,
            "name"=>$name,
        ]);
    }
}