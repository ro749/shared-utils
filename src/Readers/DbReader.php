<?php

namespace Ro749\SharedUtils\Readers;

use Illuminate\Support\Facades\DB;

class DbReader extends Reader
{
    public string $table = '';

    public array $required_columns = [];

    public bool $add_new_columns = true;

    public string $migration_text='';

    public array $titles = [];

    public function __construct(string $table, array $required_columns = [], bool $add_new_columns = false)
    {
        $this->table = $table;
        $this->required_columns = $required_columns;
        $this->add_new_columns = $add_new_columns;
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
            $columns = DB::getSchemaBuilder()->getColumnListing($this->table);
            foreach ($titles as $title){
                if (!in_array($title, $columns)){
                    $this->error_text .= "Column $title is not in table $this->table.";
                }
            }
        }
    }

    public function process_data(array &$titles,array &$data):void{
        if($this->add_new_columns){
            $this->migration_text .= "Schema::table('$this->table', function (Blueprint \$table) {\n";
            foreach ($titles as $title){
                if (!in_array($title, $this->required_columns)){
                    $this->migration_text .= $this->get_type($title,$data);
                }
            }
            $this->migration_text .= "});\n";
        }
        foreach ($data as $row){
            $this->migration_text .= "DB::table('$this->table')->insert([\n";
            foreach ($row as $column => $value){
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

        $file = date('Y_m_d_His').'_'.$this->table.'_table.php';
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
}
