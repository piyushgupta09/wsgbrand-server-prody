<?php

namespace Fpaipl\Prody\Models;

use App\Models\User;
use Fpaipl\Prody\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductUser extends Model
{
    use HasFactory;

    protected $table = 'product_users';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
