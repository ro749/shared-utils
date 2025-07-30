<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
class Options extends LogicModifier
{
    public $options;

    public string $type = 'options';
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function get_value($key):string{
        return $key;
    }
}