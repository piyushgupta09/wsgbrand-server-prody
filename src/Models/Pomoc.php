<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\ProductRange;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pomoc extends Model
{
    use HasFactory;

    protected $table = 'pomocs';

    protected $fillable = [
        'product_range_id',
        'material_option_id',
        'quantity',
        'unit',
        'name',
        'cost',
    ];

    public function productRange(): BelongsTo
    {
        return $this->belongsTo(ProductRange::class);
    }

    public function materialOption(): BelongsTo
    {
        return $this->belongsTo(MaterialOption::class);
    }
}
