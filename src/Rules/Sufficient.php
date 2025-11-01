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
        Log::debug($this->data);
        $exploded_attribute = explode('.', $attribute);
        if (count($exploded_attribute) > 1) {
            $name = $exploded_attribute[0];
            $index = $exploded_attribute[1];
            Log::debug($name);
            Log::debug($index);
            Log::debug($this->id);
            $id = $this->data[$name][$index][$this->id];
        } else {
            $id = $this->data['id'];
        }
        Log::debug($id);
        $current_value = $this->model_class::where('id', $id)->value($this->column);
        if ($value > $current_value) {
            Log::debug($current_value);
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
