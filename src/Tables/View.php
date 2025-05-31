<?php

namespace Ro749\SharedUtils\Tables;

class View
{

    //the parameter to pass to the url
    public string $param;

    //the name for the param in the url
    public string $name;

    public function __construct(string $url, string $param, string $name)
    {
        $this->url = $url;
        $this->param = $param;
        $this->name = $name;
    }
}