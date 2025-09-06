<?php

namespace Ro749\SharedUtils\Tables;

enum ColumnOrder: string
{
    case NONE = '';
    case ASC = 'asc';
    case DESC = 'desc';
}