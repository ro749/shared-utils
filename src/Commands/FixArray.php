<?php

namespace Ro749\SharedUtils\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Readers\MigrationHelper;
use Illuminate\Support\Facades\Log;
class FixArray extends Command
{
    protected $signature = 'db:array {table} {columns} {new_table} {parent_column} {value_column}';
    protected $description = 'convierte columnas de un array estático a uno dinámico';
    public function handle(): void
    {
        $table = $this->argument('table');
        $parent_column = $this->argument('parent_column');
        $value_column = $this->argument('value_column');
        $new_table = $this->argument('new_table');
        $columns = explode(',',$this->argument('columns'));
        $data = DB::table($table)->get()->toArray();
        $new_data = [];
        foreach($data as $row) {
            $new_row = [];
            foreach($columns as $column) {
                if(empty($row->{$column})){continue;}
                $new_row[$parent_column] = $row->id;
                $new_row[$value_column] = $row->{$column};
                $new_data[] = $new_row;
            }
        }
        $migration_text = MigrationHelper::generate_migration_for_table(
            $new_table, 
            [$parent_column => 'relation', $value_column => 'string'], 
            $new_data
        );
        $migration_text .= MigrationHelper::generate_migration_for_remove_rows(
            $table,
            $columns
        );
        MigrationHelper::create_migration_file(
            'array_'.$table, 
            $migration_text
        );
    }
}