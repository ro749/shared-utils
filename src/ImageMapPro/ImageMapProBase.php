<?php

namespace Ro749\SharedUtils\ImageMapPro;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

abstract class ImageMapProBase
{
    public string $id;
    public string $table;
    public array $colors = [];
    public array $opacities = [];

    public function __construct(
        string $id,
        string $table,
        array $colors,
        array $opacities
    ){
        $this->id = $id;
        $this->table = $table;
        $this->colors = $colors;
        $this->opacities = $opacities;
    }

    public function style_unit(&$child,&$dispo){
        if(!isset($dispo[$child["title"]])){
            $child["default_style"]["background_color"] = "#ffffff";
            $child["default_style"]["background_opacity"] = 0;
            return;
        }
        $color_value = $dispo[$child["title"]];
        $color = $this->colors[$color_value];
        $opacity = $this->opacities[$color_value];
        $child["default_style"]["background_color"] = $color;
        $child["default_style"]["background_opacity"] = $opacity;
        $child["mouseover_style"] = [
            "opacity"=> 1,
            "background_type"=> "color",
            "background_color"=> $color,
            "background_opacity"=> 0.8,
            "background_image_url"=> "",
            "background_image_opacity"=> 1,
            "background_image_scale"=> 1,
            "background_image_offset_x"=> 0,
            "background_image_offset_y"=> 0,
            "border_radius"=> 4,
            "border_width"=> 0,
            "border_style"=> "solid",
            "border_color"=> "#ffffff",
            "border_opacity"=> 1,
            "stroke_color"=> "#ffffff",
            "stroke_opacity"=> 0.75,
            "stroke_width"=> 0,
            "stroke_dasharray"=> "0",
            "stroke_linecap"=> "round",
            "icon_fill"=> "#000000",
            "parent_filters"=> [],
            "filters"=> []
        ];
        if($opacity == 0 && $color != "#ffffff"){
            $child["default_style"]["border_color"] = $color;
        }
    }

    function re_color($map,$data):array{
        $artboards = &$map["artboards"];
        foreach($artboards as &$artboard){
            foreach($artboard["children"] as &$child){
                if($child["type"] == "group"){
                    foreach($child["children"] as &$grandchildren){
                        $this->style_unit($grandchildren,$data);
                    }
                }
                else{
                    $this->style_unit($child,$data);
                }
            }
        }
        return $map;
    }
    abstract function get_unit(Request $data);
}