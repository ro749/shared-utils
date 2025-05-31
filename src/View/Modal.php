<?php

namespace Ro749\SharedUtils\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public string $id;
    public string $onclose;

    public function __construct(string $id, string $onclose = '')
    {
        $this->id = $id;
        $this->onclose = $onclose;
    }

    public function render()
    {
        return view('shared-utils::components.modal');
    }
}