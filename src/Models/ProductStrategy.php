<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Strategy;
use Illuminate\Database\Eloquent\Model;

class ProductStrategy extends Model
{
    protected $table = 'product_strategy';

    protected $fillable = [
        'decision',
        'product_id',
        'strategy_id',
        'name',
        'math',
        'value',
        'type',
        'details',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
