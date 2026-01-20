<?php

namespace Ro749\SharedUtils\Readers;

use Illuminate\Support\Facades\DB;

class DbReader extends Reader
{
    public string $model_class = '';

    public array $required_columns = [];

    public bool $add_new_columns = true;
    public string $migration_text='';

    public array $titles = [];

    public array $types = [];

    public function __construct(
        string $model_class, 
        array $required_columns = [], 
        bool $add_new_columns = false
        )
    {
        $this->model_class = $model_class;
        $this->required_columns = $required_columns;
        $this->add_new_columns = $add_new_columns;
    }

    public function get_table(): string
    {
        if($this->model_class == '') return '';
        return ($this->model_class)::make()->getTable();
    }

    public function check_columns(array &$titles):void{
        $this->migration_text = '';
        foreach ($this->required_columns as $column){
            
            if (!in_array($column, $titles)){
                $this->error_text .= "Column $column is required.";
            }
        }
        if($this->error_text != '') return;

        if(!$this->add_new_columns){
            $columns = DB::getSchemaBuilder()->getColumnListing($this->get_table());
            foreach ($titles as $title){
                if (!in_array($title, $columns)){
                    $this->error_text .= "Column $title is not in table {$this->get_table()}.";
                }
            }
        }
    }

    public function process_data(array &$titles,array &$data):void{
        if($this->add_new_columns){
            $this->migration_text .= "Schema::table('{$this->get_table()}', function (Blueprint \$table) {\n";
            foreach ($titles as $title){
                $this->types[$title] = $this->get_type($title,$data);
                if (!in_array($title, $this->required_columns)){
                    $this->migration_text .= $this->get_text_for_type($title,$this->types[$title]);
                }
            }
            $this->migration_text .= "});\n";
        }
        foreach ($data as $row){
            $this->migration_text .= "DB::table('{$this->get_table()}')->insert([\n";
            foreach ($row as $column => $value){
                if($this->types[$column] == 'int' && $value === ''){
                    $value = 0;
                }
                else if($this->types[$column] == 'float' && $value === ''){
                    $value = 0.0;
                }
                if($this->types[$column] == 'string'){
                    $value = "'".$value."'";
                }
                $this->migration_text .= "'$column' => $value,\n";
            }
            $this->migration_text .= "]);\n";
        }

        $this->migration_text = '<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        '.$this->migration_text.'
    }
};';

        $file = date('Y_m_d_His').'_'.$this->get_table().'_table.php';
        file_put_contents(database_path('migrations/'.$file),$this->migration_text );
        echo "Migration created: ".database_path('migrations/'.$file);
    }

    public function get_type(string $column,array &$data):string{
        $ans = 'int';
        foreach($data as &$row){
            if($row[$column] === '') continue;
            if($ans == 'int'){
                if(!is_numeric($row[$column])){
                    $ans = 'string';
                }
                else if (strpos($row[$column], '.')){
                    $ans = 'float';
                }
            }
            else if($ans == 'float' && is_numeric($row[$column])){
                $ans = 'int';
            }
        }
        return $ans;
    }

    public function get_text_for_type(string $column,string $type): string
    {
        switch ($type) {
            case 'int':
                return "\$table->integer('$column');\n";
            case 'float':
                return "\$table->decimal('$column', 12, 2);\n";
            case 'string':
                return "\$table->string('$column');\n";
        }
        return "";
    }
    
}
