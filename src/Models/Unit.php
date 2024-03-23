<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use Authx, HasActive;

    protected $guarded = [];

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
