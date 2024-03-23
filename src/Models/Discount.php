<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use Authx, HasActive;

    protected $fillable = [
        'name',
        'value',
        'type',
        'details',
        'one_time',
        'multi_time',
        'on_quantity',
        'on_total',
        'on_account',
        'on_checkout',
        'on_product',
        'min_quantity',
        'max_quantity',
        'min_total',
        'max_total',
        'active',
        'tags',
    ];

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
