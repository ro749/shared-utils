<?php

namespace Ro749\SharedUtils\Data;
use Illuminate\Support\Facades\Log;
abstract class Data
{
    public bool $dynamic = true;
    public $data = null;
    public function __construct(bool $dynamic = true)
    {
        $this->dynamic = $dynamic;
    }
    
    public abstract function init_data();

    public function get_data(){
        if($this->data === null){
            $this->data = $this->init_data();
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
