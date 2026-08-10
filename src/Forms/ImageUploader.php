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
        string $label = '',
        bool $autosave = false,
        string $name = "",
        string $data = "",
        string $class = "")
    {
        parent::__construct(InputType::IMAGE, label: $label, autosave: $autosave);
        $this->route = $route;
        $this->name = $name;
        $this->data = $data;
        $this->class = $class;
    }

    public static function getType(): string
    {
        return 'image';
    }

    public function render($name="")
    {
        return view('shared-utils::components.forms.image-uploader', [
            'element' => $this,
            'name' => $this->name,
        ]);
    }
}