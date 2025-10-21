<?php
namespace Ro749\SharedUtils\Forms;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Ro749\SharedUtils\Models\Model;
class BaseForm
{
    public string $model_class;
    public array $fields;
    public string $submit_text;
    public string $redirect='';
    public string $popup='';
    public string $success_msg='';
    public string $submit_url='';
    //if needs to register the loged user, fill with the column
    public string $user = '';
    //if is a form an update, rather than register
    public int $db_id = 0;
    public string $callback='';
    public string $uploading_message='';
    public bool $reload = false;

    public $initial_data = null;

    public bool $has_images = false;

    public string $view = '';

    public bool $reset = true;


    
    public function __construct(
        string $model_class = '', 
        array $fields = [], 
        string $redirect = '', 
        string $popup = 'sharedutils::templates.popup-success',
        string $success_msg = '',
        string $submit_text = 'Submit', 
        string $submit_url = '',
        string $user = '', 
        string $callback = '',
        string $uploading_message = '',
        int $db_id = 0,
        bool $reload = false,
        string $view = '',
        bool $reset = true
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
        $this->callback = $callback;
        $this->uploading_message = $uploading_message;
        $this->db_id = $db_id;
        $this->reload = $reload;
        $this->view = $view;
        $this->has_images = $this->get_has_images();
        $this->reset = $reset;
        
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
           $rules[$key] = $value->get_rules($key, $table, $rawRequest);
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

    public function prosses(Request $rawRequest): string
    {
        $data = $rawRequest->validate($this->rules($rawRequest));
        $this->before_process($data);
        if($this->db_id) {
            $data['id'] = $this->db_id;
        }
        foreach ($this->fields as $key => $field) {
            if(!isset($data[$key])){
                $data[$key] = '';
                continue;
            }
            if ($field->type == InputType::PASSWORD || $field->encrypt) {
                $data[$key] = Hash::make($data[$key]);
            }
            if ($field->type == InputType::IMAGE) {
                $file = $rawRequest->file($key);
                $data[$key] = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $ans = $file->storeAs($field->route, $data[$key], 'public');
                if($this->db_id!=0){
                    $prev_image = DB::table($this->get_table())->where('id', $this->db_id)->value($key);
                    if ($prev_image != '') {
                        
                        Storage::disk('public')->delete($field->route . $prev_image);
                    }
                }
            }
        }

        if(isset($data['id'])) {
            $model = $this->model_class::update($data, ['id' => $data['id']]);
        } else {
            if ($this->user !== '') {
                $data[$this->user] = Auth::guard($this->user)->user()->id;
            }
            $model = $this->model_class::create($data);
        }

        $this->after_process($model);
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

    public function get_has_images(): bool
    {
        foreach ($this->fields as $key => $field) {
            if ($field->type == InputType::IMAGE) {
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