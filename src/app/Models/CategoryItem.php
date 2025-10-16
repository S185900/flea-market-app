<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryItem extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'category_id'];

    public $timestamps = false;

    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_items');
    }
}
