<?php
namespace Ro749\SharedUtils\FormRequests;
use Hash;
use Illuminate\Http\Request;

abstract class BaseFormRequest
{
    public string $id;
    public string $model_name;
    public array $formFields;
    public string $submit_text;
    public string $url;
    public string $popup;
    
    public function __construct(string $id, string $model_name, array $formFields = [], string $url = '', string $popup = '',string $submit_text = 'Submit')
    {
        $this->id = $id;
        $this->model_name = $model_name;
        $this->formFields = $formFields;
        $this->url = $url;
        $this->popup = $popup;
        $this->submit_text = $submit_text;
    }

    public function rules(): array
    {
        $rules = [];
        foreach ($this->formFields as $key=>$value) {
           $rules[$key] = $value->get_rules();
        }
        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        foreach ($this->formFields as $key=>$value) {
            if($value->rule === '')continue;
           $messages[$key] = $value->message;
        }
        return $messages;
    }

    public function prosses(Request $rawRequest): string
    {
        //echo json_encode($this->rules());
        $data = $rawRequest->validate($this->rules());
        //echo json_encode($data);
        foreach ($this->formFields as $key => $field) {
            if ($field->type == InputType::PASSWORD) {
                $data[$key] = Hash::make($data[$key]);
            }
        }
        ($this->model_name)::create($data);
        return $this->url;
    }
}