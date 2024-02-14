<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use Authx;
    
    protected $fillable = ['name', 'unit', 'rate', 'details'];

    // Consumables may be linked to multiple products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_consumable')->withPivot('quantity', 'cost', 'reasons');
    }
}
