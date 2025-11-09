<?php

namespace Ro749\SharedUtils\Tables;

class ButtonsColumn extends Column{
    /* @var TableButton[] $buttons*/
    public array $buttons = [];

    public function __construct(array $buttons = []) {
        $this->buttons = $buttons;
    }
}