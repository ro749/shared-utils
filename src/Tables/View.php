<?php

namespace Ro749\SharedUtils\Tables;

class View
{
    public string $url;
    //the parameter to pass to the url
    public string $param;

    //the name for the param in the url
    public string $name;

    public bool $full_row = false;

    public function __construct(
        string $url, 
        string $param, 
        string $name,
        bool $full_row = false
    )
    {
        $this->url = $url;
        $this->param = $param;
        $this->name = $name;
        $this->full_row = $full_row;
    }
}