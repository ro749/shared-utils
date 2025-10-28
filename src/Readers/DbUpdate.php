<?php

namespace Ro749\SharedUtils\Readers;

use Illuminate\Support\Facades\DB;

class DbUpdate extends DbReader
{
    public string $public_id = '';
    public function __construct(
        string $model_class, 
        string $public_id,
        array $required_columns = [], 
        bool $add_new_columns = false
    )
    {
        parent::__construct(
            model_class: $model_class,
            required_columns: $required_columns,
            add_new_columns: $add_new_columns
        );
        $this->public_id = $public_id;
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
                if (!in_array($title, $this->required_columns)){
                    $this->migration_text .= $this->get_type($title,$data);
                }
            }
            $this->migration_text .= "});\n";
        }
        foreach ($data as $row){
            $this->migration_text .= "DB::table('{$this->get_table()}')->where('".$this->public_id."', '".$row[$this->public_id]."')->update([\n";
            foreach ($row as $column => $value){
                if($column == $this->public_id) continue;
                $this->migration_text .= "'$column' => '$value',\n";
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
        switch ($ans) {
            case 'int':
                return "\$table->integer('$column');\n";
            case 'float':
                return "\$table->decimal('$column', 12, 2);\n";
            case 'string':
                return "\$table->string('$column');\n";
        }
        return "";
    }

    public function save_changes(){

    }
}
