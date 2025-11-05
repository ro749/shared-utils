<?php

namespace Ro749\SharedUtils\Forms;

class ImageUploader extends Field
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

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.forms.image-uploader' , ["field"=>$this,"name"=>$name,"data"=>$data]);
    }
}