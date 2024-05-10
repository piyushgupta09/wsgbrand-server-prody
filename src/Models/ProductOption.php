<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Pomo;
use Fpaipl\Prody\Models\Product;
use Spatie\MediaLibrary\HasMedia;
use Fpaipl\Panel\Traits\NamedSlug;
use Fpaipl\Brandy\Models\StockItem;
use Fpaipl\Panel\Traits\ManageMedia;
use Fpaipl\Panel\Traits\ManageModel;
use Fpaipl\Prody\Models\MaterialOption;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductOption extends Model implements HasMedia
{
    use HasFactory,
        NamedSlug,
        ManageMedia,
        ManageModel,
        InteractsWithMedia;

    const MEDIA_COLLECTION_NAME = 'product_option';
    const MEDIA_CONVERSION_THUMB = 's100';
    const MEDIA_CONVERSION_CARD = 's300';
    const MEDIA_CONVERSION_PREVIEW = 's400';
    const MEDIA_CONVERSION_BANNER = 's800';
    const MEDIA_CONVERSION_FULL = 's1200';

    protected $fillable = [
        'name',
        'code',
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

    public function pomos()
    {
        return $this->hasMany(Pomo::class);
    }

    public function materialOptions(): BelongsToMany
    {
        return $this->belongsToMany(MaterialOption::class, 'pomos', 'product_option_id', 'material_option_id');
    }

    // Media

    public function getImage($conversion = self::MEDIA_CONVERSION_THUMB)
    {
        return $this->getFirstMediaUrl($this->getMediaCollectionName(), $conversion);
    }

    public function getImages($conversion = self::MEDIA_CONVERSION_THUMB)
    {
        return $this->getMedia($this->getMediaCollectionName())->map(function ($media) use ($conversion) {
            return $media->getUrl($conversion);
        });
    }

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


            // $productOption->addMedia($path)
            //   ->preservingOriginal()
            //   ->withResponsiveImages()
            //   ->manipulations(['*' => ['crop' => '3:4']])
            //   ->toMediaCollection(ProductOption::MEDIA_COLLECTION_NAME);

    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('s100')
            ->format('webp')
            ->width(80)
            ->height(100)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('s300')
            ->format('webp')
            ->width(200)
            ->height(300)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('s400')
            ->format('webp')
            ->width(300)
            ->height(400)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('s800')
            ->format('webp')
            ->width(600)
            ->height(800)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('s1200')
            ->format('webp')
            ->width(800)
            ->height(1200)
            ->sharpen(10)
            ->queued();
    }
}
