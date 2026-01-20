<?php

namespace Ro749\SharedUtils\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Ro749\SharedUtils\Readers\MigrationHelper;

class Normalize extends Command{
    protected $signature = 'db:normalize {table} {column} {--new-table=} {--new-column=}';
    protected $description = 'Convierte una columna con valores repetidos en una tabla normalizada';

    public function handle(){
        $table = $this->argument('table');
        $column = $this->argument('column');
        $new_table = $this->option('new-table') ?: $column.'s';
        $new_column = $this->option('new-column') ?: 'name';
        $unique_values = DB::table($table)->select($column)->distinct()->get()->pluck($column)->toArray();
        $change_data = [];
        foreach ($unique_values as $index => $value){
            $change_data[] = [
                $column,
                $value,
                $index + 1
            ];
        }
        
        $migration_text = MigrationHelper::generate_migration_for_table(
            $new_table, [$new_column => 'string'], 
            array_map(function($value) use ($new_column) {
            return [$new_column => $value];
        }, $unique_values));

        $migration_text .= MigrationHelper::generate_migration_for_alter_data(
            $table, 
            $change_data
        );
        $migration_text .= MigrationHelper::generate_migration_for_alter_table(
            $table, 
            [$column => 'int']
        );
        MigrationHelper::create_migration_file(
            'normalize_'.$table.'_'.$column, 
            $migration_text
        );
    }


}
