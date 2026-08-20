<?php

namespace Ro749\SharedUtils\Readers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Ro749\SharedUtils\Readers\MigrationHelper;
class DbUpdate extends DBRead
{
    public string $public_id = '';
    public bool $debug = false;

    public function __construct(
        string $table = '',
        string $model_class = '', 
        string $public_id = '',
        array $required_columns = [], 
        bool $add_new_columns = false,
        bool $debug = false,
    )
    {
        parent::__construct(
            table: $table,
            model_class: $model_class,
            required_columns: $required_columns,
            add_new_columns: $add_new_columns
        );
        $this->public_id = $public_id;
        $this->debug = $debug;
    }

    public function check_columns(array &$titles):void{
        $this->migration_text = '';
        if ($this->debug)
        {
            $titlesPretty = json_encode($titles, JSON_PRETTY_PRINT);
            Log::debug("Titles: $titlesPretty");
        }
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
        if(empty($this->public_id)){
            $this->public_id = $titles[0];
        }
        if($this->add_new_columns){
            $this->migration_text .= "Schema::table('{$this->get_table()}', function (Blueprint \$table) {\n";
            foreach ($titles as $title){
                if (!in_array($title, $this->required_columns)){
                    $this->types[$title] = $this->get_type($title,$data);
                    if(Schema::hasColumn($this->get_table(), $title)) continue;
                    if (!in_array($title, $this->required_columns)){
                        $this->migration_text .= $this->get_text_for_type($title,$this->types[$title]);
                    }
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
        MigrationHelper::create_migration_file($this->get_table().'_table', $this->migration_text);
    }

    public function save_changes(){

    }
}
