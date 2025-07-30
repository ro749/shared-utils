<?php
namespace Ro749\SharedUtils\Models;

use Doctrine\Inflector\Rules\English\Rules;
use Ro749\SharedUtils\Models\Modifier;
use Ro749\SharedUtils\Models\Editable;
use Ro749\SharedUtils\Models\LogicModifiers\LogicModifier;
use Ro749\SharedUtils\FormRequests\InputType;
class Attribute
{
    public string $label;
    public ?Modifier $modifier;
    public ?InputType $input_type;
    public ?LogicModifier $logic_modifier;

    public ?Editable $editable;

    public string $icon;
    public array $rules;
    public string $message;
    public bool $encrypt;

    public function __construct(
        string $label,
        Modifier $modifier =null,
        LogicModifier $logic_modifier = null,
        Editable $editable = null,
        string $owner = "null",
        string $icon = '',
        array $rules = [],
        string $message = '',
        bool $encrypt = false
    ) {
        $this->label = $label;
        $this->modifier = $modifier;
        $this->logic_modifier = $logic_modifier;
        $this->editable = $editable;
        $this->icon = $icon;
        $this->rules = $rules;
        $this->message = $message;
        $this->encrypt = $encrypt;
    }
}