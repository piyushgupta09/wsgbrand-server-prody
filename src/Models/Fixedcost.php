<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Illuminate\Database\Eloquent\Model;

class Fixedcost extends Model
{
    use Authx;

    protected $fillable = ['name', 'amount', 'capacity', 'rate', 'details'];

    // Assuming no direct relationships needed for FixedCost as it applies globally
}
