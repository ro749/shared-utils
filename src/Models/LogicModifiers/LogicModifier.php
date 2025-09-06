<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
abstract class LogicModifier
{

    abstract public function get_value(string $table, string $key):string;
}