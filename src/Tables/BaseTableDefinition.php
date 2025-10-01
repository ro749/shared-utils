<?php
namespace Ro749\SharedUtils\Tables;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Getters\BaseGetter;
use Ro749\SharedUtils\FormRequests\BaseFormRequest;
use Ro749\SharedUtils\FormRequests\InputType;
use Ro749\SharedUtils\FormRequests\FormField;
use Ro749\SharedUtils\FormRequests\Selector;
use Ro749\SharedUtils\Tables\TableButton;
use Ro749\SharedUtils\Tables\TableButtonView;
use Ro749\SharedUtils\Enums\Icon;
use Illuminate\Http\Request;
use Ro749\SharedUtils\Tables\Texts\TableTexts;
class BaseTableDefinition
{
    public BaseGetter $getter;
    public ?View $view = null;
    public ?Delete $delete = null;
    public ?BaseFormRequest $form;

    public bool $needs_buttons = false;
    public bool $is_editable = false;

    //if clicking edit redirects, if empty normal edit
    public ?View $edit_url = null;
    /* @var TableButton[] $buttons*/
    public array $buttons = [];

    public int $page_length = 0;

    public ?TableTexts $texts = null;
    public function __construct(
        BaseGetter $getter,
        View $view = null, 
        Delete $delete = null,
        BaseFormRequest $form = null,
        View $edit_url = null,
        array $buttons = [],
        int $page_length = 0,
        TableTexts $texts = new TableTexts()
    )
    {
        $this->getter = $getter;
        $this->view = $view;
        $this->delete = $delete;
        $this->form = $form;
        $this->edit_url = $edit_url;
        $this->page_length = $page_length;
        $this->texts = $texts;
        $this->buttons = $buttons;
        if($this->form != null){
            $this->make_it_modifiable();
        }
        $this->generate_buttons();
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
        return count($this->buttons) > 0;//$this->view || $this->delete || $this->is_editable || $this->edit_url;
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
        $filters = $this->getter->filters;
        foreach($this->getter->statistics as $stat){
            $filters = array_merge($filters,$stat->filters);
        }
        return [
            'id' => $this->get_id(),
            'columns' => $this->getter->columns,
            'filters' => $filters,
            'delete' => $this->delete,
            'needs_buttons' => $this->needsButtons(),
            'is_editable' => $this->is_editable,
            'edit_url' => $this->edit_url,
            'buttons' => $this->buttons,
            'form' => $this->form?->get_info(),
            'needs_selectors' => $this->getter->needs_selectors(),
            'order' => $this->get_order(),
            'page_length' => $this->page_length==0?null:$this->page_length,
            'texts' => $this->texts,
            'view' => ($this->view&&$this->view->full_row)?$this->view:null
        ];
    }

    function make_it_modifiable(){
        $this->is_editable = true;
        foreach ($this->form->fields as $key => $field) {
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
        $this->form->fields["id"] = new FormField(
            type: InputType::TEXT,
            rules: ['required', 'integer', 'exists:' . $this->getter->table . ',id'],
        );
        $this->form->fields = array_filter($this->form->fields, function ($field) {
            return $field->type != InputType::PASSWORD && !$field->encrypt;
        });
    }

    function generate_buttons() {
        if($this->view) {
            $this->buttons[] = new TableButtonView(
                icon: Icon::VIEW,
                button_class: "view-btn",
                background_color_class:"bg-primary-light",
                text_color_class: "text-primary-600",
                view: $this->view
            );
        }
        if($this->edit_url) {
            $this->buttons[] = new TableButtonView(
                icon: Icon::EDIT,
                button_class: "edit-btn",
                background_color_class:"bg-success-focus",
                text_color_class:"text-success-main",
                view: $this->edit_url
            );
        }
        if ($this->is_editable) {
            $this->buttons[] = new TableButton(
                icon: Icon::EDIT,
                button_class: "edit-btn",
                background_color_class:"bg-success-focus",
                text_color_class:"text-success-main",
            );
        }
        if ($this->delete) {
            $this->buttons[] = new TableButton(
                icon: Icon::DELETE,
                button_class: "delete-btn",
                background_color_class:"bg-danger-focus",
                text_color_class: "text-danger-main",
            );
        }
    }

    function get_metadata(Request $request){
        foreach ($this->getter->columns as $key => $value) {
            $ans[] = ['key' => $key, 'label' => $value->display];
        }
        return $ans;
    }

    function get_order(){
        $i = 0;
        foreach ($this->getter->columns as $key => $value) {
            if($value->order != ColumnOrder::NONE){
                return [$i, $value->order];
            }
            $i++;
        }
    }

    function get_id(){
        return class_basename($this);
    }
}