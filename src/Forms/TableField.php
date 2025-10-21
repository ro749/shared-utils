<?php

namespace Ro749\SharedUtils\Forms;
use Ro749\SharedUtils\Tables\BaseTable;
class TableField extends FormField{
    public BaseTable $table;
    public function __construct(
        BaseTable $table, 
    )    
    {
        parent::__construct(
            type: InputType::ARRAY,
        );
        $this->table = $table;
    }

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.tables.localSmartTable',[
            "table"=>$this->table,
        ]);
    }
}