<?php

namespace Ro749\SharedUtils\Forms;

class ImageUploader extends Field
{
    public string $component = 'sharedutils::image-uploader';
    public string $route = '';
    public string $view = '';
    public array $view_data = [];

    public string $name;
    public string $data;
    public string $class;

    public function __construct(
        string $route,
        string $view,
        array $view_data = [],
        bool $autosave = false,
        string $name = "",
        string $data = "",
        string $class = "")
    {
        parent::__construct(InputType::IMAGE,autosave: $autosave);
        $this->route = $route;
        $this->view = $view;
        $this->view_data = $view_data;
        $this->name = $name;
        $this->data = $data;
        $this->class = $class;
    }

    public static function getType(): string
    {
        return 'image';
    }

    public function render()
    {
        return view('shared-utils::components.forms.image-uploader', [
            'element' => $this,
            'name' => $this->name,
        ]);
    }
}