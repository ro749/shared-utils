<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Getters\BaseGetter;
use Closure;
class Layer{
    public BaseGetter $getter;
    public string|Closure $title;
    public string $parent;

    public function __construct(
        BaseGetter $getter,
        string|Closure $title,
        string $parent = ''
    ){
        $this->getter = $getter;
        $this->title = $title;
        $this->parent = $parent;
    }
}