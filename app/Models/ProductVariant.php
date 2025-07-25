<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'variant_name', 'variant_title', 'regular_price', 'sale_price', 'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
