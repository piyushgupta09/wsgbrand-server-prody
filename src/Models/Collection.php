<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Fpaipl\Prody\Models\CollectionProduct;

class Collection extends Model
{
    use Authx, HasActive;

    protected $guarded = [];

    const STATUS = ['draft', 'live'];

    const TYPES = [
        [ 'id' => 'ranged', 'name' => 'Ranged' ],
        [ 'id' => 'featured', 'name' => 'Featured' ],
        [ 'id' => 'best_seller	', 'name' => 'Best Seller'],
        [ 'id' => 'recommended', 'name' => 'Recommended' ],
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function getTableData($key)
    {
        switch ($key) {
            case 'iamge': return $this->getImage('s100');
            case 'active': return $this->active ? 'Yes' : 'No';
            default: return $this->{$key};
        }
    }    

    public function getImage($conversion="s100")
    {
        $images = json_decode($this->images, true);
        return isset($images[$conversion]) ? $images[$conversion] : null;
    }

    // Scopes

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Relationships

    public function collectionProducts()
    {
        return $this->hasMany(CollectionProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product');
    }

    public function productOptions()
    {
        return $this->belongsToMany(Product::class, 'collection_product');
    }

    public function addProductCollection($productId)
    {
        $firstProductOptionId = Product::find($productId)?->productOptions()?->first()->id;
        if (!$firstProductOptionId) {
            return;
        }
        $this->products()->attach($productId, ['product_option_id' => $firstProductOptionId]);

    }
}
