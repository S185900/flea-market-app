<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    public string $name;
    public string $postal_code;
    public string $shipping_address;
    public ?string $building_name;
    public ?string $profile_image_url;

    public function __construct(string $name, string $postal_code, string $shipping_address, ?string $building_name, ?string $profile_image_url)
    {
        $this->name = $name;
        $this->postal_code = $postal_code;
        $this->shipping_address = $shipping_address;
        $this->building_name = $building_name;
        $this->profile_image_url = $profile_image_url;
    }

    public function fullAddress(): string
    {
        return "{$this->postal_code} {$this->shipping_address} {$this->building_name}";
    }

    public function imageUrl(): string
    {
        return asset('storage/' . $this->profile_image_url);
    }
}
