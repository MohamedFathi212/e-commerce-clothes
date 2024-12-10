<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // الأعمدة القابلة للتعبئة
    protected $fillable = [
        'name', 
        'slug', 
        'short_description', 
        'description', 
        'regular_price', 
        'sale_price', 
        'SKU', 
        'stock_status', 
        'featured', 
        'quantity', 
        'image', 
        'images', 
        'category_id', 
        'brand_id'
    ];

    // تحويل الأعمدة
    protected $casts = [
        'images' => 'array',
    ];

    // العلاقة مع الفئة
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // العلاقة مع العلامة التجارية
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
