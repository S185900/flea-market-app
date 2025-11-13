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

    const STATUS_AVAILABLE = 'available';
    const STATUS_SOLD = 'sold';

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function images() {
        return $this->hasMany(ItemImage::class);
    }

    // 「商品1つに1取引」なら
    public function transaction(){
        return $this->hasOne(Transaction::class);
    }

    public function isSold() {
        return $this->transaction()->exists();
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function getConditionLabelAttribute()
    {
        return match($this->condition) {
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い',
            default => '不明',
        };
    }

}
