<?php

namespace Ro749\SharedUtils\Inputs;
use Closure;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Input extends Component
{
    public string $id;
    public string $table;
    public string $column;
    public int $dbid;
    public bool $autosave;

    public function __construct(string $id, string $table,string $column,int $dbid,bool $autosave=false)    
    {
        $this->id = $id;
        $this->table = $table;
        $this->column = $column;
        $this->dbid = $dbid;
        $this->autosave = $autosave;
    }

    public function get(): mixed
    {
        $query = DB::table($this->table)->select($this->column)->where('id', $this->dbid);
        return $query->get()[0]->{$this->column};
    }
    public function save(string $new_value): int   
    {
        return DB::table($this->table)->where('id', $this->dbid)->update([$this->column => $new_value]);
    }
    public function render()
    {
        return view('shared-utils::components.text');
    }
}