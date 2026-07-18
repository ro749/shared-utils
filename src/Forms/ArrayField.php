<?php

namespace Ro749\SharedUtils\Forms;

use Ro749\SharedUtils\Getters\BaseGetter;
use Ro749\SharedUtils\Tables\BaseTable;
use Ro749\SharedUtils\Tables\Column;
use Ro749\SharedUtils\Tables\Delete;

class ArrayField extends Field
{
    public string $component = 'sharedutils::array-field';
    public SelectorDB $selector;
    public BaseTable $table;
    public function __construct(
        string $model_class = "",
        string $label_column = "", 
        string $value_column = "id",
        string $display_label = ""
    )
    {
        parent::__construct(
            type: InputType::ARRAY,
        );
        $this->selector = new SelectorDB(
            model_class: $model_class,
            label_column: $label_column,
            value_column: $value_column,
        );
        $this->table = new BaseTable(
            getter: new BaseGetter(
                model_class: $model_class,
                columns: [
                    'label' => new Column(display: $display_label),
                ],
            ),
            delete: new Delete(
                warning: 'borrar este elemento?',
            ),
        );
    }

    public function render($name="", $form_id="")
    {
        
        return view('shared-utils::components.forms.array-field',[
            'element' => $this,
            'name' => $name,
            'form_id' => $form_id,
        ]);
    }
}