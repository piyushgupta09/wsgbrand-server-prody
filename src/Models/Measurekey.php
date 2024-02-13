<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\NamedSlug;
use Fpaipl\Prody\Models\Measureval;
use Illuminate\Database\Eloquent\Model;

class Measurekey extends Model
{
    use Authx, NamedSlug;

    protected $fillable = [
        'name',
        'unit',
        'detail',
    ];

    public function measurevals()
    {
        return $this->hasMany(Measureval::class);
    }

    public function getTableData($key)
    {
        switch ($key) {
            case 'values':
                return $this->measurevals->pluck('value')->implode(', ');
            default:
                return $this->{$key};
        }
    }
}
