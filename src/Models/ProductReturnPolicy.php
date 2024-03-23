<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\ReturnPolicy;
use Illuminate\Database\Eloquent\Model;

class ProductReturnPolicy extends Model
{
    protected $table = 'product_return_policy';

    protected $fillable = [
        'decision',
        'return_policy_id',
        'product_id',
    ];

    public function returnPolicy()
    {
        return $this->belongsTo(ReturnPolicy::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
