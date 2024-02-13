<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductDecision extends Model
{
    use Authx;

    // Table name if different from the pluralized version of the class name
    protected $table = 'product_decisions';

    // Mass assignable attributes
    protected $fillable = [
        'factory',
        'vendor',
        'market',
        'ecomm',
        'retail',
        'inbulk',
        'offline',
        'pay_cod',
        'pay_online',
        'pick_up',
        'delivery',
        'locked',
        'cost_locked',
        'product_id',
    ];

    // Define relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Additional properties and methods can be defined here
}
