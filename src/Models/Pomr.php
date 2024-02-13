<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\ProductRange;
use Fpaipl\Prody\Models\MaterialRange;
use Illuminate\Database\Eloquent\Model;
use Fpaipl\Prody\Models\ProductMaterial;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pomr extends Model
{
    protected $table = 'pomrs';

    protected $fillable = [
        'name',
        'cost',
        'unit',
        'quantity',
        'product_range_id',
        'material_range_id',
        'grade',
        'product_material_id',
    ];

    public function productMaterial(): BelongsTo
    {
        return $this->belongsTo(ProductMaterial::class);
    }

    /**
     * Get the product range associated with the Pomr.
     */
    public function productRange(): BelongsTo
    {
        return $this->belongsTo(ProductRange::class);
    }

    /**
     * Get the material range associated with the Pomr.
     */
    public function materialRange(): BelongsTo
    {
        return $this->belongsTo(MaterialRange::class);
    }
}
