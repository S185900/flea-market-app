<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

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

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function comment() {
        return $this->hasOne(Comment::class);
    }
}
