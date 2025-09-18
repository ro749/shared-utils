<?php

namespace Ro749\SharedUtils\Tables\Texts;

class Paginate
{
    public string $first;
    public string $last;
    public string $next;
    public string $previous;

    public function __construct(
        string $first = 'Primero', 
        string $last = 'Ultimo', 
        string $next = 'Siguiente', 
        string $previous = 'Anterior'
    )
    {
        $this->first = $first;
        $this->last = $last;
        $this->next = $next;
        $this->previous = $previous;
    }
}