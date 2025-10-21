<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Getters\BaseGetter;
use Ro749\SharedUtils\Forms\BaseForm;
class LocalTable extends BaseTable{
    
    public function __construct(
        BaseGetter $getter,
        View $view = null, 
        BaseForm $form = null,
        View $edit_url = null
    ){
        parent::__construct(
            getter: $getter, 
            view: $view, 
            delete: new Delete(warning: ''), 
            form: $form, 
            edit_url: $edit_url,
        );
    }

    function get_info(){
        return [
            'id' => $this->get_id(),
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