<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Attrikey;
use Fpaipl\Panel\Traits\NamedSlug;
use Illuminate\Database\Eloquent\Model;

class Attrival extends Model
{
    use Authx, NamedSlug;

    protected $fillable = [
        'attrikey_id',
        'value', // red, blue, green
        'detail',
        'active'
    ];
  
    public function attrikey()
    {
        return $this->belongsTo(Attrikey::class);
    }
}
