<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;

class ReturnPolicy extends Model
{
    use Authx, HasActive;

    protected $table = 'return_policies';

    protected $fillable = [
        'name',
        'details',
        'active',
    ];

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
