<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyList extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'item_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function favoriteItems($userId, ?string $title = null)
    {
        $query = Item::with(['images', 'transaction'])
            ->whereHas('likes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId); // 自分の出品は除外

        if (!empty($title)) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        return $query->get();
    }

}
