<?php

namespace Ro749\SharedUtils\Forms;

class CopyField extends Field
{   
    public string $model_class = '';
    public string $column = '';
    public string $id = '';
    public function __construct(
        string $model_class, 
        string $column,
        string $id
        )
    {
        parent::__construct(type: InputType::COPY);
        $this->model_class = $model_class;
        $this->column = $column;
        $this->id = $id;
        
    }
    public function get_value($data): string
    {
        return ($this->model_class)::where(['id'=>$data[$this->id]])->value($this->column);
    }
}