<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Consumable;
use Illuminate\Database\Eloquent\Model;

class ProductConsumable extends Model
{
    protected $table = 'product_consumable';

    protected $fillable = [
        'product_id', 
        'consumable_id', 
        'rate', 
        'ratio', 
        'amount',
        'reasons'
    ];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }
}
