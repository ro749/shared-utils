<?php
namespace Ro749\SharedUtils\FormRequests;
use Illuminate\Http\Request;

abstract class BaseFormRequest
{
    public string $id;
    public string $model_name;
    public array $formFields;
    public string $url;
    public string $popup;
    
    public function __construct(string $id, string $model_name, array $formFields = [], string $url = '', string $popup = '')
    {
        $this->id = $id;
        $this->model_name = $model_name;
        $this->formFields = $formFields;
        $this->url = $url;
        $this->popup = $popup;
    }

    public function rules(): array
    {
        $rules = [];
        foreach ($this->formFields as $key=>$value) {
            if($value->rule === '') continue;
           $rules[$key] = $value->rule;
        }
        return $rules;
    }

    public function messages(): array
    {
        $rules = [];
        foreach ($this->formFields as $key=>$value) {
            if($value->rule === '') continue;
           $rules[$key] = $value->message;
        }
        return $rules;
    }

    public function prosses(Request $rawRequest): string
    {
        $data = $rawRequest->validate($this->rules());
        ($this->model_name)::create($data);
        return $this->url;
    }
}