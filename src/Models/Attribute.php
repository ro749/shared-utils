<?php
namespace Ro749\SharedUtils\Models;
class Attribute
{
    public string $label;

    public AttributeType $type;

    public function __construct(
        string $label = '',
        AttributeType $type = AttributeType::TEXT,
    ) {
        $this->label = $label;
        $this->type = $type;
    }
}