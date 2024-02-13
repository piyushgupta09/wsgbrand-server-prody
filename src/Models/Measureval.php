<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\NamedSlug;
use Illuminate\Database\Eloquent\Model;

class Measureval extends Model
{
    use Authx, NamedSlug;

    protected $fillable = [
        'measurekey_id',
        'value', // red, blue, green
        'detail',
        'active'
    ];

    public function measurekey()
    {
        return $this->belongsTo(Measurekey::class);
    }
}
