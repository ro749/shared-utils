<?php
namespace Ro749\SharedUtils\Models;
use Ro749\SharedUtils\Getters\ArrayGetter;
use Ro749\SharedUtils\Tables\BaseTable;
use Ro749\SharedUtils\Tables\Column;
use Ro749\SharedUtils\Filters\BackendFilters\UserFilter;
use Ro749\SharedUtils\Forms\BaseForm;
use Ro749\SharedUtils\Forms\Field;
use Ro749\SharedUtils\Forms\Selector;
use Ro749\SharedUtils\Forms\InputType;
use Illuminate\Support\Facades\Auth;
trait ModelTrait
{
    private function is_editable():bool{
        foreach($this->attributes as $key => $value){
            if(($value->editable == Editable::UPDATE || $value->editable == Editable::ALLWAYS)){
                return true;
            }
        }
        return false;
    }
    public function get_table(BaseTable $table)
    {
        $table->getter = new ArrayGetter($this->getTable(),[]);
        foreach($this->attributes as $key => $value){
            if($value->encrypt) continue;
            $table->getter->columns[$key] = new Column(
                display: $value->label,
                modifier: $value->modifier,
                logic_modifier: $value->logic_modifier
            );
        }
        if(!empty($this->owner) && Auth::guard($this->owner)->check()){
            unset($table->getter->columns[$this->owner]);
            $table->getter->backend_filters[$this->owner] = new UserFilter(
                id: $this->owner,
                column: $this->owner,
                guard: $this->owner
            );
        }
        if($this->is_editable()){
            $table->form = new BaseForm(
                table: $table->getter->table,
            );
            $this->get_edit_form($table->form);
            $table->make_it_modifiable();
        }
        $table->generate_buttons();
        $table->needs_buttons = $table->needsButtons();
    }

    private function get_field($key,$value): Field{
        return match (true) {
            $value->logic_modifier === null => new Field(
                type: $value->input_type ?? InputType::TEXT,
                label: $value->label,
                icon : $value->icon,
                placeholder: $value->placeholder,
                rules: $value->rules,
                encrypt: $value->encrypt,
            ),
            $value->logic_modifier->type === 'options' => new Selector(
                options: $value->logic_modifier->options,
                label: $value->label,
                icon : $value->icon,
                placeholder: $value->placeholder,
                id: $key,
            ),
            $value->logic_modifier->type === 'foreign_key' => Selector::fromDB(
                id: $key,
                table: $value->logic_modifier->table,
                label: $value->label,
                icon : $value->icon,
                placeholder: $value->placeholder,
                label_column: $value->logic_modifier->column,
            ),
            default => throw new \Exception('Unknown logic modifier type'),
        };
    }

    public function get_register_form(BaseForm $form)
    {
        $form->table = $this->getTable();
        foreach($this->attributes as $key => $value){
            if(($value->editable == Editable::CREATE || $value->editable == Editable::ALLWAYS)){
                $form->fields[$key] = $this->get_field($key, $value);
            }
        }
        if(!empty($this->owner) && Auth::guard($this->owner)->check()){
            $form->user = $this->owner;
        }
    }

    public function get_edit_form(BaseForm $form)
    {
        $form->table = $this->getTable();
        foreach($this->attributes as $key => $value){
            if(($value->editable == Editable::UPDATE || $value->editable == Editable::ALLWAYS)){
                $form->fields[$key] = $this->get_field($key, $value);
            }
        }
    }
}
