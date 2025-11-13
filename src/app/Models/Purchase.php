<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'item_id',
        'buyer_id',
        'seller_id',
        'status',
        'payment_method',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'stripe_checkout_session_id',
        'shipping_address',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // 購入者視点のリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入完了判定
    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }

    // 購入者ごとの絞り込み
    public function scopeByUser($query, $userId)
    {
        return $query->where('buyer_id', $userId);
    }
}
