<?php

namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Eloquent\Builder; 
use Illuminate\View\View;
use Ro749\SharedUtils\Forms\BaseForm;
class BaseFilter extends BaseForm
{
    public function __construct(
        string $model_class = '', 
        string $redirect = '', 
        string $popup = 'sharedutils::templates.popup-success', 
        string $success_msg = '',  
        string $submit_url = '', 
        string $user = '', 
        string $guard = 'web', 
        string $callback = '', 
        string $uploading_message = '', 
        int $db_id = 0, 
        bool $reload = false, 
        string $view = '', 
        bool $reset = true, 
        bool $soft_reload = false, 
        bool $session = false, 
        bool $debug = false
    )
    {
        parent::__construct(
            submit_text: '',
            autosave: true,
        );
    }
}