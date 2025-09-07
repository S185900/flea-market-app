<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'brand_id',
        'description',
        'price',
        'category_id',
        'likes_count',
        'comments_count',
        'condition',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function images() {
        return $this->hasMany(ItemImage::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }
}
