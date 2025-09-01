<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Getters\BaseGetter;
use Ro749\SharedUtils\FormRequests\BaseFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class LocalTable extends BaseTableDefinition{
    public string $parent_table = '';
    public string $parent_column = '';
    public string $owner = '';
    
    public function __construct(
        string $id, 
        string $parent_table,
        string $parent_column,
        BaseGetter $getter,
        View $view = null, 
        BaseFormRequest $form = null,
        View $edit_url = null,
        string $owner = ''
    ){
        parent::__construct(
            id: $id, 
            getter: $getter, 
            view: $view, 
            delete: new Delete(warning: ''), 
            form: $form, 
            edit_url: $edit_url,
        );
        $this->parent_table = $parent_table;
        $this->parent_column = $parent_column;
        $this->owner = $owner;
    }
    function save($request) {
        $parent_values = [];
        if(!empty($this->owner)) $parent_values[$this->owner] = Auth::user()->id;
        $parent_id = DB::table($this->parent_table)->insertGetId($parent_values);
        $data = $request->input('data');
        foreach ($data as $key => &$value) {
            $data[$key][$this->parent_column] = $parent_id;
        }
        DB::table($this->getter->table)->insert($data);
    }

    function get_info(){
        return [
            'id' => $this->id,
            'columns' => $this->getter->columns,
            'filters' => $this->getter->filters,
            'delete' => $this->delete,
            'needs_buttons' => $this->needsButtons(),
            'is_editable' => $this->is_editable,
            'edit_url' => $this->edit_url,
            'buttons' => $this->buttons,
            'form' => $this->form?->get_info(),
        ];
    }

    function make_it_modifiable(){
        return;
    }
}