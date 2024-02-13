<?php

namespace Fpaipl\Prody\Models;

use Illuminate\Support\Str;
use Fpaipl\Prody\Models\ProductOption;
use Fpaipl\Panel\Traits\Authx;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Fpaipl\Panel\Traits\ManageTag;
use Spatie\Activitylog\LogOptions;
use Fpaipl\Panel\Traits\ManageMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model implements HasMedia
{
    use
        Authx,
        SoftDeletes,
        InteractsWithMedia,
        LogsActivity,
        ManageMedia,
        ManageTag;

    protected $fillable = [
        'name',
        'slug',
        'info',
        'parent_id',
        'order',
        'tags',
        'display',
        'active',
    ];

    const MEDIA_COLLECTION_NAME = 'category';
    const MEDIA_CONVERSION_THUMB = 's100';
    const MEDIA_CONVERSION_PREVIEW = 's400';
   
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->generateUniqueSlug($value);
    }
    
    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
    
        if ($this->parent_id) {
            $parent = Category::find($this->parent_id);
            if ($parent) {
                $slug = $parent->slug . '-' . $slug;
            }
        }
    
        $counter = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    
        $this->attributes['slug'] = $slug;
    }  
    
    public function getTableData($key)
    {
        switch ($key) {
            case 'parent_id': return $this->getParentFullName($this);
            default: return $this->{$key};
        }
    }

    /*----------------- Scopes ----------------------*/

    public function scopeCanHaveChildren($query, $sortBy = 'name', $order = 'asc')
    {
        return $query->where('parent_id', '!=' , NULL)->orderby($sortBy, $order);
    }

    public function scopeRoot($query, $sortBy = 'order', $order = 'asc')
    {
        return $query->where('parent_id', NULL)->orderby($sortBy, $order);
    }

    public function scopeCanBeParent($query, $sortBy = 'order', $order = 'asc')
    {
        return $query->orderby($sortBy, $order);
    }

    public function scopeDisplay($query, $sortBy = 'order', $order = 'asc')
    {
        return $query->where('display', true)->where('parent_id', '!=' , NULL)->orderby($sortBy, $order);
    }

    /*----------------- Relationships ----------------------*/

    public function parent(): HasOne
    {
       return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
       return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productOptions(): HasManyThrough
    {
        return $this->hasManyThrough(ProductOption::class, Product::class);
    }

    /*---------------------- Helpers --------------------------*/

    public function hasChildren()
    {
        return $this->children()->exists();
    }

    public function hasParent()
    {
        return isset($this->parent);
    }

    public function hasProducts()
    {
        return $this->products()->exists();
    }

    public function parentWithTrashed()
    {
        return $this->parent()->withTrashed();
    }

    public function childWithTrashed()
    {
        return $this->child()->withTrashed();
    }

    /**
     * Recursively get the full name of the category, including all parent names.
     * 
     * @param  \App\Models\Category  $category
     * @return string
     */
    public function getFullName($category)
    {
        $name = $category->name;

        if ($category->hasParent()) {
            $name = $this->getFullName($category->parent) . ' - ' . $name;
        }

        return $name;
    }

    public function getParentFullName($category)
    {
        if ($category->hasParent()) {
            return $this->getFullName($category->parent);
        }

        return '';
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
