<?php

namespace Ro749\SharedUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SingleImageMapPro extends ImageMapProBase
{
    public string $label_column;
    public string $data_column;

    public function __construct(
        string $id,
        string $table,
        string $label_column,
        string $data_column,
        array $colors,
        array $opacities
    ){
        parent::__construct(
            id: $id, 
            table:$table, 
            colors: $colors,
            opacities: $opacities
        );
        $this->label_column = $label_column;
        $this->data_column = $data_column;
    }

    public function get_map(){
        $path = storage_path("ImageMapPro.json");
        $map = json_decode(file_get_contents($path),true);
        $data = DB::table($this->table)->select('id',$this->label_column,$this->data_column)->get();
        $dispo = [];
        foreach($data as $d){
            $dispo[$d->unit] = $d->status;
        }
        $artboards = &$map["artboards"];
        foreach($artboards as &$artboard){
            foreach($artboard["children"] as &$child){
                if($child["type"] == "group"){
                    foreach($child["children"] as &$grandchildren){
                        $this->style_unit($grandchildren,$dispo);
                    }
                }
                else{
                    $this->style_unit($child,$dispo);
                }
            }
        }
        return $map;
    }

    function get_unit(Request $data){
        $data = DB::table($this->table)->where($this->label_column,$data->input("unit"))->first();
        return $data;
    }
}