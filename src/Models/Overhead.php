<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Illuminate\Validation\Rule;
use Fpaipl\Prody\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Overhead extends Model
{
    use Authx;
       
    protected $fillable = ['stage', 'name', 'amount', 'capacity', 'rate', 'description'];


    public static function validationRules($id = null)
    {
        $stages = array_column(config('panel.overhead_stages'), 'id');
        $stagesString = implode(',', $stages);
    
        return [
            'stage' => 'required|string|in:' . $stagesString,
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('overheads')->ignore($id),
            ],
            'amount' => 'required|numeric|min:0',
            'capacity' => 'required|numeric|min:0',
            'description' => 'required|string',
        ];
    }
    
    // Relationship with ProductOverhead
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_overhead')->withPivot('cost', 'ratio', 'reasons');
    }

    // Scope to filter by stage
    public function scopeOfStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }
}
