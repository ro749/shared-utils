<?php

namespace Ro749\SharedUtils\Models;

enum AttributeType: string {
    case TEXT = 'text';
    case NUMBER = 'number';
    case DATE = 'date';
    case MONEY = 'money';
    case RELATION = 'relation';

}