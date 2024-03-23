<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use Authx, HasActive;

    protected $guarded = [];

    public function getTableData($key)
    {
        switch ($key) {
            case 'description': return $this->description ?? $this->name;
            default: return $this->{$key};
        }
    }

}
