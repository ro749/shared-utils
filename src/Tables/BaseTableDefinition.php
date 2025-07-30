<?php
namespace Ro749\SharedUtils\Tables;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Getters\BaseGetter;
use Ro749\SharedUtils\FormRequests\BaseFormRequest;
use Ro749\SharedUtils\FormRequests\InputType;
use Ro749\SharedUtils\FormRequests\FormField;
use Ro749\SharedUtils\FormRequests\Selector;
class BaseTableDefinition
{
    //the id the table is going to have
    public string $id;

    public BaseGetter $getter;

    public ?View $view = null;
    public ?Delete $delete = null;
    public ?BaseFormRequest $form;

    public bool $needs_buttons = false;
    public bool $is_editable = false;


    public function __construct(
        string $id, 
        BaseGetter $getter,
        View $view = null, 
        Delete $delete = null,
        BaseFormRequest $form = null
    )
    {
        $this->id = $id;
        $this->getter = $getter;
        $this->view = $view;
        $this->delete = $delete;
        $this->form = $form;
        if($this->form != null){
            $this->make_it_modifiable();
        }
        $this->needs_buttons = $this->needsButtons();
        
    }

    public function getColumn(string $key): ?Column
    {
        return $this->getter->columns[$key] ?? null;
    }

    public function getColumnKeys(): array
    {
        return array_keys($this->getter->columns);
    }

    public function needsButtons(): bool
    {
        return $this->view || $this->delete || $this->is_editable;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        return $this->getter->get($start, $length, $search,$order,$filters);
    }

    public function get_selectors()
    {
        return $this->getter->get_selectors();
    }

    function save($request) {
        $this->form->prosses($request);
    }

    public function delete(int $id): void
    {
        DB::table($this->getter->table)->where('id', $id)->delete();
    }

    public function has_edit(): bool
    {
        return $this->form !== null;
    }

    function get_columns(): array {
        return $this->getter->columns;
    }

    function get_info(){
        return [
            'id' => $this->id,
            'table' => $this->getter->table,
            'columns' => $this->getter->columns,
            'filters' => $this->getter->filters,
            'backend_filters' => $this->getter->backend_filters,
            'view' => $this->view,
            'delete' => $this->delete,
            'needs_buttons' => $this->needs_buttons,
            'is_editable' => $this->is_editable,
        ];
    }

    function make_it_modifiable(){
        $this->is_editable = true;
        foreach ($this->form->formFields as $key => $field) {
            if(isset($this->getter->columns[$key])) {
                $this->getter->columns[$key]->editable = true;
            }
            if($field->type == InputType::SELECTOR){
                $field = new Selector(
                    options: $this->getter->columns[$key]->logic_modifier->options ?? []
                );
            }
            if(in_array('unique:' . $this->getter->table . ',' . $key, $field->rules)){
                $field->rules = array_diff($field->rules, ['unique:' . $this->getter->table . ',' . $key]);
            }
            
        }
        $this->form->formFields["id"] = new FormField(
            type: InputType::TEXT,
            rules: ['required', 'integer', 'exists:' . $this->getter->table . ',id'],
        );
        $this->form->formFields = array_filter($this->form->formFields, function ($field) {
            return $field->type != InputType::PASSWORD && !$field->encrypt;
        });
    }
}