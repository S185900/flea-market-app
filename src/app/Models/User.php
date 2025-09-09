<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_completed',
        'profile_image_url',
        'introduction',
        'shipping_address',
        'phone_number',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function items() {
        return $this->hasMany(Item::class);
    }

    public function transactionsAsBuyer() {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function transactionsAsSeller() {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'commenter_id');
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }
}
