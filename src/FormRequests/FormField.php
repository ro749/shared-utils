<?php

namespace Ro749\SharedUtils\FormRequests;

class FormField
{
    public string $label;

    public string $placeholder;

    public array $rule;

    public string $message;

    public function __construct(string $label="", string $placeholder="", array $rule=[], string $message="")
    {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->rule = $rule;
        $this->message = $message;
    }

    public function is_required(): bool
    {
        return in_array('required', $this->rule);
    }

    public function get_type(): string
    {
        if (in_array('string', $this->rule)) {
            return 'text';
        } elseif (in_array('integer', $this->rule) || in_array('numeric', $this->rule)) {
            return 'number';
        } elseif (in_array('boolean', $this->rule)) {
            return 'checkbox';
        } elseif (in_array('email', $this->rule)) {
            return 'email';
        } elseif (in_array('phone', $this->rule)) {
            return 'tel';
        }
        return 'text'; // default type
    }
}