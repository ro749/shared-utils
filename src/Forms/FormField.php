<?php

namespace Ro749\SharedUtils\Forms;
use Ro749\SharedUtils\Tables\BaseTable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
class FormField extends Field{
    public BaseForm $form;
    public string $owner_column = '';
    public function __construct(
        BaseForm $form, 
        string $owner_column = '',
    )    
    {
        parent::__construct(
            type: InputType::FORM,
        );
        $this->form = $form;
        $this->owner_column = $owner_column;
    }

    public function rules(&$rules,$key,$table,$request)
    {
        $form_rules = $this->form->rules($request);
        
        foreach ($form_rules as $form_key => $rule) {
            $rules[$key.".".$form_key] = $rule;
        }
    }

    
}