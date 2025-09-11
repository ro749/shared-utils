<?php

namespace Ro749\SharedUtils\FormRequests;

class ImageUploader extends FormField
{
    public string $route = '';
    public string $view = '';
    public array $view_data = [];

    public function __construct(string $route, string $view, array $view_data = [], bool $autosave = false)
    {
        parent::__construct(InputType::IMAGE,autosave: $autosave);
        $this->route = $route;
        $this->view = $view;
        $this->view_data = $view_data;
    }

    public static function getType(): string
    {
        return 'image';
    }

    public function render(string $name,string $push = "")
    {
        return view($this->view , ["field"=>$this,"name"=>$name, "data"=>$this->view_data]);
    }
}