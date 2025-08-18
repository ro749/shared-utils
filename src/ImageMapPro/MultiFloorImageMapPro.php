<?php

namespace Ro749\SharedUtils\ImageMapPro;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MultiFloorImageMapPro extends ImageMapProBase
{
    public string $floor_column;
    public string $type_column;
    public string $data_column;
    public array $floors = [];

    public array $files;

    public function __construct(
        string $id,
        string $table,
        string $floor_column,
        string $type_column,
        string $data_column,
        array $files,
        array $colors,
        array $opacities,
        array $floors
    ){
        parent::__construct(
            id: $id, 
            table:$table, 
            colors: $colors,
            opacities: $opacities
        );
        $this->floor_column = $floor_column;
        $this->type_column = $type_column;
        $this->data_column = $data_column;
        $this->files = $files;
        $this->floors = $floors;
    }

    public function get_tower_map(){
        $path = storage_path($this->files[0]);
        $map = json_decode(file_get_contents($path),true);
        $data = DB::table($this->table)
            ->select($this->floor_column)
            ->selectRaw("
                CASE 
                    WHEN MIN(".$this->data_column.") >= 1 AND MAX(".$this->data_column.") <= 2 
                    THEN 1 
                    ELSE 0 
                END as ".$this->data_column."
            ")
            ->groupBy($this->floor_column)
            ->get();
        $dispo = [];
        foreach($data as $d){
            $dispo[$d->floor] = $d->status;
        }
        return $this->re_color($map, $dispo);
    }

    public function get_floor_map($floor){
        $path = storage_path($this->files[$this->floors[$floor]]);
        $map = json_decode(file_get_contents($path),true);
        $data = DB::table($this->table)
        ->select('id',$this->type_column,$this->data_column)
        ->where($this->floor_column,$floor)
        ->get();
        $dispo = [];
        foreach($data as $d){
            $dispo[$d->type] = $d->status;
        }
        return $this->re_color($map, $dispo);
    }

    function get_unit(Request $data){
        $data = DB::table($this->table)->
        where($this->floor_column,$data->input("floor"))->
        where($this->type_column,$data->input("type"))->
        first();
        return $data;
    }
}