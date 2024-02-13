<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Overhead;
use Illuminate\Database\Eloquent\Model;

class ProductOverhead extends Model
{
    protected $table = 'product_overhead';

    protected $fillable = [
        'product_id', 
        'overhead_id', 
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

    // Relationship with Overhead
    public function overhead()
    {
        return $this->belongsTo(Overhead::class, 'overhead_id');
    }
}
