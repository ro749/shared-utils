<?php
namespace Ro749\SharedUtils\Models;
enum Editable: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case ALLWAYS = 'allways';
}
