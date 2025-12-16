<?php
namespace Ro749\SharedUtils\Forms;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Ro749\SharedUtils\Models\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
class BaseForm
{
    public string $model_class = '';
    public array $fields;
    public string $submit_text;
    public string $redirect='';
    public string $popup='';
    public string $success_msg='';
    public string $submit_url='';
    //if needs to register the loged user, fill with the column, also fill the guard if is not web
    public string $user = '';
    public string $guard = 'web';
    //if is a form an update, rather than register
    public int $db_id = 0;
    public string $callback='';
    public string $uploading_message='';
    public bool $reload = false;

    public $initial_data = null;

    public bool $has_files = false;

    public string $view = '';

    public bool $reset = true;
    public bool $session = false;


    
    public function __construct(
        string $model_class = '', 
        array $fields = [], 
        string $redirect = '', 
        string $popup = 'sharedutils::templates.popup-success',
        string $success_msg = '',
        string $submit_text = 'Submit', 
        string $submit_url = '',
        string $user = '', 
        string $guard = 'web',
        string $callback = '',
        string $uploading_message = '',
        int $db_id = 0,
        bool $reload = false,
        string $view = '',
        bool $reset = true,
        bool $session = false
    )
    {
        $this->model_class = $model_class;
        $this->fields = $fields;
        $this->redirect = $redirect;
        $this->popup = $popup;
        $this->success_msg = $success_msg;
        $this->submit_text = $submit_text;
        $this->submit_url = $submit_url;
        $this->user = $user;
        $this->guard = $guard;
        $this->callback = $callback;
        $this->uploading_message = $uploading_message;
        $this->db_id = $db_id;
        $this->reload = $reload;
        $this->view = $view;
        $this->has_files = $this->get_has_files();
        $this->reset = $reset;
        $this->session = $session;
        
    }

    public function get_table(): string
    {
        if($this->model_class == '') return '';
        return ($this->model_class)::make()->getTable();
    }

    public function get_model(): Model
    {
        return ($this->model_class)::make();
    }

    public function rules($rawRequest): array
    {
        $rules = [];
        $table = $this->get_table();
        foreach ($this->fields as $key=>$value) {
           $value->rules($rules,$key, $table, $rawRequest);
        }
        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        foreach ($this->fields as $key=>$value) {
            if($value->rule === '')continue;
           $messages[$key] = $value->message;
        }
        return $messages;
    }

    public function validate_field(Request $request)
    {
        $data = Arr::dot($request->all());
        $field_key = array_keys($data)[0];
        $pattern = preg_replace('/\.\d+\./', '.*.', $field_key);
        $rules = [
            $pattern => $this->rules($request)[$pattern]
        ];
        return $request->validate($rules);
    }

    public function prosses(Request $rawRequest)
    {
        $data = $rawRequest->validate($this->rules($rawRequest));
        $this->before_process($data);
        if($this->session) {
            foreach ($data as $key => $value) {
                session()->put($key, $value);
            }
            return;
        }
        if($this->db_id!=0) {
            $data['id'] = $this->db_id;
        }
        $arrays = [];
        $forms = [];
        foreach ($this->fields as $key => $field) {
            if(!array_key_exists($key, $data)) {
                if($field->type == InputType::FILE) {
                    
                    if(!$field->autosave){
                        $field->save();
                    }
                    
                }
                if($field->type == InputType::COPY) {
                    $data[$key] = $field->get_value($data);
                }
                continue;
            }
            if($data[$key] == null){
                $data[$key] = '';
                continue;
            }
            if($field->encrypt){
                $data[$key] = Hash::make($data[$key]);
            }
            switch ($field->type) {
                case InputType::PASSWORD:
                    $data[$key] = Hash::make($data[$key]);
                    break;
                case InputType::SESSION:
                    $data[$key] = session()->get($key);
                    break;
                case InputType::COPY:
                    $data[$key] = $field->get_value($data);
                    break;
                case InputType::IMAGE:
                    $file = $rawRequest->file($key);
                    $data[$key] = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $ans = $file->storeAs($field->route, $data[$key], 'public');
                    if($this->db_id!=0){
                        $prev_image = DB::table($this->get_table())->where('id', $this->db_id)->value($key);
                        if ($prev_image != '') {
                            Storage::disk('public')->delete($field->route . $prev_image);
                        }
                    }
                    break;
                case InputType::FILE:
                    
                    if($field->autosave){
                        Log::info('key: '.$key);
                        $file = $rawRequest->file($key);
                        $field->read($file,$key);
                    }
                    break;
                case InputType::FORM:
                    $forms[$key] = $data[$key];
                    unset($data[$key]);
                    break;
                case InputType::ARRAY:
                    $arrays[$key] = $data[$key];
                    unset($data[$key]);
                    break;
            }
        }
        if(!empty($this->model_class)){
            if(isset($data['id'])) {
                $id = $data['id'];
                unset($data['id']);
                $model = $this->model_class::updateOrCreate(['id' => $id], $data);
            } else {
                if ($this->user !== '') {
                    $data[$this->user] = Auth::guard($this->guard)->user()->id;
                }
                $model = $this->model_class::create($data);
            }
            foreach ($forms as $key => $form_data){
                $form_data[$this->fields[$key]->owner_column] = $model->id;
                $this->fields[$key]->form->model_class::create($form_data);
            }
            foreach ($arrays as $key => $array) {
                foreach($array as $value){
                    $value[$this->fields[$key]->owner_column] = $model->id;
                    $this->fields[$key]->table->form->model_class::create($value);
                }
            }
            $ans = $this->after_process($model);
            if($ans != null){
                return $ans;
            }
        }
        return $this->redirect;
    }

    function is_autosave(): bool { 
        foreach ($this->fields as $key => $field) {
            if ($field->autosave) {
                return true;
            }
        }
        return false;
    }

    public function get_has_files(): bool
    {
        foreach ($this->fields as $key => $field) {
            if ($field->type == InputType::IMAGE || $field->type == InputType::FILE) {
                return true;
            }
        }
        return false;
    }

    public function get_info(): array
    {
        return [
            'id' => $this->get_id(),
            'fields' => $this->fields
        ];
    }

    public function get_initial_data()
    {
        if($this->db_id!=0) {
            $query = DB::table($this->get_table());
            $something_selected = false;
            foreach ($this->fields as $key => $field) {
                if($field->type == InputType::PASSWORD) continue;
                if($field->type == InputType::IMAGE) continue;
                $query->addSelect($key);
                $something_selected = true;
            }
            if(!$something_selected) return [];
            $ans = $query->where('id', $this->db_id)->first();
            return $ans;//json_decode(json_encode($ans), true);
        }
        if($this->session) {
            $data = [];
            foreach ($this->fields as $key => $field) {
                if(session()->has($key)){
                    $data[$key] = session()->get($key);
                }
            }
            return $data;
        }
        return $this->initial_data;
    }

    function get_id(){
        return class_basename($this);
    }

    public function before_process(array &$data){}
    public function after_process($model){}

    public static function instanciate(): BaseForm
    {
        $basename = class_basename(static::class);
        return new (config('overrides.forms.'.$basename));
    }

}