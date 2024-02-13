<?php

namespace Fpaipl\Prody\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Fpaipl\Prody\Models\Collection;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\ProductOption;

class CollectionProduct extends Model
{
    use HasFactory;

    protected $table = 'collection_product';

    protected $fillable = [
        'collection_id',
        'product_id',
        'product_option_id',
    ];

    // Relationships

    public function collection()
    {
        return $this->belongsTo(Collection::class,'collection_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class,'product_option_id');
    }
}
