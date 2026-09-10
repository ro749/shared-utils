<?php

namespace Ro749\SharedUtils\Forms;

class ImageUploader extends Field
{
    public string $component = 'sharedutils::image-uploader';
    public string $route = '';
    public string $name;
    public string $data;
    public string $class;
    public string $imageUrl;

    public function __construct(
        string $route,
        string $label = '',
        bool $autosave = false,
        string $name = "",
        string $data = "",
        string $class = "",
        string $imageUrl = ""
    )
    {
        parent::__construct(InputType::IMAGE, label: $label, autosave: $autosave);
        $this->route = $route;
        $this->name = $name;
        $this->data = $data;
        $this->class = $class;
        $this->imageUrl = $imageUrl;
    }

    public static function getType(): string
    {
        return 'image';
    }
}