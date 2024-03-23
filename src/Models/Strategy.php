<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    use Authx, HasActive;

    protected $fillable = [
        'name',
        'slug',
        'math',
        'value',
        'type',
        'details',
        'active',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
        $this->attributes['slug'] = strtolower(str_replace(' ', '-', $value));
    }

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
