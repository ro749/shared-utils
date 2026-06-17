<?php

namespace Ro749\SharedUtils\Readers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DbReader extends Reader
{
    public string $model_class = '';
    public string $table = '';

    public array $required_columns = [];

    public bool $add_new_columns = true;
    public string $migration_text='';

    public array $titles = [];

    public array $types = [];

    public function get_table(): string
    {
        if($this->table != '') return $this->table;
        if($this->model_class == '') return '';
        return ($this->model_class)::make()->getTable();
    }

    public function __construct(
        string $model_class = "", 
        string $table = '',
        array $required_columns = [], 
        bool $add_new_columns = false
        )
    {
        $this->model_class = $model_class;
        $this->table = $table;
        $this->required_columns = $required_columns;
        $this->add_new_columns = $add_new_columns;
    }

    public function process_data(array &$titles,array &$data):void{
        foreach ($data as $row){
            $row['new'] = true;
            $this->model_class::create($row);
        }
    }
}
