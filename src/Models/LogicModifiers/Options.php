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

    public function  get_value(string $table, string $key):string{
        return $key;
    }

    public function get_table(): string {
        return '';
    }
}