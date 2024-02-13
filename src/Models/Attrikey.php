<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Attrival;
use Fpaipl\Panel\Traits\NamedSlug;
use Illuminate\Database\Eloquent\Model;

class Attrikey extends Model
{
    use Authx, NamedSlug;

    protected $fillable = [
        'name',
        'detail',
    ];

    public function attrivals()
    {
        return $this->hasMany(Attrival::class);
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'values':
                return $this->attrivals->pluck('value')->implode(', ');
            default:
                return $this->{$key};
        }
    }
}
