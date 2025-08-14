<?php
namespace Ro749\SharedUtils\Models;
enum Modifier: string
{
    case METERS = 'meters';
    case FOOT = 'foot';
    case MONEY = 'money';
    case DOLARS = 'dolars';
    case PERCENT = 'percent';
    case DATE = 'date';
    case NUMBER = 'number';
    case ENCAPSULATE = 'encapsulate';
}
