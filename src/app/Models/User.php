<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Address;
use App\Models\Profile;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image_url',
        'shipping_address',
        'postal_code',
        'building_name',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_completed' => 'boolean',
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
        return $this->hasMany(Like::class, 'user_id');
    }

    public function getProfileImageUrlAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function getAddress(): Address
    {
        return new Address(
            $this->postal_code,
            $this->shipping_address,
            $this->building_name
        );
    }

    public function getProfile(): Profile
    {
        return new Profile(
            $this->name,
            $this->postal_code,
            $this->shipping_address,
            $this->building_name,
            $this->profile_image_url
        );
    }

}
