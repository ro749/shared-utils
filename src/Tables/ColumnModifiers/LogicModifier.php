<?php

namespace Ro749\SharedUtils\Tables\ColumnModifiers;
abstract class LogicModifier
{
    abstract public function type(): string;

    abstract public function get_value($key):string;
}