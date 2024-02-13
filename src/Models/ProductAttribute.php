<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Attrikey;
use Fpaipl\Prody\Models\Attrival;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = [
        'product_id',
        'attrikey_id',
        'attrival_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attrikey()
    {
        return $this->belongsTo(Attrikey::class);
    }

    public function attrival()
    {
        return $this->belongsTo(Attrival::class);
    }
}
