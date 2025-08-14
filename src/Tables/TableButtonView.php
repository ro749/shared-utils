<?php

namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Enums\Icon;
use function DI\string;
class TableButtonView extends TableButton
{
    public View $view;
    public function __construct(Icon $icon, string $button_class, string $background_color_class, string $text_color_class, View $view)
    {
        parent::__construct($icon, $button_class, $background_color_class, $text_color_class);
        $this->view = $view;
    }
}