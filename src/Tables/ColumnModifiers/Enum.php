<?php

namespace Ro749\SharedUtils\Tables\ColumnModifiers;
class Enum extends LogicModifier
{
    public string $options;

    public function __construct(string $options)
    {
        $this->options = $options;
    }

    public function type(): string
    {
        return 'enum';
    }

    public function get_value($key):string{
        return $key;
    }
}