<?php
namespace Ro749\SharedUtils\FormRequests;

enum InputType: string
{
    case TEXT = 'text';
    case EMAIL = 'email';
    case PHONE = 'tel';
    case PASSWORD = 'password';
}
?>