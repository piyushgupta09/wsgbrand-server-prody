<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Traits\Authx;
use Spatie\MediaLibrary\HasMedia;
use Fpaipl\Panel\Traits\NamedSlug;
use Spatie\Activitylog\LogOptions;
use Fpaipl\Panel\Traits\ManageMedia;
use Illuminate\Validation\Rules\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Brand extends Model implements HasMedia
{
    use 
        Authx,
        InteractsWithMedia,
        LogsActivity,
        NamedSlug,
        ManageMedia;

    protected $fillable = [
        'name',
    ];
    
    const MEDIA_COLLECTION_NAME = 'brand';
    const MEDIA_CONVERSION_THUMB = 's100';
    const MEDIA_CONVERSION_PREVIEW = 's400';

    public static function validationRules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', File::types(['jpg','jpeg','webp','png'])],
        ];
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'name': return $this->name;
            case 'image': return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_NAME, 's100');
            default: return '';
        }
    }

    /*---------------------- Relationships --------------------------*/

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productsWithTrashed(): HasMany
    {
        return $this->hasMany(Product::class)->withTrashed();
    }

    /*---------------------- Media --------------------------*/

    public function getImage($conversion = self::MEDIA_CONVERSION_THUMB): string
    {
        return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_NAME, $conversion);
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
            ->height(400)
            ->sharpen(10)
            ->queued();
    }

    /*---------------------- Logs --------------------------*/

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('model_log');
    }
}
