<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ingredients',
        'category_id',
        'price',
        'unit_type',
        'unit_value',
        'img_url',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
