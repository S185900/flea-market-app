<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'user_id',
        'title',
        'brand_id',
        'description',
        'price',
        'condition',
        'status',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }
}
