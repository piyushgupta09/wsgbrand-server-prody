<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRange extends Model
{
    protected $fillable = [
        'material_id',
        'width',
        'length',
        'rate',
        'source',
        'quality',
        'other',
    ];

    protected $table = 'material_ranges';

    // Relationships

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
