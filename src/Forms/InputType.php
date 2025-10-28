<?php
namespace Ro749\SharedUtils\Forms;

enum InputType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    //for decimals and big numbers, no arrows
    case NUMBER = 'number';
    //just integers, low numbers, with arrows
    case QUANTITY = 'quantity';
    //for number only texts, no decimals, no arrows and allows leading zeros
    case ID_NUMBER  = 'id_number';
    case EMAIL = 'email';
    case PHONE = 'tel';
    case PASSWORD = 'password';
    case DATE = 'date';
    case HIDDEN = 'hidden';
    case SELECTOR = 'selector';
    case IMAGE = 'image';
    case FILE = 'file';
    case ARRAY = 'array';
}
?>