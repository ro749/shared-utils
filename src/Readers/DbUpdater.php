<?php

namespace Ro749\SharedUtils\Readers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class DbUpdater extends DbUpdate
{
    public string $key = '';
    public function __construct(
        string $model_class, 
        string $public_id,
        array $required_columns = [], 
        bool $add_new_columns = false,
        string $key = ''
    )
    {
        parent::__construct(
            model_class: $model_class,
            public_id: $public_id,
            required_columns: $required_columns,
            add_new_columns: $add_new_columns
        );
        $this->public_id = $public_id;
        $this->key = $key;
    }

    public function process_data(array &$titles,array &$data):void{
        foreach ($data as $row){
            $new_row = [];
            foreach ($row as $key => $value) {
                if (
                    $key == $this->public_id || 
                    $value == '' ||
                    $this->model_class::where($this->public_id, $row[$this->public_id])->value($key) == $value
                    ) 
                continue;
                $new_row["new_{$key}"] = $value;
            }
            $this->model_class::where($this->public_id, $row[$this->public_id])->update($new_row);
        }
    }

    public function save_changes(){
        foreach ($this->required_columns as $column){
            if ($column == $this->public_id) continue;
            $this->model_class::whereNotNull("new_{$column}")->update([
                $column => DB::raw("new_{$column}")
            ]);
            $this->model_class::query()->update([
                "new_{$column}" => null
            ]);
        }
    }
}
