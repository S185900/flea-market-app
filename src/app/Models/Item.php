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

    const CONDITION_GOOD = 1;
    const CONDITION_NO_NOTICEABLE_DAMAGE = 2;
    const CONDITION_SOME_DAMAGE = 3;
    const CONDITION_BAD = 4;

    public static function conditionLabels()
    {
        return [
            self::CONDITION_GOOD => '良好',
            self::CONDITION_NO_NOTICEABLE_DAMAGE => '目立った傷や汚れなし',
            self::CONDITION_SOME_DAMAGE => 'やや傷や汚れあり',
            self::CONDITION_BAD => '状態が悪い',
        ];
    }

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
        return $this->belongsToMany(Category::class);
    }

    public function images() {
        return $this->hasMany(ItemImage::class);
    }

    // 「商品1つに1取引」なら
    public function transaction() {
        return $this->hasOne(Transaction::class);
    }

    public function isSold()
    {
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
            1 => '新品',
            2 => '未使用に近い',
            3 => '良好',
            4 => 'やや傷や汚れあり',
            5 => '傷や汚れあり',
            default => '不明',
        };
    }
}
