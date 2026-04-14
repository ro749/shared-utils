<?php

namespace Ro749\SharedUtils\Forms;
use Closure;
use Illuminate\Support\Facades\DB;

class RadioButtons extends Selector
{
    public string $component = 'sharedutils::radio-buttons';
    public string $button_view;
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
        string $hot_reload = '',
        string $button_view = 'shared-utils::components.forms.radio-button',
        string $name = "",
        string $push = "",
        string $data = "",
    )    
    {
        parent::__construct($options,$id,$label,$placeholder,$icon,$required,$unique,$rules,$message,$value,$search,$table,$label_column,$value_column,$autosave,$hot_reload,$name,$push,$data);
        $this->button_view = $button_view;
    }

    public function render()
    {
        return view('shared-utils::components.forms.radio-buttons',[
            'element' => $this,
            'name' => $this->name,
        ]);
    }
}