<?php

namespace Ro749\SharedUtils\Tables\Texts;

class Aria
{
    public string $orderable;
    public string $orderableReverse;

    public function __construct(
        string $orderable = 'Ordenar por esta columna', 
        string $orderableReverse = 'Invertir el orden de esta columna'
    )
    {
        $this->orderable = $orderable;
        $this->orderableReverse = $orderableReverse;
    }
}