<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Pomo;
use Fpaipl\Prody\Models\Pomr;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'material_id',
        'product_id',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function pomos()
    {
        return $this->hasMany(Pomo::class);
    }

    public function pomrs()
    {
        return $this->hasMany(Pomr::class);
    }
}
