<?php

namespace Ro749\SharedUtils\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Ro749\SharedUtils\Readers\MigrationHelper;

class Absorb extends Command{
    protected $signature = 'db:absorb {table} {id_column} {normalized_table} {data_column}';
    protected $description = '"absorbe" vaolres que deverian de estar en otra tabla de unos datos que fueron normalizados';

    public function handle(){
        $table = $this->argument('table');
        $id_column = $this->argument('id_column');
        $normalized_table = $this->argument('normalized_table');
        $data_column = $this->argument('data_column');
        $normalized_data = DB::table($normalized_table)->get();
        $absorbed_values = [];
        $migration_text = MigrationHelper::generate_migration_for_add_rows(
            $normalized_table,
            [$data_column=>'string']
        );

        foreach ($normalized_data as $row){
            $absorbed_value = DB::table($table)->where($id_column, $row->id)->value($data_column);
            if(DB::table($table)
                ->where([
                    [$id_column,'=',$row->id],
                    [$data_column,'!=',$absorbed_value]
                ])->count()!=0){
                $this->error('No se puede absorver, dato no es el mismo en todos con id '.$row->id.' en '.$table);
                return;
            }
            $absorbed_values[$row->id] = $absorbed_value;
        }
        foreach ($absorbed_values as $key=>$value){
            $migration_text .= "DB::table('".$normalized_table."')->where('id', $key)->update(['".$data_column."' => '$value']);\n";
        }
        $migration_text .= MigrationHelper::generate_migration_for_remove_rows($table,[$data_column]);
        MigrationHelper::create_migration_file(
            'absorb_'.$table.'_'.$data_column, 
            $migration_text
        );
    }


}
