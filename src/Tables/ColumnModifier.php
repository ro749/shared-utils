<?php

namespace Ro749\SharedUtils\Tables;

enum ColumnModifier: string
{
    case METERS = 'meters';
    case FOOT = 'foot';
    case MONEY = 'money';
    case DOLARS = 'dolars';
    case PERCENT = 'percent';
    case DATE = 'date';
}
?>