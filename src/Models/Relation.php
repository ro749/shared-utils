<?php
namespace Ro749\SharedUtils\Models;
class Relation extends Attribute
{
    public string $model_class = '';
    public string $column = '';
    public function __construct(
        string $label = '',
        string $model_class = '',
        string $column = ''
    ) {
        parent::__construct($label, AttributeType::RELATION);
        $this->model_class = $model_class;
        $this->column = $column;
    }
}