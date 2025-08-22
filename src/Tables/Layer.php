<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Getters\ArrayGetter;
use Closure;
class Layer{
    public ArrayGetter $getter;
    public string|Closure $title;
    public string $parent;

    public function __construct(
        ArrayGetter $getter,
        string|Closure $title,
        string $parent = ''
    ){
        $this->getter = $getter;
        $this->title = $title;
        $this->parent = $parent;
    }
}