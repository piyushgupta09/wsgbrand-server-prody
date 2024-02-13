<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Spatie\MediaLibrary\HasMedia;
use Fpaipl\Panel\Traits\HasActive;
use Fpaipl\Panel\Traits\ManageMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Supplier extends Model implements HasMedia
{
    use Authx, HasActive, ManageMedia, InteractsWithMedia;

    const MEDIA_COLLECTION_NAME = 'supplier';
    
    const MATERIAL_SUPPLIER = 'material-supplier';
    const SERVICE_PROVIDER = 'service-provider';
    const GENERAL_SUPPLIER = 'general-supplier';

    const TYPES = [
        'material-supplier' => 'Material Supplier',
        'service-provider' => 'Service Provider',
        'general-supplier' => 'General Supplier',
    ];

    protected $fillable = [
        'sid',
        'name',
        'address',
        'contact_person',
        'contact_number',
        'email',
        'website',
        'type',
        'details',
        // 'apis',
        // 'active',
        // 'tags',
    ];

    protected static function booted()
    {    
        static::creating(function ($model) {
        
            $count = $model->count();
            $totalCount = $count ? $count : 0;

            $brandPrefix = 'WG-';

            switch (get_class($model)) {
                case 'Fpaipl\Prody\Models\Supplier':
                    $modelPrefix = 'SUP-';
                    break;
                
                default:
                    $modelPrefix = '';
                    break;
            }

            // Generate the new SID
            $model->sid = $brandPrefix . $modelPrefix . ($totalCount + 1);
        });
    }

    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|numeric|digits:10',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'type' => 'nullable|string|in:' . implode(',', array_keys(self::TYPES)),
            'details' => 'nullable|string',
        ];
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'sid': return $this->sid;
            case 'type': return self::TYPES[$this->type];
            default: return $this->key;
        }
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function getMediaCollectionName(): string
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
    }

}