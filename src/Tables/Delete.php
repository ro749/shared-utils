<?php

namespace Ro749\SharedUtils\Tables;

class Delete
{
    //warning: the warning message to show before deleting
    public string $warning;

    public function __construct(string $warning)
    {
        $this->warning = $warning;
    }
}