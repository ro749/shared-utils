<?php
namespace Ro749\SharedUtils\FormRequests;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BaseFormRequest
{
    public string $id;
    public string $table;
    public array $formFields;
    public string $submit_text;
    public string $redirect;
    public string $popup;
    public string $submit_url;
    //if needs to register the loged user, fill with the column
    public string $user;
    public string $callback;
    
    public function __construct(string $id, string $table, array $formFields = [], string $redirect = '', string $popup = '',string $submit_text = 'Submit', string $submit_url = '',string $user = '', string $callback = '')
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
        $data = $rawRequest->validate($this->rules());
        foreach ($this->formFields as $key => $field) {
            if ($field->type == InputType::PASSWORD || $field->encrypt) {
                $data[$key] = Hash::make($data[$key]);
            }
        }
        if ($this->user !== '') {
            $data[$this->user] = Auth::guard($this->user)->user()->id;
        }
        if(isset($data['id'])) {
            DB::table($this->table)->where('id', $data['id'])->update($data);
        } else {
            DB::table($this->table)->insert($data);
        }
        return $this->redirect;
    }
}