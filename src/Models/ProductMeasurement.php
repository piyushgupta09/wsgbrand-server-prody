<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Measurekey;
use Fpaipl\Prody\Models\Measureval;
use Illuminate\Database\Eloquent\Model;

class ProductMeasurement extends Model
{
    protected $fillable = [
        'product_id',
        'measurekey_id',
        'measureval_id',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurekey()
    {
        return $this->belongsTo(Measurekey::class);
    }

    public function measureval()
    {
        return $this->belongsTo(Measureval::class);
    }
}
