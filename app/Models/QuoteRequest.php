<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'product_id',
        'category_id',
        'brand_id',
        'customer_name',
        'company_name',
        'customer_email',
        'customer_phone',
        'country',
        'product_name',
        'product_sku',
        'brand_name',
        'quantity',
        'message',
        'image',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->withDefault();
    }
}
