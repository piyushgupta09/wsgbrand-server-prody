<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Traits\NamedSlug;
use Illuminate\Database\Eloquent\Model;

class ProductProcess extends Model
{
    use NamedSlug;

    protected $fillable = [
        'stage',
        'nature',
        'name',
        'slug',
        'cost',
        'time',
        'order',
        'instructions',
        'description',
        'special_note',
        'product_id',
    ];

    /**
     * Scope a query to only include processes of a given stage.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $stage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope a query to only include processes of a given nature (Direct or Indirect).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $nature
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNature($query, $nature)
    {
        return $query->where('nature', $nature);
    }

    /**
     * Product relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
