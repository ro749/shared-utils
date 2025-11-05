<?php

namespace Ro749\SharedUtils\Models;

trait HasRandomId
{
    protected static function bootHasRandomId()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $min = $model->randomIdMin ?? 10000000;
                $max = $model->randomIdMax ?? 99999999;
                
                do {
                    $id = random_int($min, $max);
                } while (static::where('id', $id)->exists());
                
                $model->id = $id;
            }
        });
    }
    
    public function initializeHasRandomId()
    {
        $this->incrementing = false;
        $this->keyType = 'int';
    }
}