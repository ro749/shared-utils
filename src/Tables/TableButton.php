<?php

namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Enums\Icon;
use function DI\string;
class TableButton
{
    //what is going to display in the frontend table
    public Icon $icon;
    public string $button_class;
    public string $background_color_class;
    public string $text_color_class;
    public function __construct(Icon $icon, string $button_class, string $background_color_class, string $text_color_class)
    {
        $this->icon = $icon;
        $this->button_class = $button_class;
        $this->background_color_class = $background_color_class;
        $this->text_color_class = $text_color_class;
    }
}