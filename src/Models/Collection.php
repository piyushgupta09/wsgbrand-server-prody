<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Traits\Authx;
use Spatie\MediaLibrary\HasMedia;
use Fpaipl\Panel\Traits\ManageTag;
use Fpaipl\Panel\Traits\NamedSlug;
use Spatie\Activitylog\LogOptions;
use Fpaipl\Panel\Traits\ManageMedia;
use Fpaipl\Panel\Traits\ManageModel;
use Illuminate\Validation\Rules\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fpaipl\Panel\Traits\HasActive;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Collection extends Model implements HasMedia
{
    use
        Authx,
        HasActive,
        SoftDeletes,
        InteractsWithMedia,
        LogsActivity,
        NamedSlug,
        ManageMedia,
        ManageModel,
        ManageTag;

    protected $fillable = [
        'name',
        'order',
        'type',
        'info',
        'active',
    ];

    const STATUS = ['draft', 'live'];

    const TYPES = [
        [ 'id' => 'ranged', 'name' => 'Ranged' ],
        [ 'id' => 'featured', 'name' => 'Featured' ],
        [ 'id' => 'best_seller	', 'name' => 'Best Seller'],
        [ 'id' => 'recommended', 'name' => 'Recommended' ],
    ];

    const MEDIA_COLLECTION_NAME = 'collection';

    public static function validationRules()
    {
        return [
            'name' => ['required'],
            'order' => ['nullable', 'numeric'],
            'type' => ['nullable', 'string'],
            'info' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', File::types(['jpg', 'webp', 'png', 'jpeg'])],
        ];
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'active': return $this->active ? 'Yes' : 'No';
            default: return $this->{$key};
        }
    }    

    // Helper Functions

    public function getTimestamp($value)
    {
        return getTimestamp($this->$value);
    }

    public function getValue($key)
    {
        return $this->$key;
    }

    public function productsWithTrashed()
    {
        return $this->products()->withTrashed();
    }

    # add scope for type that also contains params
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Relationships

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
        $firstProductOptionId = Product::find($productId)->productOptions()->first()->id;
        $this->products()->attach($productId, ['product_option_id' => $firstProductOptionId]);

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
            ->useFallbackPath(public_path('storage/assets/images/placeholder.jpg'))
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('s100')
            ->format('webp')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('s400')
            ->format('webp')
            ->width(400)
            ->height(600)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('s800')
            ->format('webp')
            ->width(800)
            ->height(1200)
            ->sharpen(10)
            ->queued();
    }

    // Logging

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('model_log');
    }
}
