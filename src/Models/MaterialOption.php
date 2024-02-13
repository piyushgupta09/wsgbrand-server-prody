<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Pomoc;
use Fpaipl\Prody\Models\Material;
use Fpaipl\Prody\Models\ProductOption;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Fpaipl\Panel\Traits\ManageMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaterialOption extends Model implements HasMedia
{
    use ManageMedia, InteractsWithMedia;

    const MEDIA_COLLECTION_NAME = 'material_option';

    protected $fillable = [
        'material_id',
        'slug',
        'name',
        'code',
        'image',
        'images',
    ];

    // Relationships

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function pomocs(): HasMany
    {
        return $this->hasMany(Pomoc::class);
    }

    public function pomos(): HasMany
    {
        return $this->hasMany(Pomo::class);
    }

    public function productOptions(): BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class, 'pomos', 'product_option_id', 'material_option_id');
    }

    // Media

    public function getMediaCollectionName()
    {
        return self::MEDIA_COLLECTION_NAME;
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection($this->getMediaCollectionName())
            ->useFallbackUrl(config('app.url') . '/storage/assets/images/placeholder.jpg')
            ->useFallbackPath(public_path('storage/assets/images/placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('s100')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(100)
            ->height(100)
            ->sharpen(10);

        $this->addMediaConversion('s400')
            ->format(Manipulations::FORMAT_WEBP)
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }
}
