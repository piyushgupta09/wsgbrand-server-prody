<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Fpaipl\Prody\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use Authx, HasActive;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getImage($conversion="s100")
    {
        $images = json_decode($this->images, true);
        return isset($images[$conversion]) ? $images[$conversion] : null;
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'image': return $this->getImage('s100');
            default: return $this->{$key};
        }
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
