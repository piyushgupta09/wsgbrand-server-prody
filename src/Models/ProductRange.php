<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Pomr;
use Fpaipl\Prody\Models\Unit;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Traits\NamedSlug;
use Fpaipl\Brandy\Models\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductRange extends Model
{
    use HasFactory,
        NamedSlug;

    protected $fillable = [
        'name',
        'slug',
        'mrp',
        'cost',
        'rate',
        'active',
        'product_id',
    ];

    // Relationships

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function pomrs()
    {
        return $this->hasMany(Pomr::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
