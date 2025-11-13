<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public string $postal_code;
    public string $shipping_address;
    public string $building_name;

    public function __construct(string $postal_code, string $shipping_address, string $building_name)
    {
        $this->postal_code = $postal_code;
        $this->shipping_address = $shipping_address;
        $this->building_name = $building_name;
    }

    public function full()
    {
        return "{$this->postal_code} {$this->shipping_address} {$this->building_name}";
    }
}
