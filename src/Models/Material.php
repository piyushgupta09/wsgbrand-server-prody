<?php

namespace Fpaipl\Prody\Models;

use Illuminate\Support\Str;
use Fpaipl\Prody\Models\Pomo;
use Fpaipl\Prody\Models\Unit;
use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Supplier;
use Fpaipl\Panel\Traits\HasActive;
use Fpaipl\Panel\Traits\ManageTag;
use Fpaipl\Prody\Models\MaterialRange;
use Fpaipl\Prody\Models\MaterialOption;
use Illuminate\Database\Eloquent\Model;
use Fpaipl\Prody\Models\ProductMaterial;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use Authx, ManageTag, HasActive;

    protected $fillable = [
        'sid',
        'supplier_id',
        'category_name',
        'category_type',
        'unit_name',
        'unit_abbr',
        'name',
        'price',
        'details',
        'stock',
        'stockItems',
    ];

    public static function validationRules()
    {
        return [
            'sid' => ['required', 'unique:materials'],
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'category_name' => ['required', 'string', 'min:5', 'max:255'],
            'category_type' => [
                'required',
                'in:' . implode(',', array_keys(config('prody.fabric_category_types')))
            ],
            'unit_name' => ['required', 'string', 'min:1', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'details' => ['nullable', 'string', 'min:5', 'max:255'],
            'tags' => ['nullable', 'string', 'min:5', 'max:255'],
        ];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
    
    public function getTableData($key)
    {
        switch ($key) {
            // case 'unit_name': return $this->unit->abbr;
            default: return $this->$key;
        }
    }

    // add static created method
    public static function boot()
    {
        parent::boot();
        static::created(function ($material) {
            $material->update(['name' => $material->category_name . '-' . $material->name]);
        });
    }

    public function materialOptions(): HasMany
    {
        return $this->hasMany(MaterialOption::class);
    }

    public function materialRanges(): HasMany
    {
        return $this->hasMany(MaterialRange::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_materials');
    }

    public function productMaterials()
    {
        return $this->hasMany(ProductMaterial::class);
    }

    public function pomos()
    {
        return $this->hasManyThrough(Pomo::class, MaterialOption::class);
    }

    public function pomrs()
    {
        return $this->hasManyThrough(Pomr::class, MaterialRange::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
