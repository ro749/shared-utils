<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Getters\BaseGetter;
use Ro749\SharedUtils\FormRequests\BaseFormRequest;
use Illuminate\Support\Facades\DB;
class LocalTable extends BaseTableDefinition{
    public function __construct(
        string $id, 
        BaseGetter $getter,
        View $view = null, 
        BaseFormRequest $form = null,
        View $edit_url = null,
        
    ){
        parent::__construct(
            id: $id, 
            getter: $getter, 
            view: $view, 
            delete: new Delete(warning: ''), 
            form: $form, 
            edit_url: $edit_url,
        );
    }
    function save($request) {
        $data = $request->input('data');
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