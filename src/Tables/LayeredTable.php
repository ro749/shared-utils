<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Statistics\BaseStatistic;
use Ro749\SharedUtils\Getters\StatisticsGetter;
use Ro749\SharedUtils\FormRequests\BaseFormRequest;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Tables\TableButton;
use Ro749\SharedUtils\Enums\Icon;
class LayeredTable
{
    public string $id;
    public array $layers = [];
    public function __construct(
        string $id,
        array $layers,
    ){
        $this->id = $id;
        $this->layers = $layers;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = [],$layer = 0): mixed
    {
        $ans = $this->layers[$layer]->get($start, $length, $search,$order,$filters);
        $ans['layer'] = $layer;
        return $ans;
    }

    public function get_info(){
        return [
            'id' => $this->id
        ];
    }

    public function get_metadata($layer = 0){
        return [
            'id' => $this->id,
            'table' => $this->layers[$layer]->table,
            'columns' => $this->layers[$layer]->columns,
            'filters' => $this->layers[$layer]->filters,
            'backend_filters' => $this->layers[$layer]->backend_filters,
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
    }
}