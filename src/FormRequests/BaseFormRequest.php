<?php
namespace Ro749\SharedUtils\FormRequests;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class BaseFormRequest
{
    public string $id;
    public string $table;
    public array $formFields;
    public string $submit_text;
    public string $redirect;
    public string $popup;
    public string $submit_url="";
    //if needs to register the loged user, fill with the column
    public string $user;
    public string $callback;
    public string $uploading_message='';
    
    public function __construct(
        string $id, 
        string $table, 
        array $formFields = [], 
        string $redirect = '', 
        string $popup = '',
        string $submit_text = 'Submit', 
        string $submit_url = '',
        string $user = '', 
        string $callback = '',
        string $uploading_message = ''
    )
    {
        $this->id = $id;
        $this->table = $table;
        $this->formFields = $formFields;
        $this->redirect = $redirect;
        $this->popup = $popup;
        $this->submit_text = $submit_text;
        $this->submit_url = $submit_url;
        $this->user = $user;
        $this->callback = $callback;
        $this->uploading_message = $uploading_message;
    }

    public function rules($rawRequest): array
    {
        if($rawRequest->filled('id')) {
            foreach ($this->formFields as $key => $field) {
                if (in_array('unique', $field->rules)) {
                    foreach ($field->rules as $index => $rule) {
                        if ($rule === 'unique') {
                            $field->rules[$index] = Rule::unique($this->table, $key)->ignore($rawRequest->input('id'));
                        }
                    }
                }
            }
        } else {
            foreach ($this->formFields as $key => $field) {
                if (in_array('unique', $field->rules)) {
                    foreach ($field->rules as $index => $rule) {
                        if ($rule === 'unique') {
                            $field->rules[$index] = "unique:{$this->table},{$key}";
                        }
                    }
                }
            }
        }
        
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
        $data = $rawRequest->validate($this->rules($rawRequest));
        foreach ($this->formFields as $key => $field) {
            if ($field->type == InputType::PASSWORD || $field->encrypt) {
                $data[$key] = Hash::make($data[$key]);
            }
        }
        
        if(isset($data['id'])) {
            DB::table($this->table)->where('id', $data['id'])->update($data);
        } else {
            if ($this->user !== '') {
                $data[$this->user] = Auth::guard($this->user)->user()->id;
            }
            DB::table($this->table)->insert($data);
        }
        return $this->redirect;
    }

    public function get_info(): array
    {
        return [
            'fields' => $this->formFields
        ];
    }
}