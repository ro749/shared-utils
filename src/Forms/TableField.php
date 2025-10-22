<?php

namespace Ro749\SharedUtils\Forms;
use Ro749\SharedUtils\Tables\BaseTable;
use Illuminate\Validation\Rule;
class TableField extends FormField{
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

    public function get_rules($key,$table,$request): array
    {
        return $this->table->form->rules($request);
    }

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.tables.localSmartTable',[
            "name"=>$name,
            "table"=>$this->table,
        ]);
    }
}