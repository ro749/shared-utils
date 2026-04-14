<?php

namespace Ro749\SharedUtils\Forms;

class TextArea extends Field
{
    public string $component = 'sharedutils::textarea';
    public function __construct(
        string $label="", 
        string $placeholder="", 
        string $icon="", 
        array $rules=[], 
        string $message="", 
        string $value = "",
        bool $autosave = false,
        int $rows = 3,
        int $cols = 50,
        string $name = "",
        string $push = "",
        string $data = "",
    ){
        parent::__construct(
            type: InputType::TEXTAREA,
            label:$label, 
            placeholder:$placeholder, 
            icon:$icon,
            rules:$rules, 
            message:$message, 
            value:$value,
            autosave: $autosave,
            name: $name,
            data: $data
        );

        $this->rows = $rows;
        $this->cols = $cols;
    }

    public function render()
    {
        return view('shared-utils::components.forms.textarea', [
            'element' => $this,
            'name' => $this->name,
        ]);
    }
}