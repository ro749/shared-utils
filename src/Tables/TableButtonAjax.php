<?php

namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Enums\Icon;
use function DI\string;
class TableButtonAjax extends TableButton
{
    public string $url;
    public string $warning;
    public string $warning_popup;
    public string $success;
    public string $success_popup;
    public bool $reload;
    
    
    public function __construct(
        Icon $icon, 
        string $button_class, 
        string $background_color_class, 
        string $text_color_class, 
        string $url,
        string $warning = '',
        string $warning_popup = 'sharedutils::templates.popup-warning',
        string $success = '',
        string $success_popup = 'sharedutils::templates.popup-success',
        bool $reload = true
    )
    {
        parent::__construct(
            $icon, 
            $button_class, 
            $background_color_class, 
            $text_color_class
        );
        $this->url = $url;
        $this->warning = $warning;
        $this->warning_popup = $warning_popup;
        $this->reload = $reload;
        $this->success = $success;
        $this->success_popup = $success_popup;
    }
}