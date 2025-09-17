<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'commenter_id',
        'item_id',
        'comment',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    public function commenter() {
        return $this->belongsTo(User::class, 'commenter_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
