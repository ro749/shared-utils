<?php
namespace Ro749\SharedUtils\Tables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class DynamicTable extends BaseTable
{
    public string $table;
    public string $metadata_id;
    public function __construct(
        string $id,
        string $table,
        string $metadata_id,
        View $view = null, 
        Delete $delete = null,
        View $edit_url = null
    ){
        $this->id = $id;
        $this->table = $table;
        $this->metadata_id = $metadata_id;
        $this->view = $view;
        $this->delete = $delete;
        $this->edit_url = $edit_url;
    }

    function get_metadata(Request $request){
        $ans = DB::table("columns")->where("table","=",$this->metadata_id)->get();
        return $ans;
    }

    function get_info(){
        return [
            'id' => $this->id,
            'table' => $this->getter->table,
            'columns' => $this->getter->columns,
            'view' => $this->view,
            'delete' => $this->delete,
            'needs_buttons' => $this->needsButtons(),
            'is_editable' => $this->is_editable,
            'edit_url' => $this->edit_url,
            'buttons' => $this->buttons,
            'filters' => []
        ];
    }
}