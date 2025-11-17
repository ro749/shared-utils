<?php
namespace Ro749\SharedUtils\Tables;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Tables\TableButton;
use Ro749\SharedUtils\Enums\Icon;
use Illuminate\Database\Eloquent\Builder;
use Ro749\SharedUtils\Filters\BackendFilters\BasicFilter;
class LayeredTable
{
    public array $layers = [];
    public function __construct(
        array $layers,
    ){
        $this->layers = $layers;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = [],$layer = 0): mixed
    {
        $curent_layer = $this->layers[$layer];
        if($curent_layer->parent != ''){
            $ans = $curent_layer->getter->backend_filters[] = new BasicFilter(
                id:'',
                filter: function(Builder $query,array $data) use ($curent_layer,$layer) {
                    $query->where($curent_layer->parent,'=', $data[$layer-1]);
                }
            ); 
        }
        
        $ans = $curent_layer->getter->get($start, $length, $search,$order,$filters);
        $ans['layer'] = $layer;
        $ans['filters'] = $filters;
        return $ans;
    }

    public function get_info(){
        return [
            'id' => $this->get_id(),
        ];
    }

    public function get_metadata($layer = 0, $selected_id = 0){
        $ans = [
            'id' => $this->get_id(),
            'table' => $this->layers[$layer]->getter->get_table(),
            'columns' => $this->layers[$layer]->getter->columns,
            'filters' => $this->layers[$layer]->getter->filters,
            'backend_filters' => $this->layers[$layer]->getter->backend_filters,
            'autoload' => true,
            'view' => null,
            'delete' => null,
            'needs_buttons' => true,
            'is_editable' => false,
            'edit_url' => null,
            'buttons' => [
                $layer == count($this->layers)-1 ?
                new TableButton(
                    icon: Icon::ACCEPT,
                    button_class: "ok-btn",
                    background_color_class:"bg-primary-light",
                    text_color_class:"text-primary-600"
                ):
                new TableButton(
                    icon: Icon::NEXT,
                    button_class: "next-btn",
                    background_color_class:"bg-primary-light",
                    text_color_class:"text-primary-600"
                )
            ]
        ];
        $ans['title'] = '';
        if($selected_id != 0){
            $prev_layer = $this->layers[$layer-1];
            $ans['title'] = $prev_layer->getter->model_class::where('id','=', $selected_id)->value($prev_layer->title);
        }
        return $ans;
    }

    function get_id(){
        return class_basename($this);
    }

    public static function instance(): LayeredTable
    {
        $basename = class_basename(static::class);
        return new (config('overrides.tables.'.$basename));
    }
}