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
    case PERCENTAGE = 'percentage';
    //for money, formated with commas, 2 decimals, and a $ at front
    case MONEY = 'money';
    case EMAIL = 'email';
    case PHONE = 'tel';
    case PASSWORD = 'password';
    case DATE = 'date';
    case HIDDEN = 'hidden';
    case CHECKBOX = 'checkbox';
    //for data that is in the session
    case SESSION = 'session';
    case COPY = 'copy';
    case SELECTOR = 'selector';
    case IMAGE = 'image';
    case FILE = 'file';
    case FORM = 'form';
    case ARRAY = 'array';
}
?>