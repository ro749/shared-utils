<?php
namespace Ro749\SharedUtils\FormRequests;

enum InputType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case ID_NUMBER  = 'id_number';
    case EMAIL = 'email';
    case PHONE = 'tel';
    case PASSWORD = 'password';
    case HIDDEN = 'hidden';
    case SELECTOR= 'selector';
}
?>