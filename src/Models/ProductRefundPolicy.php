<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\RefundPolicy;
use Illuminate\Database\Eloquent\Model;

class ProductRefundPolicy extends Model
{
    protected $table = 'product_refund_policy';

    protected $fillable = [
        'decision',
        'refund_policy_id',
        'product_id',
    ];

    public function refundPolicy()
    {
        return $this->belongsTo(RefundPolicy::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }
}
