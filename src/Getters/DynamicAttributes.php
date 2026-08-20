<?php

namespace Ro749\SharedUtils\Getters;

class DynamicAttributes{
    public string $table = '';
    public string $parent_id = '';
    public string $value_column = '';
    public string $label_column = '';

    public function __construct(
        string $table = '',
        string $parent_id = '',
        string $value_column = 'value',
        string $label_column = 'attribute'
        ) {
        $this->table = $table;
        $this->parent_id = $parent_id;
        $this->value_column = $value_column;
        $this->label_column = $label_column;
    }

    public function init(string $model_class){
        $model_base_name = strtolower(class_basename($model_class));
        $this->table = !empty($this->table) ? $this->table : $model_base_name.'_attributes';
        $this->parent_id = !empty($this->parent_id) ? $this->parent_id : $model_base_name.'_id';

    }
}