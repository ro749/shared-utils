<?php

namespace Ro749\SharedUtils\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
class Sufficient implements DataAwareRule, ValidationRule
{
    public string $model_class;
    public string $id = 'id';
    public string $column;

    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];
    public function __construct(string $model_class, string $id, string $column)
    {
        $this->model_class = $model_class;
        $this->id = $id;
        $this->column = $column;
    }
 
    // ...
 
    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
 
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exploded_attribute = explode('.', $attribute);
        if (count($exploded_attribute) > 1) {
            $name = $exploded_attribute[0];
            $index = $exploded_attribute[1];
            $id = $this->data[$name][$index][$this->id];
        } else {
            if (array_key_exists($this->id, $this->data)) {
                $id = $this->data[$this->id];
            } else {
                $id = null;
            }
        }
        $current_value = $this->model_class::where($this->id, $id)->value($this->column);
        if ($value > $current_value) {
            $fail('No hay sufficientes. Maximo '.$current_value );
        }
    }
    /**
     * Convert the rule to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'sufficient';
    }
}
