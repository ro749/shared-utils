<?php

namespace Ro749\SharedUtils\Forms;
use Ro749\SharedUtils\Tables\BaseTable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
class TableField extends Field{
    public BaseTable $table;
    public string $owner_column = '';
    public function __construct(
        BaseTable $table, 
        string $owner_column = '',
    )    
    {
        parent::__construct(
            type: InputType::ARRAY,
        );
        $this->table = $table;
        $this->owner_column = $owner_column;
    }

    public function rules(&$rules,$key,$table,$request)
    {
        $table_rules = $this->table->form->rules($request);
        
        foreach ($table_rules as $table_key => $rule) {
            $rules[$key.".*.".$table_key] = $rule;
        }
    }

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.tables.localSmartTable',[
            "name"=>$name,
            "table"=>$this->table,
        ]);
    }
}