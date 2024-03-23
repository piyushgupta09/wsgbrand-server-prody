<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Fpaipl\Prody\Models\ProductOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use Authx, HasActive;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'name': return $this->getParentFullName($this) . ' - ' . $this->name;
            default: return $this->{$key};
        }
    }

    public function getImage($conversion="s100")
    {
        $images = json_decode($this->images, true);
        return isset($images[$conversion]) ? $images[$conversion] : null;
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

}
