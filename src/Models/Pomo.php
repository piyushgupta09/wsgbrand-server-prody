<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Material;
use Fpaipl\Prody\Models\ProductOption;
use Fpaipl\Prody\Models\MaterialOption;
use Illuminate\Database\Eloquent\Model;
use Fpaipl\Prody\Models\ProductMaterial;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pomo extends Model
{
    protected $table = 'pomos';

    protected $fillable = [
        'grade',
        'product_material_id',
        'product_option_id',
        'material_option_id',
    ];

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function materialOption(): BelongsTo
    {
        return $this->belongsTo(MaterialOption::class);
    }

    public function productMaterial(): BelongsTo
    {
        return $this->belongsTo(ProductMaterial::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'product_material_id', 'material_id');
    }
}
