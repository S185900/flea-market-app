<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::pluck('id')->toArray();
        $items = Item::pluck('id')->toArray();

        $combinations = collect($users)
            ->crossJoin($items)
            ->shuffle()
            ->take(50); // 例えば50件だけ作る

        $combinations->each(function ($pair) {
            [$userId, $itemId] = $pair;
            Like::factory()->create([
                'user_id' => $userId,
                'item_id' => $itemId,
            ]);
        });
    }
}
