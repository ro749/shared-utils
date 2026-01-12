<?php

namespace Ro749\SharedUtils\Data;
use Illuminate\Support\Facades\Log;

abstract class Data
{
    public bool $dynamic = true;
    //the data of this forms will be sent in the request, fill it with the id of the form
    public array $request_data = [];
    public $data = null;
    public function __construct(bool $dynamic = true)
    {
        $this->dynamic = $dynamic;
    }
    
    public abstract function init_data($request = null);

    public function get_data($request = null){
        if($this->data === null){
            $this->data = $this->init_data($request);
        }
        return $this->data;
    }

    public function get($attribute){
        if($this->data === null){
            $this->data = $this->init_data();
        }
        return $this->data[$attribute] ?? null;
    }

    public static function instance(): Data
    {
        $basename = class_basename(static::class);
        return new (config('overrides.data.'.$basename));
    }

    function get_id(){
        return class_basename($this);
    }
}
